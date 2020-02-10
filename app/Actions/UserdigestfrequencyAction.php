<?php
/**
 * Сохранение информации об оплате заказа
 *
 * oheadid - orderHead id
 *
*/

class UserdigestfrequencyAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$freq = FilterInput::add(new IntFilter("freq", true, "Частота рассылки дайджеста"));
		if (!FilterInput::isValid())
		{
			FormRestore::add("user-freq");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		$actor = $this->actor;;

		$um = new UserManager();
		$userObj = $um->getById($actor->id);
		$userObj->digestFrequency = $freq;
		$um->save($userObj);

		Enviropment::redirect("userarea" ,"Мы уважаем Ваш выбор. Спасибо за то, что Вы с нами!");

	}

}
