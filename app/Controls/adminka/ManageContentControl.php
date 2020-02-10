<?php
/**
 * Контрол для просмотра списка страниц CMS
 */
class ManageContentControl extends BaseAdminkaControl
{
	public function render()
	{
		$cm = new ContentManager();
		$list = $cm->getList($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("list", $list);
		$menuDesc = Content::getMenuDesc();
		$this->addData("menuTypeList", $menuDesc);

	}	
}
