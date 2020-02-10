<?php
/**
 * Контрол для просмотра списка категорий
 */
class ManageCategoriesControl extends BaseAdminkaControl
{
	public function render()
	{
		$cm = new CategoryManager();
		$list = $cm->getAll($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("list", $list);

	}	
}
