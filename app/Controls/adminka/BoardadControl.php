<?php
/**
 * Контрол для представления объявления в админке
 * 
 */
class BoardadControl extends BaseAdminkaControl
{
	public function render()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("manageboard", "Не указан ID объявления");

		$bam = new BoardAdManager();
		$boardad = $bam->getById($id);
		if (!$boardad)
			Adminka::redirect("manageboard", "Не найдено объявление");

		if ($this->ownerSiteId != $boardad->ownerSiteId || $this->ownerOrgId != $boardad->ownerOrgId)
			Adminka::redirect("manageboard", "Не найдено объявление");

		$this->addData("boardad", $boardad);

		$um = new UserManager();
		$user = $um->getById($boardad->userId);
		$this->addData("user", $user);

		// типы объявления
		$btm = new BoardTypeManager();
		$boardTypes = $btm->getAllActive($this->ownerSiteId, $this->ownerOrgId);
		$preparedTypes = array();
		if (count($boardTypes))
		{
			foreach ($boardTypes AS $oneType)
				$preparedTypes[$oneType->id] = $oneType->name;
		}

		$this->addData("boardtypes", $preparedTypes);

		// категории
		$bcm = new BoardCategoryManager();
		$boardCats = $bcm->getAllActive($this->ownerSiteId, $this->ownerOrgId);
		$preparedCats = array();
		if (count($boardCats))
		{
			foreach ($boardCats AS $oneCat)
				$preparedCats[$oneCat->id] = $oneCat->name;
		}

		$this->addData("boardcats", $preparedCats);

	}
}
