<?php
/**
* Контрол покажет список типов объявлений барахолки
*/
class BoardControl extends IndexControl
{
	public $p = 0;

	public function render()
	{
		$this->controlName = "Board";

		$btm = new BoardTypeManager();
		$typeAliases = array();
		$gotTypeId = null;

		$bcm = new BoardCategoryManager();
		$catAliases = array();
		$gotCatId = null;

		$allTypes = $btm->getAllActive($this->ownerSiteId, $this->ownerOrgId);

		$preparedTypes = array();
		if (count($allTypes))
		{
			foreach ($allTypes AS $oneType)
				$preparedTypes[$oneType->id] = $oneType->name;
		}

		$this->addData("preparedTypes", $preparedTypes);

		// выбранный тип и категория объявления (2 в 1, разделитель -)
		$adAlias = Request::getVar("ya");
		if ($adAlias)
		{
			// смотрим какая выбрана тип и категория
			// получим массив алиасов типов
			if (count($allTypes))
			{
				foreach ($allTypes AS $oneType)
				{
					$typeAliases[$oneType->id] = $oneType->alias;
					if (strpos($adAlias, $oneType->alias) !== false)
					{
						$gotTypeId = $oneType->id;
						$this->pageTitle = "Частные объявление раздел: ".mb_strtolower($oneType->name, 'utf8')." (".SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city").")";
					}
				}
			}

			if ($gotTypeId)
			{
				// и категорий
				$allCats = $bcm->getAllActive($this->ownerSiteId, $this->ownerOrgId, $gotTypeId);
				if (count($allCats))
				{
					foreach ($allCats AS $oneCategory)
					{
						$catAliases[$oneCategory->id] = $oneCategory->alias;
						if (strpos($adAlias, $oneCategory->alias) !== false)
						{
							$gotCatId = $oneCategory->id;
						}
					}
				}
			}
		}
		else
			$this->pageTitle = "Частные объявления: продам, куплю, обменяю, пристрой совместных покупок ".SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city");

		$this->addData("typeId", $gotTypeId);
		$this->addData("catId", $gotCatId);

		// алиасы (массив)
		$this->addData("typeAliases", $typeAliases);
		$this->addData("catAliases", $catAliases);

		$this->addData("boardTypes", $allTypes);

		// все подкатегории
		$boardCats = $bcm->getAllActive($this->ownerSiteId, $this->ownerOrgId);

		$preparedCats = array();
		if (count($boardCats))
		{
			foreach ($boardCats AS $oneCat)
				$preparedCats[$oneCat->id] = $oneCat->name;
		}

		$this->addData("preparedCats", $preparedCats);

		// категории объявлений насаживаем на типы
		if (count($allTypes) && count($boardCats))
		{
			$preparedCategories = array();
			foreach ($boardCats AS $oneCategory)
			{
				if (!isset($preparedCategories[$oneCategory->boardTypeId]))
					$preparedCategories[$oneCategory->boardTypeId] = array();

				$preparedCategories[$oneCategory->boardTypeId][] = $oneCategory;
			}

			if (count($preparedCategories))
				$this->addData("preparedCategories", $preparedCategories);

		}

		$bam = new BoardAdManager();

		// если указан id объявления
		$actor = $this->actor;
		$id = Request::getInt("id");
		if ($id)
		{
			$adObj = $bam->getById($id);
			if (!$adObj)
				Enviropment::redirect("board", "Объявление не найдено");

			if ($this->ownerSiteId != $adObj->ownerSiteId || $this->ownerOrgId != $adObj->ownerOrgId)
				Enviropment::redirect("board", "Нет прав на выполнение данного действия");

			if ($actor && $adObj->userId != $actor->id && $adObj->status != BoardAd::STATUS_ACTIVE)
				Enviropment::redirect("board", "Объявление не активно");

			if (!$actor && $adObj->status != BoardAd::STATUS_ACTIVE)
				Enviropment::redirect("board", "Объявление не активно");

			$this->pageTitle = "";
			if (isset($preparedTypes[$adObj->typeId]))
				$this->pageTitle = $this->pageTitle.$preparedTypes[$adObj->typeId].": ";

			$this->pageTitle = $this->pageTitle.mb_strtolower($adObj->name, 'utf8');

			if (round($adObj->price) > 0)
				$this->pageTitle = $this->pageTitle." Цена ".round($adObj->price)." р. ";

			$this->pageTitle = $this->pageTitle." (частное объявление ".SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city").")";

			$this->addData("adObj", $adObj);
			$this->addData("adStatuses", BoardAd::getStatusDesc());

			$um = new UserManager();
			$user = $um->getById($adObj->userId);
			$this->addData("user", $user);

		}
		else
		{
			// список активных объявлений по выбранной категории
			if ($gotTypeId)
			{
				$badList = $bam->getTypeCatId($gotTypeId, $gotCatId);
				$this->addData("badList", $badList);

				$userIds = array();
				if (count($badList))
				{
					foreach ($badList AS $oneBad)
						$userIds[$oneBad->userId] = $oneBad->userId;
				}

				if (count($userIds))
				{
					$um = new UserManager();
					$users = $um->getByIds($userIds);
					if (count($users))
					{
						$userNames = array();
						foreach ($users AS $oneUser)
							$userNames[$oneUser->id] = $oneUser;

						$this->addData("userNames", $userNames);

					}

				}

			}

		}

		// объявление актора показывать надо вне зависимости от статуса
		$this->addData("actor", $actor);

	}

}
