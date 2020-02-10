<?php
/**
 * Контрол рассылка участникам закупки
 *
 */

class OrgbroadcastControl extends AuthorizedOrgControl
{
	public $pageTitle = "Рассылка участникам";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// всё хорошо, покажем форму рассылки
		$this->addData("headid", $headid);
		$this->addData("zakObj", $zakObj);

	}

}
