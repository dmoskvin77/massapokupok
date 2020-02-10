<?php
/**
 * Действие БО для сохранения прав пользователя
 */
class SaveuserpermissionsAction extends AdminkaAction
{
	public function execute()
	{
		$userId = FilterInput::add(new IntFilter("userid", true, "id"));
		$permission = FilterInput::add(new StringFilter("permission", true, "Права"));

		if (!FilterInput::isValid())
		{
			FormRestore::add("form");
			Adminka::redirect("manageusers", FilterInput::getMessages());
		}

		$um = new UserManager();
		$user = $um->getById($userId);
		if (!$user)
			Adminka::redirect("manageusers", "Пользователь не найден");

		if ($this->ownerSiteId != $user->ownerSiteId || $this->ownerOrgId != $user->ownerOrgId)
			Adminka::redirect("manageusers", "Нет прав на просмотр пользователя");

		$upm = new UserPermissionsManager();
		$permissions = $upm->getByUserId($userId);

		$checkPermission = UserPermissions::getTypeDesc($permission);
		if (!$checkPermission)
			Adminka::redirect("manageusers", "Выбранных прав не существует");

		$isAlreadyExists = false;
		if (count($permissions))
		{
			foreach ($permissions AS $onepermission)
			{
				if ($onepermission->type == $permission)
				{
					$isAlreadyExists = true;
					break;
				}
			}
		}

		if (!$isAlreadyExists)
		{
			$permissionObject = new UserPermissions();
			$permissionObject->userId = $userId;
			$permissionObject->type = $permission;
			$permissionObject->ownerSiteId = $this->ownerSiteId;
			$permissionObject->ownerOrgId = $this->ownerOrgId;
			$upm->save($permissionObject);
			Adminka::redirect("manageusers", "Права добавлены");
		}
		else
		{
			Adminka::redirect("manageusers", "Права были добавлены ранее");
		}

	}

}