<?php
/**
 * Пользователь управляет офисами
 *
 */
class OfficemanagerControl extends AuthorizedUserControl
{
	public $pageTitle = "Управление офисами";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$upm = new UserPermissionsManager();
		$canManageOffices = $upm->getByUserIdAndType($actor->id, UserPermissions::TYPE_OFFICE_MANAGER);
		if (!$canManageOffices)
			Enviropment::redirectBack("Нет прав на управление офисами");

		// список заказов, ожидающих приемки
		$oom = new OfficeOrderManager();
		$orders = $oom->getAllByStatus($this->ownerSiteId, $this->ownerOrgId, OfficeOrder::STATUS_ORG);

		$orgIds = array();
		$userIds = array();
		if (count($orders))
		{
			$this->addData("orders", $orders);

			$om = new OfficeManager();
			$allOffices = $om->getAll($this->ownerSiteId, $this->ownerOrgId);
			$offices = array();
			if (count($allOffices))
			{
				foreach ($allOffices AS $oneoffice)
					$offices[$oneoffice->id] = $oneoffice;

			}

			$this->addData("offices", $offices);

			foreach ($orders AS $officeorder)
			{
				$orgIds[$officeorder->orgId] = $officeorder->orgId;
				$userIds[$officeorder->userId] = $officeorder->userId;
			}

			$um = new UserManager();
			$this->addData("orgs", $um->getByIds($orgIds));
			$this->addData("users", $um->getByIds($userIds));
		}

	}

}
