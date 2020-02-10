<?php
/**
* Действие для сохранения головы закупки
*
*/
class OrgEditZakupkaHeadAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		// пересчитать цены у всех строк, которые ниже в ZL!!!
		// и уведомить всех покупателей, которые разместили заказ!!!

		require_once APPLICATION_DIR . "/Lib/resize.class.php";

		$headid = FilterInput::add(new IntFilter("headid", true, "Код закупки"));
		$name = FilterInput::add(new StringFilter("name", true, "Наименование"));
        $catid = FilterInput::add(new IntFilter("catId", false, "Категория"));
		$optId = FilterInput::add(new IntFilter("optId", true, "Поставщик"));
		$startDate = FilterInput::add(new StringFilter("startDate", false, "Дата начала"));
		$validDate = FilterInput::add(new StringFilter("validDate", false, "Дата конца"));
		$orgRate = FilterInput::add(new IntFilter("orgRate", true, "Наценка"));
        $setcityid = FilterInput::add(new IntFilter("setcityid", false, "Город"));
		$minAmount = FilterInput::add(new IntFilter("minAmount", false, "Минимальная сумма закупки"));
        $currency = FilterInput::add(new StringFilter("currency", false, "Валюта закупки"));
		$minValue = FilterInput::add(new IntFilter("minValue", false, "Мин. кол-во"));
		$description = Request::getVar("description");
		$specialNotes = FilterInput::add(new StringFilter("specialNotes", true, "Аннотация"));
		$status = FilterInput::add(new StringFilter("status", false, "Статус"));
		$pageUrl = FilterInput::add(new StringFilter("pageUrl", false, "URL страницы"));
		$useForm = FilterInput::add(new StringFilter("useForm", false, "Форма заказа без рядов"));
		$allowRussia = FilterInput::add(new StringFilter("allowRussia", false, "Закупка по России"));
		$removeallpics = Request::getVar("removeallpics");
		$removealldocs = Request::getVar("removealldocs");

		if (mb_strlen($description) > 10000000)
			FilterInput::addMessage("Слишком большой текст");

        if ($orgRate > 20)
            FilterInput::addMessage("Орг сбор не больше 20%");

		if (!FilterInput::isValid())
		{
			FormRestore::add("opt-zakupka-head-add");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// преобразование даты
		$unixStartDate = Utility::pickerDateToTime($startDate);
		$unixValidDate = Utility::pickerDateToTime($validDate);

		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		$zhm = new ZakupkaHeaderManager();
		$curZH = $zhm->getByIdAndOrgId($headid, $actor->id);
		if (!$curZH)
			Enviropment::redirectBack("Не найдена закупка");

		if ($this->ownerSiteId != $curZH->ownerSiteId || $this->ownerOrgId != $curZH->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		// получим все строки закупок, у которых голова равна headid
		$zlm = new ZakupkaLineManager($headid);
		$zlines = $zlm->getByHeadId($headid);

		$productsArray = array();
		$productsArrayWholescale = array();
		if (count($zlines) > 0)
		{
			// переберём строки и поменяем цены
			// это на случай, если орг поменял процент
			foreach ($zlines as $zitem)
			{
				$newPrice = $zitem->wholePrice + $zitem->wholePrice /100 * $orgRate;
				$zitem->finalPrice = $newPrice;
				// новые цены на продукты (товары)
				$productsArray[$zitem->productId] = $newPrice;
				$productsArrayWholescale[$zitem->productId] = $zitem->wholePrice;
				$zlm->save($zitem);
			}
		}

		// получим заголовки заказов, которые были сделаны по данной закупке
		$uom = new OrderHeadManager();
		$orderList = $uom->getByHeadId($headid);
		$userIds = array();
		if (count($orderList) > 0)
		{
			// перебрав получим список пользователей для рассылки уведомлений
			foreach ($orderList as $uoitem)
			{
				if (!in_array($uoitem->userId, $userIds))
					$userIds[$uoitem->id] = $uoitem->userId;
			}
		}

		// так же поменяем цены в OrderList
		$osm = new OrderListManager();
		$osetList = $osm->getByHeadId($headid);

		$uniquePrice = array();
		// оптовая цена здесь не меняется - только процент и розница
		// $uniqueWholescale = array();
		if (count($osetList) > 0)
		{
			// переберём строки и поменяем цены
			// $ositem - строка OrderList
			foreach ($osetList as $ositem)
			{
				// если это продукт с ряда, то корректируем строку
				if (isset($productsArray[$ositem->productId]))
				{
					$newUniqueAmount = $productsArray[$ositem->productId] * $ositem->productValue;
					$newUniqueWholescale = $productsArrayWholescale[$ositem->productId] * $ositem->productValue;

					$ositem->productPrice = $productsArray[$ositem->productId];
					$ositem->productAmount = $newUniqueAmount;
					$ositem->wholescaleAmount = $newUniqueWholescale;

					if (isset($uniquePrice[$ositem->uniqueId]))
						$uniquePrice[$ositem->uniqueId] = $uniquePrice[$ositem->uniqueId] + $newUniqueAmount;
					else
						$uniquePrice[$ositem->uniqueId] = $newUniqueAmount;

				}
				else
				{
					// корректируем строку по упращённому алгоритму (заказано по артикулу)
					if ($ositem->productValue > 0)
					{
						$newAmount = $ositem->wholescaleAmount + $ositem->wholescaleAmount / 100 * $orgRate;
						$ositem->productAmount = $newAmount;
                        $ositem->productPrice = $newAmount / $ositem->productValue;

						if (isset($uniquePrice[$ositem->uniqueId]))
							$uniquePrice[$ositem->uniqueId] = $uniquePrice[$ositem->uniqueId] + $newAmount;
						else
							$uniquePrice[$ositem->uniqueId] = $newAmount;
					}
					else
					{
						$ositem->productAmount = 0;
                        $ositem->productPrice = 0;
					}
				}

				$osm->save($ositem);
			}
		}

		// теперь надо пересчитать сумму всех заказов orderHead, т.к. строки заказов уже пересчитаны
		if (count($orderList) > 0)
		{
			$uoArray = array();
			foreach ($orderList as $uoitem)
			{
				if (isset($uniquePrice[$uoitem->id]))
				{
					$uoitem->orderAmount = $uniquePrice[$uoitem->id];
					$uom->save($uoitem);
					$uoArray[$uoitem->id] = $uoitem;
				}
			}

			// сделаем последний пересчет с учетом наличия
			foreach ($uoArray as $uKey => $uVal)
			{
				// $hVal - объект
				// $hKey - id
				// готовим пустые переменные под суммы
				$orderValue = 0;
				$orderAmount = 0;
				$wholescaleAmount = 0;
				// просканируем заказы, те, у которых notAvailable == 1 - в сумму НЕ включаем!
				$checkList = $osm->getByUniqueId($uKey);
				if ($checkList)
				{
					foreach ($checkList as $chItem)
					{
						if ($chItem->notAvailable != 1)
						{
							$orderValue = $orderValue + $chItem->productValue;
							$orderAmount = $orderAmount + $chItem->productAmount;
							$wholescaleAmount = $wholescaleAmount + $chItem->wholescaleAmount;
						}
					}
				}

				$uVal->orderValue = $orderValue;
				$uVal->orderAmount = $orderAmount;
				$uVal->wholescaleAmount = $wholescaleAmount;
				$uom->save($uVal);
			}

		}

		// вот теперь можно сохранить параметры, которые скормили в голову закупки

		// формируем массив данных
		$addData = compact("name", "orgId", "optId", "unixStartDate", "unixValidDate", "orgRate", "minAmount", "minValue", "description", "specialNotes", "pageUrl", "status", "useForm", "allowCity", "allowClubs", "allowRussia", "currency", "setcityid", "catid");

		// начало транзакции
		$zhm = new ZakupkaHeaderManager();

		$zhm->startTransaction();
		try
		{
			$getNewZH = $zhm->addNewZH($this->ownerSiteId, $this->ownerOrgId, $addData, $headid);
		}
		catch (Exception $e)
		{
			$zhm->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirectBack("Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке");
		}
		$zhm->commitTransaction();

		// проверим добавлен ли новый орг
		if (!$getNewZH)
		{
			Enviropment::redirectBack("Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке");
		}
		else
		{
			// загрузим и отресазим картинки
			$rezArray = array();
			$rezArrayDoc = array();

			// всасываем файлы, масштабируем их
			for($i=1; $i<=3; $i++)
			{
				$fileName = "doc".$i;
				try
				{
					// был ли загружен файл?
					if (Request::isFile($fileName))
					{
						$doc = new UploadedFile($fileName);
						// имя файла : хэш
						if (in_array($doc->extension, array("doc", "pdf", "xls")) && in_array($doc->type, array("application/msword", "application/vnd.ms-excel", "application/pdf")))
						{
							$file = $this->tplFolder."_".md5($doc->name).md5(time()).".".$doc->extension;
							$doc->rename($file);
							$doc->saveTo(Configurator::get("application:docFolder"));
							if (file_exists(Configurator::get("application:docFolder").$file))
								$rezArrayDoc[] = $file;
						}
					}
				}
				catch (Exception $e)
				{
					// ловим ошибки при сохранении фоток
					Logger::error("zh doc error:");
					Logger::error($e);
				}
			}

			// http://pikachoose.com/demo/ - галерея
			// всасываем файлы, масштабируем их
			for($i=1; $i<=10; $i++)
			{
				$fileName = "file".$i;
				try
				{
					// был ли загружен файл?
					if (Request::isFile($fileName))
					{
						$image = new UploadedFile($fileName);

						// имя файла : хэш
						$file = $this->tplFolder."_".md5($image->name).md5(time()).".".$image->extension;
						$image->rename($file);
						$image->saveTo(Configurator::get("application:zhFolder")."uploaded/");

						// сделаем копию небольшого размера
						// 450 x 320
						$w = 450;
						$h = 320;
						$fullFileName = Configurator::get("application:zhFolder")."uploaded/".$file;
						if (file_exists($fullFileName))
						{
							$newFileName = Configurator::get("application:zhFolder")."medium/".$file;
							try {
								$obj = new Resize($fullFileName);
								$obj->setNewImage($newFileName);
								$obj->setProportionalFlag('P');
								$obj->setProportional(1);
								$obj->setNewSize($h, $w);
								$obj->make();
							}
							catch (Exception $e) {
								Logger::error($e);
							}

						}

						// сделаем копию маленького размера
						// 100 x 100
						$w = 100;
						$h = 100;
						if (file_exists($fullFileName))
						{
							$newFileName = Configurator::get("application:zhFolder")."small/".$file;
							try {
								$obj = new Resize($fullFileName);
								$obj->setNewImage($newFileName);
								$obj->setProportionalFlag('A');
								$obj->setProportional(1);
								$obj->setNewSize($h, $w);
								$obj->make();
							}
							catch (Exception $e) {
								Logger::error($e);
							}
						}
						// оба файла получились, добавим в список загруженных
						if (file_exists(Configurator::get("application:zhFolder")."medium/".$file) && file_exists(Configurator::get("application:zhFolder")."small/".$file))
							$rezArray[] = $file;
						// удалим исходный
						if (file_exists(Configurator::get("application:zhFolder")."uploaded/".$file))
							unlink(Configurator::get("application:zhFolder")."uploaded/".$file);
					}
				}
				catch (Exception $e)
				{
					// ловим ошибки при сохранении фоток
					Logger::error("zh pic resize error:");
					Logger::error($e);
				}
			}

			// удалим старые фото если включена галка
			if ($removeallpics && $getNewZH)
			{
				if ($getNewZH->picFile1)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile1))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile1);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile1))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile1);
				}

				if ($getNewZH->picFile2)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile2))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile2);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile2))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile2);
				}

				if ($getNewZH->picFile3)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile3))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile3);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile3))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile3);
				}

				if ($getNewZH->picFile4)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile4))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile4);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile4))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile4);
				}

				if ($getNewZH->picFile5)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile5))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile5);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile5))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile5);
				}

				if ($getNewZH->picFile6)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile6))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile6);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile6))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile6);
				}

				if ($getNewZH->picFile7)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile7))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile7);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile7))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile7);
				}

				if ($getNewZH->picFile8)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile8))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile8);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile8))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile8);
				}

				if ($getNewZH->picFile9)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile9))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile9);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile9))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile9);
				}

				if ($getNewZH->picFile10)
				{
					if (file_exists(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile10))
						unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile10);

					if (file_exists(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile10))
						unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile10);
				}

				// убираем в базе
				$getNewZH->picFile1 = null;
				$getNewZH->picFile2 = null;
				$getNewZH->picFile3 = null;
				$getNewZH->picFile4 = null;
				$getNewZH->picFile5 = null;
				$getNewZH->picFile6 = null;
				$getNewZH->picFile7 = null;
				$getNewZH->picFile8 = null;
				$getNewZH->picFile9 = null;
				$getNewZH->picFile10 = null;

				$getNewZH->picSrv1 = null;
				$getNewZH->picSrv2 = null;
				$getNewZH->picSrv3 = null;
				$getNewZH->picSrv4 = null;
				$getNewZH->picSrv5 = null;
				$getNewZH->picSrv6 = null;
				$getNewZH->picSrv7 = null;
				$getNewZH->picSrv8 = null;
				$getNewZH->picSrv9 = null;
				$getNewZH->picSrv10 = null;

				$zhm->save($getNewZH);
			}

			// удалим старые доки
			if ($removealldocs && $getNewZH)
			{
				if ($getNewZH->docFile1)
				{
					if (file_exists(Configurator::get("application:docFolder").$getNewZH->docFile1))
						unlink(Configurator::get("application:docFolder").$getNewZH->docFile1);
				}

				if ($getNewZH->docFile2)
				{
					if (file_exists(Configurator::get("application:docFolder").$getNewZH->docFile2))
						unlink(Configurator::get("application:docFolder").$getNewZH->docFile2);
				}

				if ($getNewZH->docFile3)
				{
					if (file_exists(Configurator::get("application:docFolder").$getNewZH->docFile3))
						unlink(Configurator::get("application:docFolder").$getNewZH->docFile3);
				}

				$getNewZH->docFile1 = null;
				$getNewZH->docFile2 = null;
				$getNewZH->docFile3 = null;

				$zhm->save($getNewZH);
			}

			// подготовим переменные под сохранение продукта
			$picFile1 = null; $picFile2 = null; $picFile3 = null; $picFile4 = null; $picFile5 = null;
			$picFile6 = null; $picFile7 = null; $picFile8 = null; $picFile9 = null; $picFile10 = null;

			$docFile1 = null; $docFile2 = null; $docFile3 = null;

			// кол-во уже имеющихся файлов
			$countFlPresence = 0;
			if ($getNewZH)
			{
				$countFlName = 1;
				$tmpPicArray = array();
				// начались какие-то чудовищные манипуляции
				if ($getNewZH->picFile1) $countFlPresence++;
				if ($getNewZH->picFile2) $countFlPresence++;
				if ($getNewZH->picFile3) $countFlPresence++;
				if ($getNewZH->picFile4) $countFlPresence++;
				if ($getNewZH->picFile5) $countFlPresence++;
				if ($getNewZH->picFile6) $countFlPresence++;
				if ($getNewZH->picFile7) $countFlPresence++;
				if ($getNewZH->picFile8) $countFlPresence++;
				if ($getNewZH->picFile9) $countFlPresence++;
				if ($getNewZH->picFile10) $countFlPresence++;

				foreach ($rezArray as $rezItem)
				{
					$tmpPicArray[$countFlName + $countFlPresence] = $rezItem;
					$countFlName++;
				}

				foreach ($tmpPicArray as $tpkey => $tpval)
				{
					if ($tpkey == 1) $picFile1 = $tpval;
					if ($tpkey == 2) $picFile2 = $tpval;
					if ($tpkey == 3) $picFile3 = $tpval;
					if ($tpkey == 4) $picFile4 = $tpval;
					if ($tpkey == 5) $picFile5 = $tpval;
					if ($tpkey == 6) $picFile6 = $tpval;
					if ($tpkey == 7) $picFile7 = $tpval;
					if ($tpkey == 8) $picFile8 = $tpval;
					if ($tpkey == 9) $picFile9 = $tpval;
					if ($tpkey == 10) $picFile10 = $tpval;
				}
			}

			// по докам
			$countDocPresence = 0;
			if ($getNewZH)
			{
				$countDocName = 1;
				$tmpDocArray = array();
				if ($getNewZH->docFile1) $countDocPresence++;
				if ($getNewZH->docFile2) $countDocPresence++;
				if ($getNewZH->docFile3) $countDocPresence++;

				foreach ($rezArrayDoc as $rezItem)
				{
					$tmpDocArray[$countDocName + $countDocPresence] = $rezItem;
					$countDocName++;
				}

				foreach ($tmpDocArray as $tpkey => $tpval)
				{
					if ($tpkey == 1) $docFile1 = $tpval;
					if ($tpkey == 2) $docFile2 = $tpval;
					if ($tpkey == 3) $docFile3 = $tpval;
				}
			}

			$srvurl = "http://".$_SERVER['HTTP_HOST'];

			if ($picFile1 != null) { $getNewZH->picFile1 = $picFile1; $getNewZH->picSrv1 = $srvurl; }
			if ($picFile2 != null) { $getNewZH->picFile2 = $picFile2; $getNewZH->picSrv2 = $srvurl; }
			if ($picFile3 != null) { $getNewZH->picFile3 = $picFile3; $getNewZH->picSrv3 = $srvurl; }
			if ($picFile4 != null) { $getNewZH->picFile4 = $picFile4; $getNewZH->picSrv4 = $srvurl; }
			if ($picFile5 != null) { $getNewZH->picFile5 = $picFile5; $getNewZH->picSrv5 = $srvurl; }
			if ($picFile6 != null) { $getNewZH->picFile6 = $picFile6; $getNewZH->picSrv6 = $srvurl; }
			if ($picFile7 != null) { $getNewZH->picFile7 = $picFile7; $getNewZH->picSrv7 = $srvurl; }
			if ($picFile8 != null) { $getNewZH->picFile8 = $picFile8; $getNewZH->picSrv8 = $srvurl; }
			if ($picFile9 != null) { $getNewZH->picFile9 = $picFile9; $getNewZH->picSrv9 = $srvurl; }
			if ($picFile10 != null) { $getNewZH->picFile10 = $picFile10; $getNewZH->picSrv10 = $srvurl; }

			if ($docFile1 != null) { $getNewZH->docFile1 = $docFile1; $getNewZH->docSrv1 = $srvurl; }
			if ($docFile2 != null) { $getNewZH->docFile2 = $docFile2; $getNewZH->docSrv2 = $srvurl; }
			if ($docFile3 != null) { $getNewZH->docFile3 = $docFile3; $getNewZH->docSrv3 = $srvurl; }

			$zhm->save($getNewZH);

			// надо разослать письма пользователям
			// $headid
			// $userIds[$uniqueId]
			if (count($userIds) > 0)
			{
				// TODO: так низя - слишком долго, надо ставить в очередь
				foreach ($userIds as $uKey => $uVal)
					$this->sendEmail($uVal, $uKey, $status, $name);

			}

			// переход на страницу входа
			// Enviropment::redirect("orgviewzakupka/headid/".$getNewZH->id, "Закупка отредактирована");

			Enviropment::redirectBack("Закупка отредактирована");

		}

	}


	// отправка писем
	/**
	* Функция отправляет письмо на е-майл
	*
	* @param User $user пользователь
	*/

	protected function sendEmail($userId, $unuqueId, $status, $name)
	{
		$shortTitle = "Произошли изменения в закупке";

		$um = new UserManager();
		$user = $um->getById($userId);
		if ($user)
		{
			$host = 'http://'.$this->host;
			$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
			$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
			$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

			$viewLink = $host."/viewzakaz/id/".$unuqueId;

			if ($user->firstName || $user->secondName)
				$user->firstName = ', '.$user->firstName;

			if ($user->secondName)
				$user->secondName = ' '.$user->secondName;

			if ($user->lastName)
				$user->lastName = ' '.$user->lastName;

			$vars = array(
				"MESSAGE_TITLE" => $shortTitle,
				"ZAK_TITLE" => $name,
				"USER_LOGIN" => $user->login,
				"FIRST_NAME" => $user->firstName,
				"SECOND_NAME" => $user->secondName,
				"LAST_NAME" => $user->lastName,
				"VIEW_LINK" => $viewLink,
				"MESSAGE_SIGN" => $signMessage,
				"HOST" => $host
			);

			$header = Enviropment::prepareForMail(MailTextHelper::parse("header.html", $vars));

			if ($status == ZakupkaHeader::STATUS_NEW || $status == ZakupkaHeader::STATUS_ACTIVE)
				$body = Enviropment::prepareForMail(MailTextHelper::parse("changezakupka.html", $vars));

			if ($status == ZakupkaHeader::STATUS_STOP)
				$body = Enviropment::prepareForMail(MailTextHelper::parse("stopzakupka.html", $vars));

			if ($status == ZakupkaHeader::STATUS_CHECKED)
				$body = Enviropment::prepareForMail(MailTextHelper::parse("checkzakupka.html", $vars));

			if ($status == ZakupkaHeader::STATUS_ADDMORE)
				$body = Enviropment::prepareForMail(MailTextHelper::parse("morezakupka.html", $vars));

			if ($status == ZakupkaHeader::STATUS_SEND)
				$body = Enviropment::prepareForMail(MailTextHelper::parse("waitingzakupka.html", $vars));

			if ($status == ZakupkaHeader::STATUS_DELIVERED)
				$body = Enviropment::prepareForMail(MailTextHelper::parse("deliveredzakupka.html", $vars));

			if ($status == ZakupkaHeader::STATUS_CLOSED)
				$body = Enviropment::prepareForMail(MailTextHelper::parse("closezakupka.html", $vars));

			// при других статусов надо отправить сообщения с другим текстом

			$footer = Enviropment::prepareForMail(MailTextHelper::parse("footer.html", $vars));
			if ($user->login)
				allMailManager::addMailMessage($shortTitle, $header.$body.$footer, $user->login, $fromEmail, $fromName);

		}

	}

}
