<?php 
/**
* Контрол для представления данные пользователя
*/
class UserControl extends BaseAdminkaControl
{
	public $pageTitle = "Работа с пользователем";
	
	public function render()
	{
		$userId = Request::getInt("id");
		if (!$userId)
			Adminka::redirect("manageusers", "Не указан ID пользователя");
		
		$um = new UserManager();
		$user = $um->getById($userId);
		if (!$user)
			Adminka::redirect("manageusers", "Пользователь не найден");

		if ($this->ownerSiteId != $user->ownerSiteId || $this->ownerOrgId != $user->ownerOrgId)
			Adminka::redirect("manageusers", "Нет прав на просмотр пользователя");
		
		$this->addData("user", $user);

		// поднять права пользователя
		$upm = new UserPermissionsManager();
		$permissions = $upm->getByUserId($userId);
		$this->addData("permissions", $permissions);

		$this->addData("permissionstypes", UserPermissions::getTypeDesc());

	}

}
