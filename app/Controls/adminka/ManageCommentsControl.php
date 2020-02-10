<?php
/**
 * Контрол для представления формы управления закупками (модерация)
 * 
 */
class ManageCommentsControl extends BaseAdminkaControl
{
	public function render()
	{
		$zhcomm = new ZhCommentManager();
		$commlist = $zhcomm->getAllNew($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("commlist", $commlist);
	}
}
