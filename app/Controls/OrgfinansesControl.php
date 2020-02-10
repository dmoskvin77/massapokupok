<?php
/**
 * Контрол финансы организатора
 *
 */

class OrgfinansesControl extends AuthorizedOrgControl
{
	public $pageTitle = "Оплаты участников";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		// подключен ли единый кошелек
		$pum = new PurseManager();
		$purseObj = $pum->getByOrgId($actor->id);
		if (!$purseObj) {
			$this->addData("noW1Purse", true);
		}

		// яндекс кнопка оплаты
        $ybm = new YakassabuttonManager();
        $yabutObj = $ybm->getByOrgId($actor->id);
        if (!$yabutObj) {
            $this->addData("noYandexKassaButton", true);
        }

		$pm = new PayManager();
		$payObjects = $pm->getIdsByOrgId($actor->id);
		if (count($payObjects)) {
			$zhIds = array();
			$orgIds = array();
			$userIds = array();
			foreach ($payObjects AS $onePayObj)
			{
				$zhIds[] = $onePayObj->headId;
				$orgIds[] = $onePayObj->orgId;
				$userIds[] = $onePayObj->userId;
			}

			$this->addData("payObjects", $payObjects);

			// передадим ещё массив закупок
			$zhm = new ZakupkaHeaderManager();
			$headObjects = $zhm->getByIds($zhIds);
			if ($headObjects)
			{
				$zakData = array();
				foreach ($headObjects AS $oneZak)
					$zakData[$oneZak->id] = $oneZak;

				$this->addData("zakData", $zakData);
			}

			// передадим массив организаторов
			$um = new UserManager();
			$userObjects = $um->getByIds($orgIds);
			if ($userObjects)
			{
				$orgData = array();
				foreach ($userObjects AS $oneOrg)
					$orgData[$oneOrg->id] = $oneOrg;

				$this->addData("orgData", $orgData);
			}

			// а так же передадим простых пользователей
			$userObjects = $um->getByIds($userIds);
			if ($userObjects)
			{
				$userData = array();
				foreach ($userObjects AS $oneUser)
					$userData[$oneUser->id] = $oneUser->nickName;

				$this->addData("userData", $userData);
			}

			// так же отправим во вьюшку расшифровку констант сущности pay
			$this->addData("payStatuses", Pay::getStatusDesc());
			$this->addData("payTypes", Pay::getTypeDesc());
			$this->addData("payWays", Pay::getWayDesc());

		}

	}

}
