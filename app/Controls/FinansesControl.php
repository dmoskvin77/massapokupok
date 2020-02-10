<?php
/**
 * Контрол финансы
 *
 */

class FinansesControl extends AuthorizedUserControl
{
	public $pageTitle = "Мои оплаты";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$gotError = Request::getInt("error");
		if ($gotError == 1)
			Context::setError("Оплата не была произведена");

		$gotOk = Request::getInt("ok");
		if ($gotOk == 1)
			Context::setError("Оплата произведена, обновите страницу для проверки статуса счёта");

		$pm = new PayManager();
		$payObjects = $pm->getIdsByUserId($actor->id);
		if (count($payObjects))
		{
			$this->addData("payObjects", $payObjects);

			$zhIds = array();
			$orgIds = array();
			foreach ($payObjects AS $onePayObj)
			{
				$zhIds[] = $onePayObj->headId;
				$orgIds[] = $onePayObj->orgId;
			}

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

			// так же отправим во вьюшку расшифровку констант сущности pay
			$this->addData("payStatuses", Pay::getStatusDesc());
			$this->addData("payTypes", Pay::getTypeDesc());
			$this->addData("payWays", Pay::getWayDesc());

		}

	}

}
