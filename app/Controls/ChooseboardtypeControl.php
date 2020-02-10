<?php

class ChooseboardtypeControl extends AuthorizedUserControl
{
	public $pageTitle = "Добавить объявление - выбор типа объявления";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);

		// если указан id объявления для редактировния
		$id = FilterInput::add(new IntFilter("id", true, "ID объявления"));
		if ($id)
		{
			$bam = new BoardAdManager();
			$adObj = $bam->getById($id);
			if (!$adObj)
				Enviropment::redirectBack("Не найдено объявление");

			$actor = Context::getActor();
			if ($adObj->userId != $actor->id || $this->ownerSiteId != $adObj->ownerSiteId || $this->ownerOrgId != $adObj->ownerOrgId)
				Enviropment::redirectBack("Нет прав на выполнение данного действия");

			$this->addData("adObj", $adObj);
		}

		// дадим выбрать тип
		$btm = new BoardTypeManager();
		$allTypes = $btm->getAllActive($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("boardTypes", $allTypes);

		// и категорию объявления
		// все подкатегории
		$bcm = new BoardCategoryManager();
		$boardCats = $bcm->getAllActive($this->ownerSiteId, $this->ownerOrgId);
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

	}

}
