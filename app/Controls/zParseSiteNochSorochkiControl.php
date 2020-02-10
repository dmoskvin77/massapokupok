<?php
/**
 * парсинг каталога сайта
 *
 * http://simplehtmldom.sourceforge.net/manual.htm
 *
 */
class zParseSiteNochSorochkiControl extends AuthorizedOrgControl
{
	public $pageTitle = "Парсинг";
	public $controlName = "zParseSiteNochSorochki";
	public $urlHostName = "noch-sorochki.ru";

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

			$url = "http://".$this->urlHostName."/katalog1.html";

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
			$ret = $html->find('a');
			if (count($ret))
			{
				foreach ($ret AS $element)
				{
					if (strpos($element->href, 'katalog') !== false)
					{
						if (!$zpm->getByCatLnk($this->ownerSiteId, $this->ownerOrgId, "http://".$this->urlHostName.'/'.$element->href, $this->controlName))
						{
							$zpwObj = new zParseWork();
							$zpwObj->control = $this->controlName;
							$zpwObj->mode = $mode;
							$zpwObj->iteration = $iteration;
							$zpwObj->catLink = "http://".$this->urlHostName.'/'.$element->href;
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
					if ((strpos($element->href, 'koft') !== false || strpos($element->href, 'tst') !== false || strpos($element->href, 'tbt') !== false || strpos($element->href, 'tbk') !== false || strpos($element->href, 'pbr') !== false || strpos($element->href, 'pdl') !== false || strpos($element->href, 'psh') !== false || strpos($element->href, 'komp') !== false || strpos($element->href, 'r') === 0 || strpos($element->href, 't') === 0) && strpos($element->href, '.php') !== false)
					{
						if (!$zpm->getByProdLnk($this->ownerSiteId, $this->ownerOrgId, "http://".$this->urlHostName.'/'.$element->href, $this->controlName))
						{
							$zpwObj = new zParseWork();
							$zpwObj->control = $this->controlName;
							$zpwObj->mode = $mode;
							$zpwObj->iteration = $iteration;
							$zpwObj->prodLink = "http://".$this->urlHostName.'/'.$element->href;
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
					$html = @file_get_html($url);

					if (!$html) {
						$zpm->remove($oneProdLink->id);
						continue;
					}

					// парсим сам товар теперь

					// наименование
					$prodName = '';
					$ret = $html->find('h3', 1);
					if ($ret)
						$prodName = $ret->plaintext;

					// артикул
					$prodArt = '';
					$ret = $html->find('p');
					if (count($ret)) {
						foreach ($ret AS $element) {
							if (strpos($element->plaintext, 'ртикуль') !== false)
								$prodArt = $element->plaintext;
						}
					}

					$prodArt = str_replace('Артикульный №', '', $prodArt);

					$prodName .= ' '.$prodArt;
					$prodName = trim($prodName);

					// фотки товара
					$picUrl1 = '';
					$ret = $html->find('img', 2);
					if ($ret)
						$picUrl1 = $ret->src;

					$picUrl2 = '';
					$ret = $html->find('img', 4);
					if ($ret)
						$picUrl2 = $ret->src;

					// цена
					$price = '';
					$ret = $html->find('p');
					if (count($ret)) {
						foreach ($ret AS $element) {
							if (strpos($element->plaintext, 'ена') !== false && strpos($element->plaintext, 'руб') !== false)
								$price = $element->plaintext;
						}
					}

					$price = str_replace('Цена ', '', $price);
					$price = str_replace(' рублей', '', $price);
					$price = str_replace(',', '.', $price);
					$price = round($price, 2);

					// cpt_product_description
					$desc = '';
					$ret = $html->find('td');
					if (count($ret)) {
						foreach ($ret AS $element) {
							if (strpos($element->height, '74') !== false) {
								$desc = $element->outertext;
							}
						}
					}

					$desc = str_replace('<p>&nbsp;</p>', '', $desc);
					$desc = str_replace('<p class="стиль4">_____________________</p>', '', $desc);
					$desc = Utility::html2bbcode($desc);

					// ряд (размеры)
					$sizesArray = array();
					$ret = $html->find('form', 0)->find('table', 0);
					if ($ret)
					{
						$retStr = array();
						// и тут начинается нечто странное и страшное
						for ($i = 1; $i <= 20; $i++)
							$retStr[] = $ret->find('tr', $i);

						foreach ($retStr AS $onRetStr)
						{
							if (!is_object($onRetStr))
								continue;

							$ret1 = $onRetStr->find('td', 0);
							$ret2 = $onRetStr->find('td', 1);

							$oneSz = '';
							if ($ret1)
								$oneSz .= $ret1->plaintext.' ';

							if ($ret2)
								$oneSz .= $ret2->plaintext;

							if (trim($oneSz) != '')
								$sizesArray[] = trim($oneSz);

						}

					}

					if (count($sizesArray))
						$gotSizes = implode(',', $sizesArray);
					else
						$gotSizes = '-';

					// записать
					$oneProdLink->params = serialize(array('prodName' => base64_encode($prodName), 'prodArt' => base64_encode($prodArt), 'picUrl1' => base64_encode($picUrl1), 'picUrl2' => base64_encode($picUrl2), 'price' => base64_encode($price), 'desc' => base64_encode($desc), 'sizes' => base64_encode($gotSizes)));

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

						if (!isset($params['prodName']) || !isset($params['prodArt']) || !isset($params['picUrl1']) || !isset($params['picUrl2']) || !isset($params['price']) || !isset($params['desc']) || !isset($params['sizes']))
							continue;

						$prodArt = base64_decode($params['prodArt']);

						$picUrl1 = base64_decode($params['picUrl1']);
						$picUrl2 = base64_decode($params['picUrl2']);

						$description = '';

						$description .= "[img]http://{$this->urlHostName}/{$picUrl1}[/img] ";

						$description .= "[img]http://{$this->urlHostName}/{$picUrl2}[/img]
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

						$gotSizes = base64_decode($params['sizes']);
						$gotSizesArray = @explode(',', $gotSizes);

						if (count($gotSizesArray))
							$zlObj->sizes = serialize($gotSizesArray);

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
