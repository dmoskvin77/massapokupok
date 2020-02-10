<?php
/**
 * Контрол для представления формы управления офисами раздач
 * 
 */
class ManageOfficesControl extends BaseAdminkaControl
{
	public function render()
	{
		$ofm = new OfficeManager();
		$list = $ofm->getAll($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("offices", $list);
	}
}
