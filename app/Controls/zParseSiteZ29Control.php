<?php
/**
 * парсинг каталога сайта
 *
 * http://simplehtmldom.sourceforge.net/manual.htm
 *
 */
class zParseSiteZ29Control extends AuthorizedOrgControl
{
	public $pageTitle = "Парсинг";
	public $controlName = "zParseSiteZ29";
	public $urlHostName = "z29.ru";

	public function render()
	{
		require_once APPLICATION_DIR . "/Lib/simple_html_dom.php";

		$prodLinks = null;
		$prodLinksAdded = null;
		$catLinks = null;

		$actor = $this->actor;

		$mode = Request::getVar("mode");
		if (!in_array($mode, array('', 'start', 'parseprods', 'done', 'movetozak', 'addtov')))
			$mode = '';

		$iteration = Request::getVar("iteration");

		$zpm = new zParseWorkManager();

		// первым шагом собираем линки на товары и записываем их в базу
		// выдиралка ссылок
		if ($mode == 'start')
		{
			$lnkCatIsAdded = false;

			$url = "http://".$this->urlHostName."/category/vse-tovary-optom/";

			// удаляем все предыдущие данные по парсингу из таблицы zParseWork
			if ($iteration == 0)
				$zpm->cleanAll($this->ownerSiteId, $this->ownerOrgId, $this->controlName);

			// следующая итерация
			if ($iteration > 0)
			{
				// получим ссылку на сл. страницу со ссылками на товары
				$catLinks = $zpm->getByCatLnksByGuidCtrl($this->ownerSiteId, $this->ownerOrgId, $this->controlName);
				$this->addData("catcount", count($catLinks));

				if (count($catLinks))
				{
					foreach ($catLinks AS $oneCatLink)
					{
						$url = $oneCatLink->catLink;

						$oneCatLink->status = zParseWork::STATUS_PROCESSED;
						$zpm->save($oneCatLink);

						break;
					}
				}
			}

			$html = file_get_html($url);

			// ссылки на страницы со списком товаров
			$ret = $html->find('.no_underline');
			if (count($ret))
			{
				foreach ($ret AS $element)
				{
					if (strpos($element->href, '/category/vse-tovary-optom') !== false)
					{
						if (!$zpm->getByCatLnk($this->ownerSiteId, $this->ownerOrgId, "http://" . $this->urlHostName . $element->href, $this->controlName))
						{
							$zpwObj = new zParseWork();
							$zpwObj->control = $this->controlName;
							$zpwObj->mode = $mode;
							$zpwObj->iteration = $iteration;
							$zpwObj->catLink = "http://" . $this->urlHostName . $element->href;
							$zpwObj->status = zParseWork::STATUS_NEW;
							$zpwObj->ownerSiteId = $this->ownerSiteId;
							$zpwObj->ownerOrgId = $this->ownerOrgId;
							$zpm->save($zpwObj);

							$lnkCatIsAdded = true;

						}

					}
				}
			}


			$ret = $html->find('.sr-hover');
			if (count($ret))
			{
				foreach ($ret AS $element)
				{
					if (strpos($element->href, '/product/') !== false)
					{
						if (!$zpm->getByProdLnk($this->ownerSiteId, $this->ownerOrgId, "http://" . $this->urlHostName . $element->href, $this->controlName))
						{
							$zpwObj = new zParseWork();
							$zpwObj->control = $this->controlName;
							$zpwObj->mode = $mode;
							$zpwObj->iteration = $iteration;
							$zpwObj->prodLink = "http://" . $this->urlHostName . $element->href;
							$zpwObj->status = zParseWork::STATUS_NEW;
							$zpwObj->ownerSiteId = $this->ownerSiteId;
							$zpwObj->ownerOrgId = $this->ownerOrgId;
							$zpm->save($zpwObj);
						}
					}
				}
			}


			// если не найдено ссылок на сл. страницы со списками товаров,
			// то поменяем режим работы
			if (!$lnkCatIsAdded && !count($zpm->getByCatLnksByGuidCtrl($this->ownerSiteId, $this->ownerOrgId, $this->controlName)))
			{
				$mode = 'parseprods';
				// начало (нулевая итерация)
				$iteration = 0;
			}
			else
			{
				// накинем итерацию
				$iteration++;
			}

		}


		// парсинг товаров
		// все ссылки на страницы товаров уже есть в БД
		if ($mode == 'parseprods')
		{
			// получим ссылки на товары и будем парсить их
			$prodLinks = $zpm->getByProdLnksByGuidCtrl($this->ownerSiteId, $this->ownerOrgId, $this->controlName);
			$prodLinksAdded = $zpm->getByProdLnksByGuidCtrl($this->ownerSiteId, $this->ownerOrgId, $this->controlName, zParseWork::STATUS_PROCESSED);
			$this->addData("prodcount", count($prodLinksAdded));

			if (count($prodLinks))
			{
				foreach ($prodLinks AS $oneProdLink)
				{
					$url = $oneProdLink->prodLink;

					$oneProdLink->status = zParseWork::STATUS_PROCESSED;

					// получим данные со страницы товара для парсинга
					$html = @file_get_html($url);

					if (!$html) {
						$zpm->remove($oneProdLink->id);
						continue;
					}

					// наименование
					$prodName = '';
					$ret = $html->find('.cpt_product_name', 0);
					if ($ret)
						$prodName = $ret->plaintext;

					$picUrl1 = '';
					$ret = $html->find('#img-current_picture', 0);
					if ($ret)
						$picUrl1 = $ret->src;

					// цена
					$price = '';
					$ret = $html->find('.totalPrice', 0);
					if ($ret)
						$price = $ret->plaintext;

					$price = str_replace(' р.', '', $price);
					$price = str_replace(',', '.', $price);
					$price = round($price, 2);

					// cpt_product_description
					$desc = '';
					$ret = $html->find('.cpt_product_description div', 0);
					if ($ret)
						$desc = Utility::html2bbcode($ret->outertext);

					// MsoNormal
					$videoLnk = '';
					$ret = $html->find('.MsoNormal iframe', 0);
					if ($ret)
						$videoLnk = $ret->src;

					$videoLnk = str_replace('//www.youtube-nocookie.com/embed/', '', $videoLnk);
					$videoLnk = str_replace('?rel=0', '', $videoLnk);

					$oneProdLink->params = serialize(array('prodName' => base64_encode($prodName), 'picUrl1' => base64_encode($picUrl1), 'price' => base64_encode($price), 'desc' => base64_encode($desc), 'videoLnk' => base64_encode($videoLnk)));
					$zpm->save($oneProdLink);

					$iteration++;

					break;
				}

			}
			else
			{
				// всё спарсили
				// выводим приглашение сохранить всё в файл для экселя
				// (лучше сделать xml файл)

				$mode = 'done';
				$iteration = 0;

			}

		}

		// орг зашёл на страницу из информации о поставщике
		if ($mode == '')
		{
			// узнаем кол-во спарсенных товаров
			$prodLinksAdded = $zpm->getByProdLnksByGuidCtrl($this->ownerSiteId, $this->ownerOrgId, $this->controlName, zParseWork::STATUS_PROCESSED);
			$this->addData("prodcount", count($prodLinksAdded));

		}

		// орг выбраз закупку для загрузки в неё рядов
		if ($mode == 'movetozak')
		{
			// узнаем кол-во спарсенных товаров
			$prodLinksAdded = $zpm->getByProdLnksByGuidCtrl($this->ownerSiteId, $this->ownerOrgId, $this->controlName, zParseWork::STATUS_PROCESSED);

			if (count($prodLinksAdded))
			{
				$prodLinksNew = array();
				foreach ($prodLinksAdded AS $oneprodlink)
				{
					if ($oneprodlink->params)
					{
						$unserializedData = unserialize($oneprodlink->params);
						if (count($unserializedData))
							$prodLinksNew[] = array("id" => $oneprodlink->id, "name" => base64_decode($unserializedData['prodName']), "price" => base64_decode($unserializedData['price']));
					}
				}

				$this->addData("prodLinksNew", $prodLinksNew);
			}

			$headid = Request::getInt("headid");
			$zhm = new ZakupkaHeaderManager();
			$zhObj = $zhm->getById($headid);
			if ($zhObj && $actor->id == $zhObj->orgId)
				$this->addData("zhObj", $zhObj);

		}


		// загрузка товаров в ряды
		if ($mode == 'addtov')
		{
			$tovIds = Request::getArray("prods");
			$parsedTovs = $zpm->getByIds($tovIds);
			$countLoaded = 0;
			if (count($parsedTovs))
			{
				$zlm = new ZakupkaLineManager();
				$pm = new ProductManager();

				// затаривание в закупку
				$headid = Request::getInt("headid");
				$zhm = new ZakupkaHeaderManager();
				$zhObj = $zhm->getById($headid);
				if ($zhObj && $actor->id == $zhObj->orgId)
				{
					foreach ($parsedTovs AS $oneparseditem)
					{
						$params = @unserialize($oneparseditem->params);
						if (!count($params))
							continue;

						if (!isset($params['prodName']) || !isset($params['picUrl1']) || !isset($params['price']) || !isset($params['desc']) || !isset($params['videoLnk']))
							continue;

						$description = base64_decode($params['picUrl1']);

						$thmbPic = str_replace('.jpg', '_thm.jpg', $description);
						$thmbPic = str_replace('.JPG', '_thm.JPG', $thmbPic);

						$description = "[url=http://{$this->urlHostName}{$description}][img]http://{$this->urlHostName}".$thmbPic."[/img][/url]
";

						$description .= base64_decode($params['desc'])."
";

						// ну и видео
						if (base64_decode($params['videoLnk']) && base64_decode($params['videoLnk']) != '')
							$description .= "[video]".base64_decode($params['videoLnk'])."[/video]";

							// проверим есть ли в базе товар по такой ссылке, как пришла с парсингом
						// если нет, создадим товар, переменную на объект приметим
						$prodObj = $pm->getByParseLink($this->ownerSiteId, $this->ownerOrgId, $oneparseditem->prodLink, $zhObj->orgId);
						if (!$prodObj)
						{
							$prodObj = new Product();
							$prodObj->orgId = $zhObj->orgId;
							$prodObj->dateCreate = time();
							$prodObj->prodLink = $oneparseditem->prodLink;
							$prodObj->ownerSiteId = $this->ownerSiteId;
							$prodObj->ownerOrgId = $this->ownerOrgId;
						}

						$prodObj->status = Product::STATUS_ENABLED;
						$prodObj->dateUpdate = time();
						$prodObj->name = base64_decode($params['prodName']);
						$prodObj->description = $description;
						$prodObj = $pm->save($prodObj);


						// проверим есть ли в закупке ряд по такой ссылке, как пришла с парсингом
						// если нет, создадим ряд, наполним его параметрами с парсинга, увеличим счетчик
						$zlObj = $zlm->getByParseLink($oneparseditem->prodLink, $zhObj->orgId, $headid);
						if (!$zlObj)
						{
							$countLoaded++;
							$zlObj = new ZakupkaLine();
							$zlObj->orgId = $zhObj->orgId;
							$zlObj->headId = $headid;
							$zlObj->status = ZakupkaLine::STATUS_ACTIVE;
							$zlObj->prodLink = $oneparseditem->prodLink;
							$zlObj->dateCreate = time();
							$zlObj->ownerSiteId = $this->ownerSiteId;
							$zlObj->ownerOrgId = $this->ownerOrgId;
						}

						$zlObj->productId = $prodObj->id;
						$zlObj->wholePrice = round(floatval(str_replace(',', '.', base64_decode($params['price']))), 2);

						$zlObj->sizes = serialize(array('-','-','-','-','-'));
						$zlObj->isGrow = 1;
						$zlObj->shouldClose = 0;
						$zlObj->dateUpdate = time();

						$zlObj = $zlm->save($zlObj);

					}

				}

			}

			$this->addData("countLoaded", $countLoaded);

		}

		$this->addData("mode", $mode);
		$this->addData("iteration", $iteration);

		$this->addData("controlName", $this->controlName);
		$this->addData("urlHostName", $this->urlHostName);

		// получить список закупок на редактировании у актора
		if (count($prodLinksAdded))
		{
			$zhm = new ZakupkaHeaderManager();
			$zhlist = $zhm->getByOrgId($actor->id, ZakupkaHeader::STATUS_NEW);
			$this->addData("zhlist", $zhlist);
		}

	}

}
