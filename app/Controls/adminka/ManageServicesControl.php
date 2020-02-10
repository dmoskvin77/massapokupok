<?php
/**
 *
 */
class ManageServicesControl extends BaseAdminkaControl
{
	public function render()
	{
		// сохраним переданные переменные
		$isalive = Request::getInt("isalive");
    	$id = Request::getInt("id");

        $filteredStatus = SiteCommision::STATUS_NEW;
        $filteredType = null;
		if ($isalive == 1) {
            FormRestore::add("services-filter");
            $filteredStatus = Request::getVar('status');
            if (!$filteredStatus || $filteredStatus == '0') {
                $filteredStatus = SiteCommision::STATUS_NEW;
            }
            else {
                $this->addData("defStatus", $filteredStatus);
            }
            $filteredType = Request::getVar('type');
            if (!$filteredType || $filteredType == '0') {
                $filteredType = null;
            }
            else {
                $this->addData("defType", $filteredType);
            }
        }

        $scm = new SiteCommisionManager();
        if ($id) {
            $ids[] = $id;
        }
        else {
            $idsMain = array();
            $ids = $scm->getByStatus($filteredStatus, $filteredType, $this->ownerSiteId, $this->ownerOrgId);
            if ($this->ownerSiteId == 1) {
                // выводить надо не только счета за комиссию, но и счета на подключение и т.д.
                $idsMain = $scm->getMainOwnerCommissions($filteredStatus, $filteredType);
            }
            $ids = array_merge($ids, $idsMain);
        }

		// пейджер
		$perPage = 30;
		$this->addData("perPage", $perPage);
		$this->addData("total", count($ids));
		$this->addData("page", Request::getInt("page"));
        $ids = FrontPagerControl::limit($ids, $perPage, "page");
		if (count($ids)) {
            $commisionList = $scm->getByIds($ids);
            $this->addData("commList", $commisionList);
            $orgIds = array();
            $headIds = array();
            foreach ($commisionList AS $commisionItem) {
                $orgIds[]  = $commisionItem->orgId;
                if ($commisionItem->headId) {
                    $headIds[] = $commisionItem->headId;
                }
            }
            $orgIds = array_unique($orgIds);
            $headIds = array_unique($headIds);
            $um = new UserManager();
            $orgList = $um->getByIds($orgIds);
            if (count($orgList)) {
                $this->addData("orgList", $orgList);
            }
            $zhm = new ZakupkaHeaderManager();
            $zakList = $zhm->getByIds($headIds);
            if (count($zakList)) {
                $this->addData("zakList", $zakList);
            }
        }

        // справочники
        $this->addData("commStatuses",  SiteCommision::getStatusDesc());
        $this->addData("commTypes",     SiteCommision::getTypeDesc());
        $this->addData("commWays",      SiteCommision::getWayDesc());
	}

}
