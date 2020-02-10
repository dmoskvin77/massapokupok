<?php
/**
* Действие БО для активации пользователя
*/
class ActivateUserAction extends AdminkaAction
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

		if ($user->entityStatus != User::ENTITY_STATUS_NOTACTIVE)
			Adminka::redirect("manageusers", "Не подходящий статус");
		
		$user->entityStatus = User::ENTITY_STATUS_ACTIVE;
		$um->save($user);

		Adminka::redirect("manageusers", "Пользователь активирован");

	}

}
