<?php
/**
* Действие для добавления ряда
*/

class OrgaddzlineAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$headid = FilterInput::add(new IntFilter("headid", true, "ID закупки"));
		$prodid = FilterInput::add(new IntFilter("prodId", false, "ID товара"));
		$wholePrice = FilterInput::add(new StringFilter("wholePrice", true, "Оптовая цена"));
		$oldWholePrice = FilterInput::add(new StringFilter("oldWholePrice", false, "Старая цена"));
		$name = FilterInput::add(new StringFilter("name", false, "Название товара"));
		$artNumber = FilterInput::add(new StringFilter("artNumber", false, "Артикул"));
		$minValue = FilterInput::add(new IntFilter("minValue", false, "Кол-во в коробе"));
		$minName = FilterInput::add(new StringFilter("minName", false, "Ед.измер. товара"));
		$sizes = FilterInput::add(new StringFilter("sizes", false, "Размеры"));
		$isGrow = FilterInput::add(new StringFilter("isGrow", false, "Флаг роста ряда"));
		$shouldClose = FilterInput::add(new StringFilter("shouldClose", false, "Флаг закрытия при наполнении"));

		// при редактировании
		$id = FilterInput::add(new IntFilter("id", false, "ID ряда"));
		$rowtype = FilterInput::add(new IntFilter("rowtype", false, "Тип ряда"));
		$mode = FilterInput::add(new StringFilter("mode", false, "Режим"));
		$description = Request::getVar("description");

		if (!$oldWholePrice || $oldWholePrice == '')
			$oldWholePrice = 0;

		if (!$wholePrice || $wholePrice == '')
			$wholePrice = 0;

		$oldWholePrice = round(floatval($oldWholePrice), 2);
		$wholePrice = round(floatval($wholePrice), 2);

		if ($oldWholePrice < 0)
			FilterInput::addMessage("Не верно указана старая цена");

		if ($wholePrice <= 0)
			FilterInput::addMessage("Не верно указана цена");

		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		// товар
		$prodObj = null;
		$isProdOk = true;
		$pm = new ProductManager();
		if ($prodid)
		{
			$prodObj = $pm->getById($prodid);
			if (!$prodObj)
			{
				FilterInput::addMessage("Не найден товар");
				$isProdOk = false;
			}
			else
			{
				if ($orgId != $prodObj->orgId || $this->ownerSiteId != $prodObj->ownerSiteId || $this->ownerOrgId != $prodObj->ownerOrgId)
				{
					FilterInput::addMessage("Нет прав на редактирование товара");
					$isProdOk = false;
				}
			}
		}

		// ряд
		$zlineObj = null;
		$isZlOk = true;
		$zlm = new ZakupkaLineManager();
		if ($id)
		{
			$zlineObj = $zlm->getById($id);
			if (!$zlineObj)
			{
				FilterInput::addMessage("Не найден ряд");
				$isZlOk = false;
			}
			else
			{
				if ($orgId != $zlineObj->orgId || $this->ownerSiteId != $zlineObj->ownerSiteId || $this->ownerOrgId != $zlineObj->ownerOrgId)
				{
					FilterInput::addMessage("Нет прав на редактирование ряда");
					$isZlOk = false;
				}
			}
		}

		// закупка
		$zhm = new ZakupkaHeaderManager();
		$zheadObj = $zhm->getById($headid);
		if (!$zheadObj)
			FilterInput::addMessage("Не найдена закупка");
		else
		{
			if ($orgId != $zheadObj->orgId || $this->ownerSiteId != $zheadObj->ownerSiteId || $this->ownerOrgId != $zheadObj->ownerOrgId)
				FilterInput::addMessage("Нет прав на измение закупки");
		}

		// анализ введенных данных
		if (mb_strlen($description) > 10000000)
			FilterInput::addMessage("Слишком большой текст");

		if (!FilterInput::isValid())
		{
			FormRestore::add("opt-zakupka-line-add");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// здесь уже можно создать новый товар в базе
		if (!$prodid && $isProdOk)
		{
			$prodObj = new Product();
			$prodObj->orgId = $orgId;
			$prodObj->status = Product::STATUS_ENABLED;
			$prodObj->dateCreate = time();
			$prodObj->dateUpdate = time();
			$prodObj->name = $name;
			$prodObj->artNumber = $artNumber;
			$prodObj->description = $description;
			$prodObj->ownerSiteId = $this->ownerSiteId;
			$prodObj->ownerOrgId = $this->ownerOrgId;
			$prodObj = $pm->save($prodObj);

		}

		// либо отредактировать товар
		if ($prodid && $prodObj && $isProdOk)
		{
			$prodObj->dateUpdate = time();
			$prodObj->name = $name;
			$prodObj->artNumber = $artNumber;
			$prodObj->description = $description;
			$prodObj = $pm->save($prodObj);

		}

		$rezSizes = array();
		if (!$rowtype)
		{
			// подготовка sizes (сериализация)
			$sizes = str_replace(',,', ',', $sizes);
			$sizes = str_replace(',,', ',', $sizes);
			$sizes = str_replace(',,', ',', $sizes);

			$sizesArray = explode(',', $sizes);
			$cntInd = 0;
			if (count($sizesArray))
			{
				foreach ($sizesArray AS $oneSize) {

					if (trim($oneSize) && trim($oneSize) != '')
						$rezSizes[$cntInd] = trim($oneSize);

					$cntInd++;
				}
			}
		}

		// добавляется новый ряд
		if (!$id)
		{
			$zlineObj = new ZakupkaLine();
			$zlineObj->headId = $headid;
			$zlineObj->ownerSiteId = $this->ownerSiteId;
			$zlineObj->ownerOrgId = $this->ownerOrgId;
		}

		if ($prodObj)
			$zlineObj->productId = $prodObj->id;
		elseif ($prodid)
			$zlineObj->productId = $prodid;

		// при изменении цены нужно изменить и все заказы (пересчитать)
		// поставим такую задачу в очередь
		if ($id && $zlineObj && $zlineObj->wholePrice != $wholePrice) {
			$qm = new QueueMysqlManager();
			$qm->savePlaceTask("changezlineprice", $orgId, $actor->nickName, $zheadObj->id, $zheadObj->name, null, serialize(array("zlId" => $zlineObj->id, "prodName" => $name, "oldPrice" => $zlineObj->wholePrice, "newPrice" => $wholePrice)), time());
		}

		// новая цена товара
		$zlineObj->wholePrice = $wholePrice;

		// старая цена - чисто для дизайна, ставится "от фонаря"
		$zlineObj->oldWholePrice = $oldWholePrice;

		if (!$id) {
			$zlineObj->orgId = $orgId;
			$zlineObj->status = ZakupkaLine::STATUS_ACTIVE;
			$zlineObj->dateCreate = time();
		}

		// параметры ряда
		if (!$rowtype)
		{
			// ряд с размерами редактировать нельзя
			if (!$zlineObj->sizes) {
				if (!count($rezSizes))
					$rezSizes[] = '-';
			}

			if (count($rezSizes)) {
				$zlineObj->sizes = serialize($rezSizes);
				$zlineObj->minValue = null;
				$zlineObj->minName = null;
			}

			if ($isGrow == 'on')
				$zlineObj->isGrow = 1;
			else
				$zlineObj->isGrow = 0;

			if ($shouldClose == 'on')
				$zlineObj->shouldClose = 1;
			else
				$zlineObj->shouldClose = 0;

		}
		else
		{
			$zlineObj->sizes = null;
			$zlineObj->minValue = $minValue;
			$zlineObj->minName = $minName;
		}


		$zlineObj->dateUpdate = time();
		$zlineObj = $zlm->save($zlineObj);

		// всё готово
		Enviropment::redirect("orgviewzakupka/headid/".$headid."#r".$zlineObj->id, "Ряд записан");

	}

}
