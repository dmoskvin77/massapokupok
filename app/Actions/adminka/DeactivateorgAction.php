<?php
/**
* Действие БО для перевода орга в участники
*/
class DeactivateorgAction extends AdminkaAction
{
	public function execute()
	{
		$userId = Request::getInt("id");
		if (!$userId)
			Adminka::redirect("manageusers", "Не задан ID пользователя");
	
		$um = new UserManager();
		$user = $um->getById($userId);
		if (!$user)
			Adminka::redirect("manageusers", "Пользователь не найден");

		if ($this->ownerSiteId != $user->ownerSiteId || $this->ownerOrgId != $user->ownerOrgId)
			Adminka::redirect("manageusers", "Нет прав на выполнение данного действия");

		$user->isOrg = 0;
		$user->requestOrg = 0;
		$um->save($user);

		Adminka::redirect("manageusers", "Орг стал участником");

	}

}
