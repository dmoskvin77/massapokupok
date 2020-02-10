<?php
/**
* Действие для добавления объявления
*/

class UseraddadAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		require_once APPLICATION_DIR . "/Lib/resize.class.php";

		// при редактировании передается ID объявления
		$id = FilterInput::add(new IntFilter("adId", false, "ID объявления"));
		$typeId = FilterInput::add(new IntFilter("typeId", true, "Тип объявления"));
		$catId = FilterInput::add(new IntFilter("catId", false, "Категория"));
		$price = FilterInput::add(new StringFilter("price", false, "Цена"));
		$name = FilterInput::add(new StringFilter("name", true, "Название товара"));
		$description = Request::getVar("description");

		if (!$price || $price == '')
			$price = 0;

		$price = round(floatval($price), 2);
		if ($price < 0)
			FilterInput::addMessage("Не верно указана цена");

		$btm = new BoardTypeManager();
		$boardTypeObj = $btm->getById($typeId);
		if (!$boardTypeObj)
			FilterInput::addMessage("Тип объявления не был определён");

		if ($this->ownerSiteId != $boardTypeObj->ownerSiteId || $this->ownerOrgId != $boardTypeObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		$bcm = new BoardCategoryManager();

		$matches = array();
		$hasPhoto = preg_match("(\[img\](.+?)\[/img\])is", $description, $matches);

		/*
		if (!$hasPhoto)
			FilterInput::addMessage("В объявлении должна быть фотография ");
		*/

		if (!FilterInput::isValid())
		{
			FormRestore::add("user-addad");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// текущий участник
		$actor = $this->actor;

		$bam = new BoardAdManager();
		// передан id объявления для редактирования
		if ($id)
		{
			$bamObj = $bam->getById($id);
			if (!$bamObj) {
				FormRestore::add("user-addad");
				Enviropment::redirectBack("Объявление не найдено");
			}
			// не владелец
			if ($bamObj->userId != $actor->id || $this->ownerSiteId != $bamObj->ownerSiteId || $this->ownerOrgId != $bamObj->ownerOrgId)
			{
				FormRestore::add("user-addad");
				Enviropment::redirectBack("Нет прав на редактирование объявления");
			}

			$bamObj->picVer1 = $bamObj->picVer1 + 1;
		}
		else
		{
			$bamObj = new BoardAd();
			$bamObj->dateCreate = time();
			$bamObj->ownerSiteId = $this->ownerSiteId;
			$bamObj->ownerOrgId = $this->ownerOrgId;
		}

		$bamObj->dateUpdate = time();
		$bamObj->status = BoardAd::STATUS_NEW;
		$bamObj->typeId = $typeId;
		$bamObj->catId = $catId;
		$bamObj->userId = $actor->id;
		$bamObj->name = $name;
		$bamObj->price = $price;
		$bamObj->description = $description;

		// надо сохранить первую картинку, забрав её курлом
		// если таковая вообще есть
		if (isset($matches[1]))
		{
			// смотрим расширение файла, является ли оно допустимым
			$canGetFile = false;
			if (strpos(strtolower($matches[1]), '.png') !== false || strpos(strtolower($matches[1]), '.bmp') !== false || strpos(strtolower($matches[1]), '.gif') !== false || strpos(strtolower($matches[1]), '.jpg') !== false || strpos(strtolower($matches[1]), '.jpeg') !== false)
				$canGetFile = true;

			// попытка загрузить и заресайзить картинку должна быть обрамлена в try - catch
			if ($canGetFile)
			{
				try
				{
					$fileName = strtolower($matches[1]);
					$fileNameParts = explode('.', $fileName);
					$fileExtension = $fileNameParts[count($fileNameParts)-1];

					$file = $this->tplFolder."_".md5($fileName).md5(time()).".".$fileExtension;

					// Logger::info("filename: {$file}");

					$fullFileName = Configurator::get("application:zhFolder")."uploaded/".$file;
					$imgFileContent = file_get_contents($matches[1]);
					file_put_contents($fullFileName, $imgFileContent);

					// сделаем копию маленького размера
					// 100 x 100
					$w = 100;
					$h = 100;
					if (file_exists($fullFileName))
					{
						$newFileName = Configurator::get("application:boardFolder")."small/".$file;
						$obj = new Resize($fullFileName);
						$obj->setNewImage($newFileName);
						$obj->setProportionalFlag('A');
						$obj->setProportional(1);
						$obj->setNewSize($h, $w);
						$obj->make();

						$bamObj->picFile1 = $file;
					}

				}
				catch (Exception $e)
				{
					Logger::error($e->getMessage());
				}

				@unlink(Configurator::get("application:boardFolder")."uploaded/".$file);

			}

		}

		$bamObj = $bam->save($bamObj);

		// пересчитаем в категории и в типе кол-во объявлений, обновим данные
		$bam->rebuildCounters($bamObj->typeId, $bamObj->catId);

		// всё готово
		Enviropment::redirect("board", "Ваше объявление будет опубликовано после проверки модератором");

	}

}
