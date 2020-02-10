<?php
/**
* Заливка товаров и рядов в закупку из данных csv файла, загруженных в базу
*
*/
class OrgimportzakupkaprocessAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		$sessHash = Enviropment::getBasketGUID();

		// выбранные типы для полей
        $field1 = Request::getVar('field1');
        $field2 = Request::getVar('field2');
        $field3 = Request::getVar('field3');
        $field4 = Request::getVar('field4');
        $field5 = Request::getVar('field5');
        $field6 = Request::getVar('field6');
        $field7 = Request::getVar('field7');
        $field8 = Request::getVar('field8');
        $field9 = Request::getVar('field9');

		$gotFields = array();

		if ($field1) $gotFields[1] = $field1; if ($field2) $gotFields[2] = $field2; if ($field3) $gotFields[3] = $field3;
		if ($field4) $gotFields[4] = $field4; if ($field5) $gotFields[5] = $field5; if ($field6) $gotFields[6] = $field6;
		if ($field7) $gotFields[7] = $field7; if ($field8) $gotFields[8] = $field8; if ($field9) $gotFields[9] = $field9;

		for ($i = 1; $i <= 9; $i++)
		{
			if (!isset($gotFields[$i]))
				$gotFields[$i] = 0;
		}

		// обязательно должны быть поля 1, 2, 3
		if (!in_array(1, $gotFields) || !in_array(2, $gotFields) || !in_array(3, $gotFields))
			Enviropment::redirectBack("Обязательно должны быть выбраны поля типов: наименование товара; артикул; цена!");

		// какая выбрана кодировка
		$setEncodingType = Request::getInt('codetype');

		// всё есть, переходим к записи данных
		$pm = new ProductManager();
		$zlm = new ZakupkaLineManager();

		// получить все строки csv файла
		$allStrings = Csvfile::getAllStrings($sessHash, $actor->id, $headid, $this->ownerSiteId, $this->ownerOrgId);
		if (!count($allStrings))
			Enviropment::redirect("orgimportzakupka/headid/".$headid, "Не обнаружены данные для загрузки, загрузите файл csv");

		$countAddedLines = 0;

		// транзакция, малоли что не так может пойти
		$pm->startTransaction();
		try
		{
			foreach ($allStrings AS $oneLine)
			{
				// готовим данные для товара
				$prodName = '';
				$prodArt = '';
				$prodDesc = '';

				// данные для ряда
				$rowPrice = 0;
				$rowSizesArray = array();

				for ($i = 1; $i <= 9; $i++)
				{
					$lineData = "field{$i}";
					$lineData = $oneLine[$lineData];

					if ($gotFields[$i] == 1)
						$prodName .= $this->setEncoding($lineData, $setEncodingType).' ';

					if ($gotFields[$i] == 2)
						$prodArt .= $this->setEncoding($lineData, $setEncodingType).' ';

					// цена
					if ($gotFields[$i] == 3)
					{
						$rowPrice = $this->setEncoding($lineData, $setEncodingType);
						$rowPrice = str_replace(',', '.', $rowPrice);
						$rowPrice = floatval($rowPrice);
						$rowPrice = round($rowPrice, 2);
					}

					// ссылка на картинку
					if ($gotFields[$i] == 4)
					{
						$prodDesc .= "[img]".$this->setEncoding($lineData, $setEncodingType)."[/img]
";
					}

					// описание
					if ($gotFields[$i] == 5)
						$prodDesc .= Utility::html2bbcode($this->setEncoding($lineData, $setEncodingType))."
";

					// ряд (ячейки)
					if ($gotFields[$i] == 6)
					{
						$gotSizesString = $this->setEncoding($lineData, $setEncodingType);
						$prepareSizes = array();
						if (strpos($gotSizesString, ',') !== false)
						{
							$prepareSizes = explode(',', $gotSizesString);
							if (count($prepareSizes))
							{
								foreach ($prepareSizes AS $onePreparedSize)
									$rowSizesArray[] = trim($onePreparedSize);
							}
						}
						else
							$rowSizesArray[] = $gotSizesString;
					}

				}

				// базовый ряд если не указано в файле
				if (!count($rowSizesArray))
					$rowSizesArray = array('-', '-', '-', '-', '-');

				// echo "rowPrice: {$rowPrice}"; exit;

				// сама непосредственно запись в базу элементов
				if ($rowPrice > 0)
				{
                    if (strpos($prodArt, ',00') !== false) {
                        $prodArt = trim(round($prodArt));
                    }

					// товар
					$prodObj = new Product();
					$prodObj->orgId = $actor->id;
					$prodObj->name = $prodName;
					$prodObj->artNumber = $prodArt;
					$prodObj->status = Product::STATUS_ENABLED;
					$prodObj->dateCreate = time();
					$prodObj->dateUpdate = time();
					$prodObj->description = $prodDesc;
					$prodObj->ownerSiteId = $this->ownerSiteId;
					$prodObj->ownerOrgId = $this->ownerOrgId;
					$prodObj = $pm->save($prodObj);

					// теперь ряд
					$zlObj = new ZakupkaLine();
					$zlObj->headId = $headid;
					$zlObj->productId = $prodObj->id;
					$zlObj->orgId = $actor->id;
					$zlObj->wholePrice = $rowPrice;
					$zlObj->finalPrice = $rowPrice + $rowPrice /100 * $zakObj->orgRate;
					$zlObj->dateCreate = time();
					$zlObj->dateUpdate = time();
					$zlObj->sizes = serialize($rowSizesArray);
					$zlObj->status = ZakupkaLine::STATUS_ACTIVE;
					$zlObj->ownerSiteId = $this->ownerSiteId;
					$zlObj->ownerOrgId = $this->ownerOrgId;
					$zlObj = $zlm->save($zlObj);

					$countAddedLines++;
				}

			}

		}
		catch (Exception $e)
		{
			$pm->rollbackTransaction();
			Logger::error($e->getMessage());
			$this->redirectBackCover("Не удалось сохранить данные. Попробуйте позднее");
		}

		$pm->commitTransaction();

		// всё готово
		// переходим в закупку (редирект)
		Enviropment::redirect("orgviewzakupka/headid/".$headid, "В закупку было загружено строк: {$countAddedLines}");

	}


	// установить нужную кодировку для поля
	private function setEncoding($value, $codetype)
	{
		// 0 - win-1251
		if ($codetype)
			return trim(base64_decode($value));
		else
			return trim(iconv('windows-1251', 'utf-8', base64_decode($value)));
	}

}
