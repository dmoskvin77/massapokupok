<?php
/**
 * Контрол для представления формы управления новостями
 * 
 */
class ManageNewsControl extends BaseAdminkaControl
{
	public function render()
	{
		$nm = new NewsManager();
		$list = $nm->getAll($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("news", $list);
	}
}
