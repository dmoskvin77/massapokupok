<?php
/**
 * Контрол для просмотра списка подсказок
 */
class ManageHintsControl extends BaseAdminkaControl
{
	public function render()
	{
		exit;

		$hm = new HintManager();
		$list = $hm->getAll($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("list", $list);

	}	
}
