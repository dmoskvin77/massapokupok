<?php
/**
* Добавление товара в корзину через форму заказа без рядов
*
*/
class AddFormToBasketAction extends BaseAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;
		$kolvo = Request::getInt("kolvo");
		// не указано кол-во
		if (intval($kolvo) == 0)
			$kolvo = 1;

		$headid = FilterInput::add(new IntFilter("headid", true, "ID закупки"));
		$osid = FilterInput::add(new IntFilter("osid", false, "ID заказа"));
		$orderArt = FilterInput::add(new StringFilter("orderArt", false, "Артикул товара"));
		$orderName = FilterInput::add(new StringFilter("orderName", true, "Наименование товара"));
		$orderPrice = FilterInput::add(new StringFilter("orderPrice", true, "Оптовая цена"));
		$orderAmount = FilterInput::add(new StringFilter("hiddenOrderAmount", true, "Итоговая цена с наценкой"));

		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		// поищем закупку
		$zhm = new ZakupkaHeaderManager();
		$curZH = $zhm->getById($headid);
		if (!$curZH)
			Enviropment::redirectBack("Закупка не найдена");

		if ($this->ownerSiteId != $curZH->ownerSiteId || $this->ownerOrgId != $curZH->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		if ($osid > 0)
		{
			$osm = new OrderListManager();
			$curOS = $osm->getById($osid);
			if (!$curOS)
				Enviropment::redirectBack("Строка заказа не найдена");

			if ($this->ownerSiteId != $curOS->ownerSiteId || $this->ownerOrgId != $curOS->ownerOrgId)
				Enviropment::redirectBack("Нет прав на выполнение данного действия");

			// сумму по этой строке просто вычтем из общей суммы юника
			$uniqueId = $curOS->uniqueId;

			$uom = new OrderHeadManager();
			$curUnique = $uom->getById($uniqueId);
			if (!$curUnique)
				Enviropment::redirectBack("Заказ не найден");

			if ($curZH->status == ZakupkaHeader::STATUS_CLOSED || $curZH->status == ZakupkaHeader::STATUS_SEND || $curZH->status == ZakupkaHeader::STATUS_DELIVERED)
				Enviropment::redirectBack("Статус закупки не позволяет изменять заказ");

			// вычленим цвета и размеры
			$zlm = new ZakupkaLineManager();
			$curZLine = null;
			if ($curOS->zlId != null)
				$curZLine = $zlm->getById($curOS->zlId);

			if ($curZLine)
			{
				// уберем из строки закупки размеры и цвета рядов
				if ($curZLine->sizesChoosen && $curOS->sizesChoosen)
				{
					$newsizes = str_replace($curOS->sizesChoosen, "", $curZLine->sizesChoosen);
					$curZLine->sizesChoosen = $newsizes;
					$zlm->save($curZLine);
				}

				if ($curZLine->colorsChoosen && $curOS->colorsChoosen)
				{
					$newcolors = str_replace($curOS->colorsChoosen, "", $curZLine->colorsChoosen);
					$curZLine->colorsChoosen = $newcolors;
					$zlm->save($curZLine);
				}
			}

			// уменьшаем сумму общего заказа
			if ($curUnique->orderAmount - $curOS->productAmount <= 0)
			{
				// удаляем строку
				$osm->remove($osid);
				// удаляем голову заказа
				$uom->remove($uniqueId);
			}
			else
			{
				$curUnique->orderValue = $curUnique->orderValue - $curOS->productValue;
				$curUnique->orderAmount = $curUnique->orderAmount - $curOS->productAmount;
				$curUnique->wholescaleAmount = $curUnique->wholescaleAmount - $curOS->wholescaleAmount;
				$uom->save($curUnique);
				// удаляем строку
				$osm->remove($osid);
			}

		}

	}

}
