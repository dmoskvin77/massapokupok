<?php
/**
 * Контрол для представления формы управления объявлениями
 * 
 */
class ManageboardControl extends BaseAdminkaControl
{
	public function render()
	{
		$bam = new BoardAdManager();
		$boardadlist = $bam->getByStatus($this->ownerSiteId, $this->ownerOrgId, BoardAd::STATUS_NEW);
		$userIds = array();
		if (count($boardadlist))
		{
			foreach ($boardadlist AS $oneBoardAd)
				$userIds[] = $oneBoardAd->userId;
		}

		$this->addData("boardadlist", $boardadlist);

		if (count($userIds))
		{
			$um = new UserManager();
			$users = $um->getByIds($userIds);
			$this->addData("users", $users);
		}
	}
}
