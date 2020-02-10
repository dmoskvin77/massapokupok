<?php
/**
* Действие для изменения поставщика
*
*/
class OrgeditoptAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
 		$id = FilterInput::add(new StringFilter("id", false, "ID оптовика"));
		$name = FilterInput::add(new StringFilter("name", true, "Наименование"));

		if (!FilterInput::isValid())
		{
			FormRestore::add("opt-edit");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		// текущий поставщик
		$op = new OptovikManager();
		if ($id)
		{
			$curOpt = $op->getById($id);

			if (!$curOpt)
				Enviropment::redirectBack("Не найден поставщик");

			if ($curOpt->userId != $actor->id || $this->ownerSiteId != $curOpt->ownerSiteId || $this->ownerOrgId != $curOpt->ownerOrgId)
				Enviropment::redirectBack("Вы не можете изменить данные поставщика, т.к. он к вам не привязан");

		}
		else
		{
			$curOpt = new Optovik();
			$curOpt->userId = $orgId;
			$curOpt->status = Optovik::STATUS_ACTIVE;
			$curOpt->dateCreate = time();
			$curOpt->dateUpdate = time();
			$curOpt->ownerSiteId = $this->ownerSiteId;
			$curOpt->ownerOrgId = $this->ownerOrgId;
			$curOpt = $op->save($curOpt);
			$id = $curOpt->id;
		}

		$ulm = new UrlListManager();
		// сохранение ссылок
		// составим 2 массива ссылок: измененные урлы и новые
		$existsUrls = array();
		// что нужно будет исключить из поиска уникальности
		$excludeIds = array();
		$gotExstUlrNames = array();
		$allVars = $_POST;
		if (count($allVars))
		{
			foreach ($allVars AS $varKey => $varVal)
			{
				$varVal = Utility::prepareHostName($varVal);
				// url_ - это существующие
				if (strstr($varKey, 'url_') !== false && $varVal && $varVal != '')
				{
					$splArray = explode("_", $varKey);
					if (isset($splArray[1]) && intval($splArray[1]) && !in_array($varVal, $gotExstUlrNames))
					{
						$excludeIds[] = intval($splArray[1]);
						$existsUrls[intval($splArray[1])] = $varVal;
						$gotExstUlrNames[] = $varVal;
					}

				}

			}

		}

		// проверим что из перечисленного можем сохранить, а что нет
		$notSavedUrls = array();
		if (count($existsUrls))
		{
			foreach ($existsUrls AS $oneExstUrlId => $oneExstUrlName)
			{
				$isUnique = $ulm->isUnique($this->ownerSiteId, $this->ownerOrgId, $oneExstUrlName, $excludeIds);
				$isSavedUrl = false;
				if ($isUnique)
				{
					// получаем объект - ссылку
					// проверяем того ли она оптовика
					// если да, то сохраняем измененный url
					$gotOneUrl = $ulm->getById($oneExstUrlId);
					if ($gotOneUrl)
					{
						$gotOptovik = $op->getById($gotOneUrl->optId);
						if ($gotOptovik)
						{
							if ($gotOptovik->userId == $orgId)
							{
								$gotOneUrl->url = $oneExstUrlName;
								$ulm->save($gotOneUrl);
								$isSavedUrl = true;
							}

						}

					}

				}

				if (!$isSavedUrl && $oneExstUrlName && $oneExstUrlName != '')
					$notSavedUrls[] = $oneExstUrlName;

			}

		}

		// newurl_1, newurl_2, newurl_3 - это новый
		// новые надо сохранить только если их нет в $gotExstUlrNames
		$gotNewUrl = Request::getVar('urlnew_1');
		if ($gotNewUrl)
		{
			$gotNewUrl = Utility::prepareHostName($gotNewUrl);
			if ($gotNewUrl && $gotNewUrl != '')
			{
				if (!in_array($gotNewUrl, $gotExstUlrNames) && $ulm->isUnique($this->ownerSiteId, $this->ownerOrgId, $gotNewUrl))
					$this->saveNewOptovik($gotNewUrl, $id);
				else
					$notSavedUrls[] = $gotNewUrl;
			}
		}

		$gotNewUrl = Request::getVar('urlnew_2');
		if ($gotNewUrl)
		{
			$gotNewUrl = Utility::prepareHostName($gotNewUrl);
			if ($gotNewUrl && $gotNewUrl != '')
			{
				if (!in_array($gotNewUrl, $gotExstUlrNames) && $ulm->isUnique($this->ownerSiteId, $this->ownerOrgId, $gotNewUrl))
					$this->saveNewOptovik($gotNewUrl, $id);
				else
					$notSavedUrls[] = $gotNewUrl;
			}
		}

		$gotNewUrl = Request::getVar('urlnew_3');
		if ($gotNewUrl)
		{
			$gotNewUrl = Utility::prepareHostName($gotNewUrl);
			if ($gotNewUrl && $gotNewUrl != '')
			{
				if (!in_array($gotNewUrl, $gotExstUlrNames) && $ulm->isUnique($this->ownerSiteId, $this->ownerOrgId, $gotNewUrl))
					$this->saveNewOptovik($gotNewUrl, $id);
				else
					$notSavedUrls[] = $gotNewUrl;
			}
		}

		// внесем изменения в текущего поставщика
		$curOpt->name = $name;
		$op->save($curOpt);

		$showMessage = "Изменения сохранены";
		if (count($notSavedUrls)) {
			$notSavedUrlsNew = array();
			$opm = new OptovikManager();
			$um = new UserManager();
			// надо найти чей сайт по каждому
			foreach ($notSavedUrls AS $nsuKey => $nsuVal)
			{
				$infoWasAdded = false;
				$ulObj = $ulm->getByUrl($this->ownerSiteId, $this->ownerOrgId, $nsuVal);
				if ($ulObj)
				{
					$optObj = $opm->getById($ulObj->optId);
					if ($optObj)
					{
						$orgObj = $um->getById($optObj->userId);
						if ($orgObj) {
							$notSavedUrlsNew[] = " <a href='/index.php?show=orgviewopt&id={$optObj->id}' target='_blank'>{$nsuVal}</a> (орг: <a href='/index.php?show=vieworg&id={$orgObj->id}' target='_blank'>{$orgObj->nickName}</a>)";
							$infoWasAdded = true;
						}
					}
				}

				if (!$infoWasAdded)
					$notSavedUrlsNew[] = " {$nsuVal}";
			}

			$showMessage = $showMessage . ", кроме: " . implode(',', $notSavedUrlsNew);
		}

		Enviropment::redirect("orgviewopt/id/".$id, $showMessage);

	}

	// сохранение нового оптовика
	private function saveNewOptovik($url, $optId)
	{
		$ulm = new UrlListManager();
		$uobj = new UrlList();
		$uobj->url = $url;
		$uobj->main = Utility::prepareMainUrlPart($url);
		$uobj->optId = $optId;
		$uobj->status = UrlList::STATUS_ENABLED;
		$uobj->ownerSiteId = $this->ownerSiteId;
		$uobj->ownerOrgId = $this->ownerOrgId;
		return $ulm->save($uobj);

	}

}
