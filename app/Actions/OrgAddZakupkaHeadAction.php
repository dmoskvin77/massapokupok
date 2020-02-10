<?php
/**
* Действие для добавления новой головы закупки
*
*/
class OrgaddzakupkaheadAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		require_once APPLICATION_DIR . "/Lib/resize.class.php";

		$id = FilterInput::add(new IntFilter("id", false, "ID закупки"));
		$name = FilterInput::add(new StringFilter("name", true, "Наименование"));
		$catid1 = FilterInput::add(new IntFilter("catId1", true, "Категория основная"));
		$catid2 = FilterInput::add(new IntFilter("catId2", false, "Категория 2"));
		$catid3 = FilterInput::add(new IntFilter("catId3", false, "Категория 3"));
		$optId = FilterInput::add(new IntFilter("optId", false, "Поставщик"));
		$orgRate = FilterInput::add(new IntFilter("orgRate", true, "Оргсбор"));
		$minAmount = FilterInput::add(new IntFilter("minAmount", false, "Минималка рубли"));
		$minValue = FilterInput::add(new IntFilter("minValue", false, "Минималка по кол-ву"));
		$currency = FilterInput::add(new StringFilter("currency", false, "Валюта закупки"));
		$useForm = FilterInput::add(new StringFilter("useForm", false, "Кнопка Заказать"));
		$usePay = FilterInput::add(new StringFilter("usePay", false, "Кнопка Оплатить"));
		$specialNotes = FilterInput::add(new StringFilter("specialNotes", true, "Аннотация"));
		$description = Request::getVar("description");
		$status = FilterInput::add(new StringFilter("status", false, "Статус закупки"));

		$zhm = new ZakupkaHeaderManager();
		$actor = Context::getActor();
		$orgId = $actor->id;

		// предыдущий статус закупки
		$oldStatus = null;

		// передан id закупки
		$getZH = null;
		if ($id)
		{
			// определим можно ли редактировать данную закупку
			$getZH = $zhm->getById($id);
			if (!$getZH)
				Enviropment::redirectBack("Не найдена закупка");

			if ($getZH->orgId != $orgId || $this->ownerSiteId != $getZH->ownerSiteId || $this->ownerOrgId != $getZH->ownerOrgId)
				Enviropment::redirectBack("Нет прав на редактирование закупки");

			if (!$status)
				FilterInput::addMessage("Не был выбран статус закупки");

			// проверим допустимо ли меняется статус у закупки
			$allowedStatuses = ZakupkaHeader::getStatusDescCurrent($status);
			if (count($allowedStatuses))
			{
				$isNewStatusOk = false;
				foreach ($allowedStatuses AS $alStatKey => $alStatVal)
				{
					if ($alStatKey == $status)
						$isNewStatusOk = true;
				}

				if (!$isNewStatusOk)
					FilterInput::addMessage("Нельзя установить выбранный статус");
			}

			$oldStatus = $getZH->status;

		}
		else
		{
			if (!$status)
				$status = ZakupkaHeader::STATUS_NEW;
		}

		// анализ введенных данных
		if (mb_strlen($description) > 10000000)
			FilterInput::addMessage("Слишком большой текст");

		if ($orgRate > 20)
			FilterInput::addMessage("Орг сбор не больше 20%");

		$om = new OptovikManager();
		$optObject = $om->getById($optId);
		if ($optObject)
		{
			if ($optObject->userId != $orgId)
				FilterInput::addMessage("Выбранный оптовик за Вами не закреплен");

			if ($optObject->status != Optovik::STATUS_ACTIVE)
				FilterInput::addMessage("Выбранный оптовик за Вами не закреплен");
		}
		else
		{
			FilterInput::addMessage("Не найден выбранный оптовик");
		}

		if (!FilterInput::isValid())
		{
			FormRestore::add("opt-zakupka-head-add");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// формируем массив данных
		$addData = compact("name", "orgId", "optId", "orgRate", "minAmount", "minValue", "description", "specialNotes",
				"status", "oldStatus", "useForm", "usePay", "currency", "catid1", "catid2", "catid3");

		if (!$getZH)
		{
			// начало транзакции
			$zhm->startTransaction();
			try
			{
				$getNewZH = $zhm->addNewZH($this->ownerSiteId, $this->ownerOrgId, $addData);
			}
			catch (Exception $e)
			{
				$zhm->rollbackTransaction();
				Logger::error($e->getMessage());
				Enviropment::redirectBack("Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке (1)");
			}
			$zhm->commitTransaction();
		}
		else
		{
			// здесь будет ряд кейсов, в зависимости от статуса закупки, который был,
			// который хотим установить

			// для начала сделаем просто общее сохранение данных для упрощения
			// начало транзакции
			$zhm->startTransaction();
			try
			{
				$getNewZH = $zhm->addNewZH($this->ownerSiteId, $this->ownerOrgId, $addData, $id);
			}
			catch (Exception $e)
			{
				$zhm->rollbackTransaction();
				Logger::error($e->getMessage());
				Enviropment::redirectBack("Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке (2)");
			}
			$zhm->commitTransaction();

		}

		// проверим добавлена ли новая закупка
		// загрузка прайсов и картинок
		$rezArray = array();
		$rezArrayDoc = array();
		for ($i=1; $i<=3; $i++)
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

		// загружаем файлы, масштабируем их
		// картинка сейчас будет только одна
		for ($i=1; $i<=1; $i++)
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


		// подготовим переменные под сохранение картинок
		$picFile1 = null;

		// документы (прайсы)
		$docFile1 = null; $docFile2 = null; $docFile3 = null;

		$countFlName = 1;
		foreach ($rezArray as $rezItem)
		{
			if ($countFlName == 1) $picFile1 = $rezItem;
			$countFlName++;
		}

		$countFlDoc = 1;
		foreach ($rezArrayDoc as $rezItem)
		{
			if ($countFlDoc == 1) $docFile1 = $rezItem;
			if ($countFlDoc == 2) $docFile2 = $rezItem;
			if ($countFlDoc == 3) $docFile3 = $rezItem;
			$countFlDoc++;
		}

		$srvurl = "http://".$_SERVER['HTTP_HOST'];

		if ($picFile1 != null)
		{
			// если имеется предыдущая картинка, её надо удалить
			if ($getNewZH->picFile1)
			{
				@unlink(Configurator::get("application:zhFolder")."small/".$getNewZH->picFile1);
				@unlink(Configurator::get("application:zhFolder")."medium/".$getNewZH->picFile1);
				// новая версия залилась
				$getNewZH->picVer1 = $getNewZH->picVer1 + 1;
			}

			$getNewZH->picFile1 = $picFile1;
			$getNewZH->picSrv1 = $srvurl;

		}

		if ($docFile1 != null) { $getNewZH->docFile1 = $docFile1; $getNewZH->docSrv1 = $srvurl; }
		if ($docFile2 != null) { $getNewZH->docFile2 = $docFile2; $getNewZH->docSrv2 = $srvurl; }
		if ($docFile3 != null) { $getNewZH->docFile3 = $docFile3; $getNewZH->docSrv3 = $srvurl; }

		$zhm->save($getNewZH);

		// переход на страницу со списком закупок орга
		Enviropment::redirect("orgzhlist");


	}

}
