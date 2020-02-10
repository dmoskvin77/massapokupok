<?php
/**
 * Контрол форма для назначения встречи
 *
 */

class OrgaddmeetingControl extends AuthorizedOrgControl
{
	public $pageTitle = "Назначение встречи";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		// принимаем id закупки, проверяем того ли она организатора
		$headid = Request::getInt('headid');
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на редактирование закупки");

		$this->addData("zakObj", $zakObj);

	}

}
