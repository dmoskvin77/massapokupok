<?php
/**
 * Контрол счета за ведения закупок
 *
 */

class OrgcommisionControl extends AuthorizedOrgControl
{
	public $pageTitle = "Коммиссия сайту за ведение закупок";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$scm = new SiteCommisionManager();

		$mode = Request::getVar("mode");
		$this->addData("mode", $mode);

		$curPage = Utils::getCurrenControlName();
		$this->addData("curPage", $curPage);

		$gotError = Request::getInt("error");
		if ($gotError == 1)
			Context::setError("Оплата не была произведена");

		$gotOk = Request::getInt("ok");
		if ($gotOk == 1)
			Context::setError("Оплата произведена, обновите страницу для проверки статуса счёта");

		if ($mode == 'pay')
		{
			// уведомлено об оплате
			$scList = $scm->getByOrgId($actor->id, SiteCommision::STATUS_NEW, false);
		}
		else if ($mode == 'confirm')
		{
			// оплата подтверждена
			$scList = $scm->getByOrgId($actor->id, SiteCommision::STATUS_SUCCED, false);
		}
		else
		{
			// новые счета организатора (новые, не оплаченные)
			$scList = $scm->getByOrgId($actor->id, SiteCommision::STATUS_NEW, true);
		}

		if (count($scList))
		{
			$this->addData("scList", $scList);
			$zhIds = array();
			foreach ($scList AS $oneScObj) {
				if ($oneScObj->type == SiteCommision::TYPE_ZAK)
					$zhIds[] = $oneScObj->headId;
			}

			// так же отправим во вьюшку расшифровку констант сущности
			$this->addData("scStatuses", SiteCommision::getStatusDesc());
			$this->addData("scTypes", SiteCommision::getTypeDesc());
			$this->addData("scWays", SiteCommision::getWayDesc());

			if (count($zhIds))
			{
				// а так же нужны ещё закупки, по которым нашлись счета
				$zhm = new ZakupkaHeaderManager();
				$zheads = $zhm->getByIds($zhIds);
				$this->addData("zheads", $zheads);
			}

		}

	}

}
