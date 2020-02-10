<?php
/**
 * Подписка на регулярную закупку (выкуп)
 *
*/

class UsersubsribezakupkaAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$vikupId = FilterInput::add(new IntFilter("id", true, "ID выкупа"));
		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		$actor = Context::getActor();
		$vm = new ZakupkaVikupManager();
		$vikupObj = $vm->getById($vikupId);
		if (!$vikupObj)
			Enviropment::redirectBack("Не найден выкуп регулярной закупки");

		if ($this->ownerSiteId != $vikupObj->ownerSiteId || $this->ownerOrgId != $vikupObj->ownerOrgId)
			Enviropment::redirectBack("Не найден выкуп регулярной закупки");

		// подписываем
		$vsm = new VikupSubscribersManager();
		$vsm->subscribeToVikup($this->ownerSiteId, $this->ownerOrgId, $vikupId, $actor->id);

		Enviropment::redirectBack("Вы подписались на уведомления о регулярно проводимой закупке");

	}

}
