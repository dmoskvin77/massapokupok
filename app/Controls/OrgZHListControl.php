<?php
/**
 * Контрол для просмотра списка закупок (заголовков)
 *
 */
class OrgZHListControl extends AuthorizedOrgControl
{
	public $pageTitle = "Просмотр списка закупок";

	public function render()
	{
		$actor = $this->actor;
		$zhm = new ZakupkaHeaderManager();

		$zhlist = $zhm->getAllGroupedStatus($actor->id);
		$this->addData("zhlist", $zhlist);
		$this->addData("stdesc", ZakupkaHeader::getStatusDesc());

		$vm = new ZakupkaVikupManager();
		$vikupList = $vm->getByOrgId($actor->id);
		if (count($vikupList))
			$this->addData("vikupList", $vikupList);

	}
}
