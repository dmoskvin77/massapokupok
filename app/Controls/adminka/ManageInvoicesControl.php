<?php
/**
 *
 */
class ManageInvoicesControl extends BaseAdminkaControl
{
	public function render()
	{
		// сохраним переданные переменные
		$isalive = Request::getInt("isalive");
    	$id = Request::getInt("id");

        $filteredStatus = MainCommision::STATUS_NEW;
        $filteredType = null;
		if ($isalive == 1) {
            FormRestore::add("services-filter");
            $filteredStatus = Request::getVar('status');
            if (!$filteredStatus || $filteredStatus == '0') {
                $filteredStatus = MainCommision::STATUS_NEW;
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

        $mcm = new MainCommisionManager();
        if ($id) {
            $ids[] = $id;
        }
        else {
            $idsMain = array();
            $ids = $mcm->getByStatus($filteredStatus, $filteredType, $this->ownerSiteId, $this->ownerOrgId);
            if ($this->ownerSiteId == 1) {
                // выводить надо все счета
                $idsMain = $mcm->getMainOwnerCommissions($filteredStatus, $filteredType);
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
            $commisionList = $mcm->getByIds($ids);
            $this->addData("commList", $commisionList);

            $ownerSiteIds = array();
            $ownerOrgIds = array();
            foreach ($commisionList AS $commItem) {
                $ownerSiteIds[] = $commItem->ownerSiteId;
                $ownerOrgIds[] = $commItem->ownerOrgId;
            }
            $ownerSiteIds = array_unique($ownerSiteIds);
            $ownerOrgIds = array_unique($ownerOrgIds);

            // получим данные по сайтам
            if (count($ownerSiteIds)) {
                $osm = new OwnerSiteManager();
                $ownerSiteObjs = $osm->getByIds($ownerSiteIds);
                $this->addData("ownerSiteObjs", $ownerSiteObjs);
            }

            // и данные по оргам
            if (count($ownerOrgIds)) {
                $oom = new OwnerOrgManager();
                $ownerOrgObjs = $oom->getByIds($ownerOrgIds);
                $this->addData("ownerOrgObjs", $ownerOrgObjs);
            }

        }

        // справочники
        $this->addData("commStatuses",  MainCommision::getStatusDesc());
        $this->addData("commTypes",     MainCommision::getTypeDesc());
        $this->addData("commWays",      MainCommision::getWayDesc());
	}

}
