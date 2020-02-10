<?php
/**
 * Контрол для просмотра оптовика
 *
 */
class OrgViewOptControl extends AuthorizedOrgControl
{
	public $pageTitle = "Просмотр оптовика";

	public function render()
	{
		$optId = Request::getInt("id");
		if (!$optId)
			Enviropment::redirectBack("Не задан ID поставщика");

		$op = new OptovikManager();
		$curOpt = $op->getById($optId);
		if (!$curOpt)
			Enviropment::redirectBack("Поставщик не найден");

		if ($this->ownerSiteId != $curOpt->ownerSiteId || $this->ownerOrgId != $curOpt->ownerOrgId)
			Enviropment::redirectBack("Поставщик не найден");

		// всё нормально
		$this->addData("optovik", $curOpt);

		// поднимаем список сайтов
		$ulm =  new UrlListManager();
		$urlList = $ulm->getByOptovik($curOpt->id);
		$this->addData("urlList", $urlList);

		$um = new UserManager();
		$gotOrg = $um->getById($curOpt->userId);
		$this->addData("org", $gotOrg);

	}

}
