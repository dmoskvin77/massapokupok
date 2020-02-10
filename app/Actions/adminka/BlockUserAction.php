<?php
/**
* Действие БО, когда администратор сайта блокирует пользователя
*/
class BlockUserAction extends AdminkaAction
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

		if ($user->entityStatus != User::ENTITY_STATUS_ACTIVE)
			Adminka::redirect("manageusers", "Не подходящий статус");
		
		$user->entityStatus = User::ENTITY_STATUS_BLOCKED;
		$um->save($user);

		Adminka::redirect("manageusers", "Пользователь заблокирован");

	}

}
