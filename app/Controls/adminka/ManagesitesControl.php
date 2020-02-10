<?php
/**
 * Контрол для представления формы управления сайтами
 * 
 */
class ManagesitesControl extends BaseAdminkaControl
{
	public function render()
	{
		if ($this->ownerSiteId != 1) {
			Adminka::redirectBack("Нет прав на выполнение данного действия");
		}

		$osm = new OwnerSiteManager();
		$ownerSitesList = $osm->getAll();
        $this->addData("sites", $ownerSitesList);

		$oom = new OwnerOrgManager();
        $ownerOrgList = $oom->getAll();
        $this->addData("orgs", $ownerOrgList);

	}
}
