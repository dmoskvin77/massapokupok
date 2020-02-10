<?php
/**
* Удалить url поставщика
*
*/
class OrgremoveopturlAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
 		$id = FilterInput::add(new StringFilter("id", true, "ID ссылки на поставщика"));

		if (!FilterInput::isValid())
		{
			FormRestore::add("opt-edit");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		// текущий поставщик
		$op = new OptovikManager();
		$ulm = new UrlListManager();
		$gotOneUrl = $ulm->getById($id);
		if (!$gotOneUrl)
			Enviropment::redirectBack("Не найдена ссылка {$id}");

		if ($this->ownerSiteId != $gotOneUrl->ownerSiteId || $this->ownerOrgId != $gotOneUrl->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		$gotOptovik = $op->getById($gotOneUrl->optId);
		if (!$gotOptovik)
			Enviropment::redirectBack("Не найден поставщик");

		if ($gotOptovik->userId != $orgId || $this->ownerSiteId != $gotOptovik->ownerSiteId || $this->ownerOrgId != $gotOptovik->ownerOrgId)
			Enviropment::redirectBack("Нет прав на удаление ссылки");

		$gotOneUrl->status = UrlList::STATUS_DISABLED;
		$ulm->save($gotOneUrl);

		Enviropment::redirectBack("Ссылка удалена");

	}

}
