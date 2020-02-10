<?php
/**
 * Контрол объявления
 *
 */

class MyboardadsControl extends AuthorizedUserControl
{
	public $pageTitle = "Мои объявления";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);
		$this->addData("actorId", $actor->id);

		$bam = new BoardAdManager();
		$adList = $bam->getByUserId($actor->id);
		$this->addData("adList", $adList);

		if (count($adList))
		{
			// описание статусов объявлений
			$this->addData("adStatuses", BoardAd::getStatusDesc());

			// типы объявлений
			$btm = new BoardTypeManager();
			$types = $btm->getAllActive($this->ownerSiteId, $this->ownerOrgId);
			$preparedTypes = array();
			if (count($types)) {
				foreach ($types AS $oneType)
					$preparedTypes[$oneType->id] = $oneType->name;
			}

			$this->addData("preparedTypes", $preparedTypes);

			// категории
			$bcm = new BoardCategoryManager();
			$cats = $bcm->getAllActive($this->ownerSiteId, $this->ownerOrgId);
			$preparedCats = array();
			if (count($cats)) {
				foreach ($cats AS $oneCat)
					$preparedCats[$oneCat->id] = $oneCat->name;
			}

			$this->addData("preparedCats", $preparedCats);
		}

	}

}
