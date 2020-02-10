<?php

set_time_limit(300);

/**
 * парсинг каталога сайта
 *
 * http://www.fitosila.ru/catalog/
 *
 */
class zParseSiteFitosilaControl extends AuthorizedOrgControl
{
	public $pageTitle = "Парсинг";
	public $controlName = "zParseSiteFitosila";
	public $urlHostName = "fitosila.ru";

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

			$url = "http://".$this->urlHostName."/catalog/";

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

			// $html = file_get_html($url, false, null, -1, -1, true, true, 'windows-1251');
			$html = file_get_html($url);

			// ссылки на страницы со списком товаров
			$ret = $html->find('a');
			if (count($ret))
			{
				foreach ($ret AS $element)
				{
					if (strpos($element->href, '.html') === false && strpos($element->href, 'catalog') !== false)
					{
						if (!$zpm->getByCatLnk($this->ownerSiteId, $this->ownerOrgId, "http://".$this->urlHostName.$element->href, $this->controlName))
						{
							$zpwObj = new zParseWork();
							$zpwObj->control = $this->controlName;
							$zpwObj->mode = $mode;
							$zpwObj->iteration = $iteration;
							$zpwObj->catLink = "http://".$this->urlHostName.$element->href;
							$zpwObj->status = zParseWork::STATUS_NEW;
							$zpwObj->ownerSiteId = $this->ownerSiteId;
							$zpwObj->ownerOrgId = $this->ownerOrgId;
							$zpm->save($zpwObj);

							$lnkCatIsAdded = true;

						}
					}
				}
			}

			// собираем ссылки на товар
			$ret = $html->find('a');
			if (count($ret))
			{
				foreach ($ret AS $element)
				{
					if (strpos($element->href, '.html') !== false && strpos($element->href, 'catalog') !== false && strpos($element->href, '-') === false)
					{
						if (!$zpm->getByProdLnk($this->ownerSiteId, $this->ownerOrgId, "http://".$this->urlHostName.$element->href, $this->controlName))
						{
							$zpwObj = new zParseWork();
							$zpwObj->control = $this->controlName;
							$zpwObj->mode = $mode;
							$zpwObj->iteration = $iteration;
							$zpwObj->prodLink = "http://".$this->urlHostName.$element->href;
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
				foreach ($prodLinks AS $oneProdLink) {
					$url = $oneProdLink->prodLink;

					$oneProdLink->status = zParseWork::STATUS_PROCESSED;

					// получим данные со страницы товара для парсинга
					// $html = @file_get_html($url, false, null, -1, -1, true, true, 'windows-1251');
					$html = @file_get_html($url);

					if (!$html) {
						$zpm->remove($oneProdLink->id);
						continue;
					}

					// парсим сам товар теперь

					// наименование
					$prodName = '';
					$ret = $html->find('h1', 0);
					if ($ret) {
						$prodName = iconv('CP1251', 'UTF-8', $ret->plaintext);
					}

					// артикул
					$prodArt = '';
					$ret = $html->find('#item-desc', 0);
					if ($ret) {
						$ret2 = $ret->find('li', 0);
						if ($ret2)
							$prodArt = iconv('CP1251', 'UTF-8', $ret2->plaintext);
					}

					$prodArt = str_replace('Код: ', '', $prodArt);

					if ($prodArt == '')
						continue;

					$prodName .= ' '.$prodArt;
					$prodName = trim($prodName);

					// фотки товара
					$picUrl1_small = '';
					$ret = $html->find('.group', 0);
					if ($ret)
						$picUrl1_small = $ret->href;

					$picUrl1_big = '';
					$ret = $html->find('.bp', 0);
					if ($ret)
						$picUrl1_small = $ret->src;

					$picUrl2_small = '';
					$ret = $html->find('.group', 1);
					if ($ret)
						$picUrl2_small = $ret->href;

					$picUrl2_big = '';
					$ret = $html->find('.bp', 1);
					if ($ret)
						$picUrl2_big = $ret->src;

					// цена
					$price = 0;
					$ret = $html->find('#item-desc', 0);
					if ($ret) {
						$ret2 = $ret->find('li');
						if (count($ret2)) {
							$sci = 0;
							foreach ($ret2 AS $element) {
								$sci++;

								if (strpos(iconv('CP1251', 'UTF-8', $element->plaintext), 'оптовая') !== false)
									$price = iconv('CP1251', 'UTF-8', $element->plaintext);

								if (strpos($element->plaintext, 'оптовая') !== false)
									$price = $element->plaintext;

								if ($sci > 100)
									break;
							}
						}
					}

					$priceArray = explode(" ", $price);

					if (count($priceArray))
					{
						if (isset($priceArray[2])) {
							$price = $priceArray[2];
							$price = str_replace(',', '.', $price);
							$price = round($price, 2);
						}
					}

					// cpt_product_description
					$desc = '';
					$ret = $html->find('#accordion', 0);
					if ($ret) {
						$ret2 = $ret->find('div', 0);
						if ($ret2) {
							$ret3 = $ret2->find('p', 0);
							if ($ret3)
								$desc = iconv('CP1251', 'UTF-8', $ret3->plaintext);
						}
					}

					$desc = Utility::html2bbcode($desc);

					// записать
					$oneProdLink->params = serialize(array('prodName' => base64_encode($prodName), 'prodArt' => base64_encode($prodArt), 'picUrl1_small' => base64_encode($picUrl1_small), 'picUrl2_small' => base64_encode($picUrl2_small), 'picUrl1_big' => base64_encode($picUrl1_big), 'picUrl2_big' => base64_encode($picUrl2_big), 'price' => base64_encode($price), 'desc' => base64_encode($desc)));

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

						if (!isset($params['prodName']) || !isset($params['prodArt']) || !isset($params['price']))
							continue;

						$prodArt = base64_decode($params['prodArt']);

						$picUrl1_small = base64_decode($params['picUrl1_small']);
						$picUrl2_small = base64_decode($params['picUrl2_small']);
						$picUrl1_big = base64_decode($params['picUrl1_big']);
						$picUrl2_big = base64_decode($params['picUrl2_big']);

						$description = '';

						if ($picUrl1_small != '' && $picUrl1_big != '')
							$description .= "[url=http://{$this->urlHostName}/{$picUrl1_big}][img]http://{$this->urlHostName}/{$picUrl1_small}[/img][/url] ";

						if ($picUrl2_small != '' && $picUrl2_big != '')
							$description .= "[url=http://{$this->urlHostName}/{$picUrl2_big}][img]http://{$this->urlHostName}/{$picUrl2_small}[/img][/url]
";

						$description .= base64_decode($params['desc'])."
";

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
						$prodObj->artNumber = $prodArt;

						if ($description != '')
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
