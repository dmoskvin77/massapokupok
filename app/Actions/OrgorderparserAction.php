<?php
/**
* Запрос на создание парсера
*
*/
class OrgorderparserAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
 		$id = FilterInput::add(new StringFilter("id", false, "ID оптовика"));
		$urlid = FilterInput::add(new StringFilter("urlid", true, "ID сайта"));

		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		// текущий поставщик
		$op = new OptovikManager();
		$ulm = new UrlListManager();

		$gotOneUrl = $ulm->getById($urlid);
		if (!$gotOneUrl)
			Enviropment::redirectBack("Не найдена ссылка {$urlid}");

		if ($this->ownerSiteId != $gotOneUrl->ownerSiteId || $this->ownerOrgId != $gotOneUrl->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$gotOptovik = $op->getById($gotOneUrl->optId);
		if (!$gotOptovik)
			Enviropment::redirectBack("Не найден поставщик");

		if ($gotOptovik->userId != $orgId || $this->ownerSiteId != $gotOptovik->ownerSiteId || $this->ownerOrgId != $gotOptovik->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$gotOneUrl->parseRequest = 1;
		$ulm->save($gotOneUrl);

		// выставим счет на создание парсера организатору
		$scm = new SiteCommisionManager();
		// поиск уже существующего счета
		$stComObj = $scm->getByHeadId($urlid, SiteCommision::TYPE_ADDPARSER);
		// счет добавляется только один раз
		if (!$stComObj)
		{
			$stComObj = new SiteCommision();
			$stComObj->orgId = $orgId;
			$stComObj->headId = $urlid;
			$stComObj->dateCreate = time();
			$stComObj->type = SiteCommision::TYPE_ADDPARSER;
			$stComObj->status = SiteCommision::STATUS_NEW;
			$stComObj->ownerSiteId = $this->ownerSiteId;
			$stComObj->ownerOrgId = $this->ownerOrgId;
			$stComObj->needAmount = intval(Configurator::get("application:parserPrice"));
			$scm->save($stComObj);
		}

		Enviropment::redirectBack("Заказ на создание парсера создан, выставлен счет");

	}

}
