<?php
/**
* Контрол покажет закупку
*/
class ViewCollectionControl extends IndexControl
{
	public $pageTitle = "Закупка";

	public function render()
	{
		$this->controlName = "ViewCollection";

		$shouldLogin = false;
		if (!$this->actor && SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'zakseeonlymembers') == 'on')
			$shouldLogin = true;

		$headid = Request::getInt("id");
		$actor = $this->actor;
		$this->addData("actor", $actor);

		// режим просмотра рядов
		$mode = Request::getVar('mode');
		if (in_array($mode, array('compact', 'short')))
			$this->addData("mode", $mode);
		else
			$mode = null;

		$ts = time();
		$this->addData("ts", $ts);

		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$curZHM = $zhm->getById($headid);
		if (!$curZHM)
			Enviropment::redirectBack("Закупка не найдена");

		if ($this->ownerSiteId != $curZHM->ownerSiteId || $this->ownerOrgId != $curZHM->ownerOrgId)
			Enviropment::redirectBack("Закупка не найдена");

		if ($actor)
		{
			if ($actor->isOrg && $curZHM->orgId == $actor->id)
				Enviropment::redirect("orgviewzakupka/headid/".$headid);
		}

		if ($curZHM->status != ZakupkaHeader::STATUS_ACTIVE && $curZHM->status != ZakupkaHeader::STATUS_ADDMORE && SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'shownabraniyezakupki') == 'on')
			$shouldLogin = false;

		if ($shouldLogin)
			Enviropment::redirect("userlogin", "Необходимо авторизоваться");

		// это действительно закупка данного орга
		$this->addData("headid", $headid);
		$this->addData("headobj", $curZHM);
		$this->addData("headname", $curZHM->name);
		$this->addData("headstatus", $curZHM->status);
		$this->addData("headstatusname", ZakupkaHeader::getStatusDesc($curZHM->status));

		$this->pageTitle .= " ".mb_strtolower($curZHM->name, 'utf8')." (".SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city").")";

		$orgIds = array();

		$zlm = new ZakupkaLineManager();
		$pm = new ProductManager();

		// поднимаем ряды этой закупки
		$lineList = $zlm->getByHeadId($headid, true);

		// сбор id товаров
		if (count($lineList))
		{
			$lineListConverted = array();
			$prodIds = array();
			foreach ($lineList AS $oneLine)
			{
				if ($oneLine->productId)
					$prodIds[] = $oneLine->productId;

				$orgIds[] = $oneLine->orgId;

				// ключ сортировки
				// поможет организовать любую сортировку рядов
				// например по цене
				$sortKey = $oneLine->id;

				$buildRow = "";
				if ($oneLine->sizes)
				{
					// транслируем ряды сразу в список <ul><li>
					// со вставкой ников куда следует
					$getSizesArray = @unserialize($oneLine->sizes);
					// размещенные заказы участников
					$getPlacedArray = @unserialize($oneLine->sizesChoosen);
					// строка с готовым списком <ul></li>
					if (count($getSizesArray) && !$mode)
					{
						asort($getSizesArray);
						$arrayUnique = array_unique($getSizesArray);

						// построение линий ряда
						for ($i = 1; $i <= $oneLine->rowNumbers; $i++)
						{
							if (count($arrayUnique) == 1)
								$buildRow = $buildRow.'<div class="zlinedatahorizontal">';
							else
								$buildRow = $buildRow.'<div class="zlinedata">';

							$prevSize = null;
							$buildCounter = 0;
							$shorterLineCounter = 0;
							foreach ($getSizesArray AS $oneSizeKey => $oneSizeVal)
							{
								if (count($arrayUnique) == 1) {
									$shorterLineCounter++;
									if ($shorterLineCounter > 10) {
										$shorterLineCounter = 1;
										$prevSize = 'путьксвету4';
									}
								}

								if ($oneSizeVal != $prevSize && $prevSize != null)
									$buildRow = $buildRow."</ul>";

								if ($oneSizeVal != $prevSize)
								{
									if ($buildCounter % 2 == 0)
										$buildRow = $buildRow."<ul>";
									else
										$buildRow = $buildRow."<ul style='background-color: #efefef;'>";

									// первую ячейку резервируем под обозначение размера
									// далее будут только плюсы
									if ($oneSizeVal != '-' && $oneSizeVal != '+') {
                                        $oneSizeValShort = Utility::limitString($oneSizeVal, 30);
                                        if ($oneSizeVal != $oneSizeValShort) {
                                            $oneSizeValShort .= '..';
                                        }
                                        $buildRow = $buildRow . "<li><b>{$oneSizeValShort}</b></li>";
                                    }

									$buildCounter++;
								}

								if (isset($getPlacedArray[$oneSizeKey.'_'.$i]) && is_array($getPlacedArray[$oneSizeKey.'_'.$i]))
								{
									$userData = $getPlacedArray[$oneSizeKey.'_'.$i];
									$ordBtn = "<a target='_blank' href='/index.php?show=viewuser&id=".$userData['id']."'>".$userData['nick']."</a>";
								}
								else
								{
									if (in_array($curZHM->status, array(ZakupkaHeader::STATUS_ACTIVE, ZakupkaHeader::STATUS_ADDMORE)))
										$ordBtn = "<a href='/index.php?do=userplaceorder&lid={$oneLine->id}&rp={$oneSizeKey}&num={$i}'><span style='color: green;'><span style='color: #333;'>[</span><b>+</b><span style='color: #333;'>]</span></span></a>";
									else
										$ordBtn = "<span style='color: green;'><span style='color: #333;'>[</span><b>+</b><span style='color: #333;'>]</span></span>";
								}

								$buildRow = $buildRow."<li>{$ordBtn}</li>";

								$prevSize = $oneSizeVal;
							}

							$buildRow = $buildRow."</ul>";
							$buildRow = $buildRow."</div>";
						}

					}

					// для коротких списков
					if (count($getSizesArray) && $mode)
					{
						asort($getSizesArray);
						$arrayUnique = array_unique($getSizesArray);
						if (count($arrayUnique))
						{
							$buildRow = $buildRow.'<select id="zlshortid-'.$oneLine->id.'-sel" class="zlshortid">';
							$isOne = false;
							foreach ($arrayUnique AS $oneSize)
							{
								$oneSize = trim($oneSize);
								if ($oneSize && $oneSize != '' && $oneSize != '-' && $oneSize != '+')
								{
									$isOne = true;
									$buildRow = $buildRow . '<option value="' . $oneSize . '">' . $oneSize . '</option>';
								}
							}

							$buildRow = $buildRow.'</select> ';
							if (!$isOne)
								$buildRow = "";

							$buildRow = $buildRow.'<button id="zlshortid-'.$oneLine->id.'" class="zlshortpush">В корзину</button>';
						}

					}

				}

				// в выбором по кол-ву
				if ($oneLine->minValue > 0)
				{
					$buildRow = $buildRow.'<select id="zlchkolvoid-'.$oneLine->id.'-sel" class="zlselselit">';

					for ($i = 1; $i <= min(5, $oneLine->minValue); $i++)
						$buildRow = $buildRow.'<option value='.$i.'>'.$i.'</option>';

					$buildRow = $buildRow.'</select> '.$oneLine->minName.' <button id="zlchkolvoid-'.$oneLine->id.'" class="zlselpush">В корзину</button>';

					if (!$mode)
						$buildRow = $buildRow.'<br/><span style="font-size: 12px; font-style: italic;">(в коробке по '.$oneLine->minValue.' '.$oneLine->minName.')</span>';
				}

				$oneLine->buildRow = $buildRow;

				$lineListConverted[$sortKey] = $oneLine;
			}

			ksort($lineListConverted);
			$this->addData("lineList", $lineListConverted);

			if (count($prodIds))
			{
				// поднимаем товары выбранных рядов
				$prodList = $pm->getByIds($prodIds);
				if (count($prodList))
				{
					$prodListConverted = array();
					foreach ($prodList AS $oneProduct)
					{
						// ссылку на первую картинку надо вынуть в отдельное поле
						$oneProduct->oneRowPic = null;
						$hasPhoto = preg_match("(\[img\](.+?)\[/img\])is", $oneProduct->description, $matches);
						if (isset($matches[1]))
						{
							$canGetFile = false;
							if (strpos(strtolower($matches[1]), '.png') !== false || strpos(strtolower($matches[1]), '.bmp') !== false || strpos(strtolower($matches[1]), '.gif') !== false || strpos(strtolower($matches[1]), '.jpg') !== false || strpos(strtolower($matches[1]), '.jpeg') !== false)
								$canGetFile = true;

							if ($canGetFile)
								$oneProduct->oneRowPic = $matches[1];
						}

						// сделаем описание без html тегов
						$oneProduct->shortDesc = null;
						if ($oneProduct->description && $oneProduct->description != '')
							$oneProduct->shortDesc = strip_tags(str_replace(array('[img][/img]', '<br/>', '<br>', '<br />','<BR/>', '<BR>', '<BR />'),' ',Utility::bbcode2html($oneProduct->description)));

						$prodListConverted[$oneProduct->id] = $oneProduct;
					}

					$this->addData("prodList", $prodListConverted);
				}

			}

		}

		$um = new UserManager();
		$orgList = $um->getByIds($orgIds);
		$this->addData("orgList", $orgList);

		// каменты
		$cocomm = new CoCommentManager();
		// заголовки
		$commlist = $cocomm->getModByHeadId($headid, CoComment::COMMENT_ZAKUPKA);
		// головы каментов
		$this->addData("commlist", $commlist);
		// пройдем циклом заголовки и получим цепочки ответов к каждому
		$subcomments = array();
		// все сабкомменты
		$allSubcomments = $cocomm->getAllSubComments($headid, CoComment::COMMENT_ZAKUPKA);
		if (count($allSubcomments) > 0)
		{
			foreach ($allSubcomments as $comheaditem)
				$subcomments[$comheaditem['rootId']][] = $comheaditem;

			// субкаменты
			$this->addData("subcomments", $subcomments);
		}

	}

}
