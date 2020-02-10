<?php 
/**
* Контрол для  создания/редактирования прав пользователя
*/
class EdituserpermissionsControl extends BaseAdminkaControl
{
	public $pageTitle = "Редактирование прав пользователя";
	
	public function render()
	{
		$userId = Request::getInt("userid");
		if (!$userId)
			Adminka::redirect("manageusers", "Не указан ID пользователя");

		$um = new UserManager();
		$user = $um->getById($userId);
		if (!$user)
			Adminka::redirect("manageusers", "Пользователь не найден");

		if ($this->ownerSiteId != $user->ownerSiteId || $this->ownerOrgId != $user->ownerOrgId)
			Adminka::redirect("manageusers", "Нет прав на просмотр пользователя");

		$um = new UserManager();
		$user = $um->getById($userId);
		if (!$user)
			Adminka::redirect("manageusers", "Не найден пользователь");

		$this->addData("user", $user);

		$upm = new UserPermissionsManager();
		$permissions = $upm->getByUserId($userId);
		$this->addData("permissions", $permissions);

		$this->addData("permissionstypes", UserPermissions::getTypeDesc());
	}

}