<?php
/**
* загрузка csv файла с рядами в закупку
*
*/
class OrgimportzakupkafileAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$fileProcessed = false;
		$actor = $this->actor;

		$mode = Request::getVar("mode");

		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		$token = Request::getVar('token');
		if (!$token)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		$guid = Enviropment::getBasketGUID();

		// есть ли указанный токен
		$hasToken = Tocken::hasTocken($token, $guid, 'Orgimportzakupka', $actor->id, $this->ownerSiteId, $this->ownerOrgId);
		if (!$hasToken)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// Request::clearInput - чистка вводимых данных
		// файлик получим и посмотрим что это за файлик
		$fileName = 'csvfile';
		if (Request::isFile($fileName))
		{
			// затариваем построчно в БД, инсертить будем собирая запросы
			try
			{
				// сначала очистим базу от предыдущей загрузки
				Csvfile::removeLines(Enviropment::getBasketGUID(), $actor->id, $headid, $this->ownerSiteId, $this->ownerOrgId);

				$csvfile = new UploadedFile($fileName, false, "csv");
				if (strtolower($csvfile->extension) == 'csv')
				{
					$file = $this->tplFolder . "_" . md5($csvfile->name) . md5(time()) . ".csv";
					$csvfile->rename($file);
					$csvfile->saveTo(Configurator::get("application:zhFolder") . "uploaded/");
					$fullFileName = Configurator::get("application:zhFolder")."uploaded/".$file;
					if (file_exists($fullFileName))
					{
						$handle = @fopen($fullFileName, "r");
						$csvlines = array();
						$cnti = 0;
						while (!feof($handle))
						{
							$line = fgets($handle);
							if ($line === false)
								break;

							// убрать из строки некоторые символы
							$line = str_replace('"', '', $line);
							$line = str_replace("'", '', $line);
							$line = str_replace('\\', '', $line);

							// не загружаем самую верхнюю строчку,
							// т.к. это заголовок таблицы
							if ($cnti > 0)
								$csvlines[] = $line;

							$cnti++;
							if ($cnti >= 201)
							{
								// запишем строки в БД
								Csvfile::saveLines(Enviropment::getBasketGUID(), $csvlines, $actor->id, $headid, $mode, $this->ownerSiteId, $this->ownerOrgId);

								// почистим  счетчики
								$cnti = 1;
								$csvlines = array();

							}
						}

						// запишем строки в БД (остатки)
						if (count($csvlines))
							Csvfile::saveLines(Enviropment::getBasketGUID(), $csvlines, $actor->id, $headid, $mode, $this->ownerSiteId, $this->ownerOrgId);

						@unlink($fullFileName);
						$fileProcessed = true;

					}

				}

			}
			catch (Exception $e)
			{
				Logger::error($e);
				$fileProcessed = false;
			}

		}

		// что-то обработали
		if ($fileProcessed) {
            if ($mode)
                Enviropment::redirect("orgimportzakupkaprocess/headid/" . $headid . "/mode/" . $mode, "Файл загружен");
            else
                Enviropment::redirect("orgimportzakupkaprocess/headid/" . $headid, "Файл загружен");
        }
		else
			Enviropment::redirectBack("Не удалось загрузить файл. Проверьте его соответствие.");

	}

}
