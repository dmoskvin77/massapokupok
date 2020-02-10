<?php
/**
 * Контрол для представления формы управления рассылками
 * 
 */
class ManagebroadcastControl extends BaseAdminkaControl
{
	public function render()
	{
		$bcm = new BroadcastManager();
		$broadcastlist = $bcm->getByStatus($this->ownerSiteId, $this->ownerOrgId, Broadcast::STATUS_NEW);

		$userIds = array();
		if (count($broadcastlist))
		{
			foreach ($broadcastlist AS $oneBroadAd)
				$userIds[] = $oneBroadAd->orgId;
		}

		$this->addData("broadcastlist", $broadcastlist);

		if (count($userIds))
		{
			$um = new UserManager();
			$users = $um->getByIds($userIds);
			$this->addData("users", $users);
		}
	}
}
