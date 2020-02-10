<?php
/**
 *
 */
class ManageHeadsControl extends BaseAdminkaControl
{
	public function render()
	{
		// сохраним переданные переменные
		$isalive = Request::getInt("isalive");
    	$id = Request::getInt("id");

		if ($isalive == 1) {
            FormRestore::add("heads-filter");
        }

        $zhm = new ZakupkaHeaderManager();
        if ($id) {
            $headsIds[] = $id;
        }
        else {
            $headsIds = $zhm->getAllIds($this->ownerSiteId, $this->ownerOrgId);
        }

		// пейджер
		$perPage = 30;
		$this->addData("perPage", $perPage);
		$this->addData("total", count($headsIds));
		$this->addData("page", Request::getInt("page"));
        $headsIds = FrontPagerControl::limit($headsIds, $perPage, "page");

		if (count($headsIds)) {
            $zakupkaList = $zhm->getByIds($headsIds);
            $this->addData("headsList", $zakupkaList);

            $orgIds = array();
            foreach ($zakupkaList AS $zakupkaItem) {
                $orgIds[] = $zakupkaItem->orgId;
            }

            $orgIds = array_unique($orgIds);
            $um = new UserManager();
            $orgList = $um->getByIds($orgIds);
            $this->addData("orgList", $orgList);
            $this->addData("headStatuses", ZakupkaHeader::getStatusDesc());

        }

	}

}
