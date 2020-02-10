<?php
/**
 * Контрол для представления формы управления закупками (модерация)
 * 
 */
class ManageZHControl extends BaseAdminkaControl
{
	public function render()
	{
		$zhm = new ZakupkaHeaderManager();
		// список закупок, ожидающих модерацию
		$zhlist = $zhm->getPending($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("zhlist", $zhlist);

	}

}
