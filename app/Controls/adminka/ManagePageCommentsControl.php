<?php
/**
 * Контрол для представления формы управления каметами
 * 
 */
class ManagePageCommentsControl extends BaseAdminkaControl
{
	public function render()
	{
		$cocomm = new CoCommentManager();
		$commlist = $cocomm->getAllNew($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("commlist", $commlist);
	}
}
