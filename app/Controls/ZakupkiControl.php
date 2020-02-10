<?php
/**
* Контрол покажет список закупок
*/
class ZakupkiControl extends IndexControl
{
	public $p = 0;

	public function render()
	{
		$this->controlName = "Zakupki";

		if ($this->actor)
			$this->addData("actorid", $this->actor);

		$shouldLogin = false;
		if (!$this->actor && SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'zakseeonlymembers') == 'on')
			$shouldLogin = true;

		$mode = Request::getVar("mode");
		$this->addData("mode", $mode);

		if ($mode == 'done' && SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'shownabraniyezakupki') == 'on')
			$shouldLogin = false;

		if ($shouldLogin)
			Enviropment::redirect("userlogin", "Необходимо авторизоваться");

		$cat = Request::getVar("cat");
		$this->addData("cat", $cat);

		$categories = array();
		$cm = new CategoryManager();
		$allCat = $cm->getAll($this->ownerSiteId, $this->ownerOrgId);
		if (count($allCat))
		{
			foreach ($allCat AS $oneCat)
				$categories[$oneCat->id] = mb_strtolower($oneCat->name, 'utf8');

			$this->addData("categories", $categories);
		}

		if ($cat && count($categories) && isset($categories[$cat]))
			$this->pageTitle = "Совместные покупки ".mb_strtolower($categories[$cat], 'utf8')." ".SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city");
		else
			$this->pageTitle = "Закупки сайт покупок ".SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city");

		$zhm = new ZakupkaHeaderManager();
		$newZHList = $zhm->getMainModeratedIds($this->ownerSiteId, $this->ownerOrgId, $mode, $cat);
		if (count($newZHList) > 0)
		{
			// включим пейджер
			$total = count($newZHList);
			$this->addData("total", $total);
			$p = Request::getVar("p");
			$this->addData("p", $p);
			if ($p > 0)
				$this->p = $p;

			$perPage = 16;
			$this->addData("perPage", $perPage);
			$outArray = FrontPagerControl::limit($newZHList, $perPage, "p");
			$newZHObjects = $zhm->getByIds($outArray);

			if (count($newZHObjects))
			{
				$zvm = new ZakupkaVoteManager();
				$dbVotes = $zvm->getByHeadIds($outArray);
                $votes = array();
                if (count($dbVotes)) {
                    foreach ($dbVotes AS $dbVote) {
                        if (isset($votes[$dbVote->headId]))
                            $votes[$dbVote->headId] = $votes[$dbVote->headId] + 1;
                        else
                            $votes[$dbVote->headId] = 1;
                    }
                    $this->addData("votes", $votes);
                }

				if ($this->actor) {
                    $dbVotes = $zvm->getByHeadIdsAndUserId($outArray, $this->actor->id);
                    $votes = array();
                    if (count($dbVotes)) {
                        foreach ($dbVotes AS $dbVote) {
                            if (isset($votes[$dbVote->headId]))
                                $votes[$dbVote->headId] = $votes[$dbVote->headId] + 1;
                            else
                                $votes[$dbVote->headId] = 1;
                        }
                        $this->addData("myvotes", $votes);
                    }
				}

				$orgIds = array();
				foreach ($newZHObjects AS $oneZH) {
					$orgIds[] = $oneZH->orgId;
				}

				if (count($orgIds))
				{
					$um = new UserManager();
					$orgList = $um->getByIds($orgIds);
					if (count($orgList))
					{
						$orgNames = array();
						foreach ($orgList AS $oneOrg)
							$orgNames[$oneOrg->id] = $oneOrg->nickName;

						$this->addData("orgNames", $orgNames);
					}
				}

				$this->addData("zhlist", $newZHObjects);
			}

			$this->addData("tabletitle", "Новые закупки");

		}

		// хлебные крошки вверху страницы
		$allCrumbs = array();
		// $allCrumbs[] = array("link" => "/", "name" => "Главная", "title" => "");
		$this->Crumbs = $allCrumbs;

	}

}
