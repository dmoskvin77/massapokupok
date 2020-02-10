<?php
/**
 * Управление комиссией (подтверждение прихода)
 */
class ManageCommisionControl extends BaseAdminkaControl
{
	public function render()
	{
		// только для главного
		if ($this->ownerSiteId != Configurator::get("application:baseSiteId"))
			Adminka::redirect("", "Нет прав на выполнение выбранного действия");

		$scm = new SiteCommisionManager();
		$list = $scm->getByOrgId();

		// орги
		$orgIds = array();
		if (count($list))
		{
			foreach ($list AS $oneScObj)
				$orgIds[] = $oneScObj->orgId;

			$um = new UserManager();
			$orgList = $um->getByIds($orgIds);

			$this->addData("list", $list);
			$this->addData("orgList", $orgList);

			// статусы
			$this->addData("scStatuses", SiteCommision::getStatusDesc());
			$this->addData("scTypes", SiteCommision::getTypeDesc());
			$this->addData("scWays", SiteCommision::getWayDesc());

		}

	}

}
