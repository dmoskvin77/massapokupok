<?php
/**
 * Парсеры поставщика
 *
 */
class OrgParsersControl extends AuthorizedOrgControl
{
	public $pageTitle = "Парсеры поставщика";

	public function render()
	{
		$actor = $this->actor;
		$optId = Request::getInt("id");
		if (!$optId)
			Enviropment::redirectBack("Не задан ID поставщика");

		$op = new OptovikManager();
		$curOpt = $op->getById($optId);
		if (!$curOpt)
			Enviropment::redirectBack("Поставщик не найден");

		if ($curOpt->userId != $actor->id || $this->ownerSiteId != $curOpt->ownerSiteId || $this->ownerOrgId != $curOpt->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		// всё нормально
		$this->addData("optovik", $curOpt);

		// поднимаем список сайтов
		$ulm =  new UrlListManager();
		$urlList = $ulm->getByOptovikEstParser($curOpt->id);
		$this->addData("urlListEstParser", $urlList);

		$urlList = $ulm->getByOptovikNoParser($curOpt->id);
		$this->addData("urlListNoParser", $urlList);

		$um = new UserManager();
		$gotOrg = $um->getById($curOpt->userId);
		$this->addData("org", $gotOrg);

	}

}
