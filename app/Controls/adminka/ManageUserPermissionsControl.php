<?php
/**
 * Контрол для просмотра списка прав пользователей
 */
class ManageUserPermissionsControl extends BaseAdminkaControl
{
	public function render()
	{
		// список надо сгруппировать по пользователям, вывести в таблицу роли пользователей через запятую
		$types = UserPermissions::getTypeDesc();

		// права пользователей
		$userPermissions = array();
		$usersIds = array();
		$upm = new UserPermissionsManager();
		$list = $upm->getAll($this->ownerSiteId, $this->ownerOrgId);
		if (count($list))
		{
			foreach ($list as $onePermission)
			{
				if (!isset($userPermissions[$onePermission->userId]))
				{
					$userPermissions[$onePermission->userId] = array();
				}

				$userPermissions[$onePermission->userId][] = $types[$onePermission->type];
				$usersIds[$onePermission->userId] = $onePermission->userId;
			}

			$this->addData("userPermissions", $userPermissions);

			$um = new UserManager();
			$this->addData("users", $um->getByIds($usersIds));

		}

	}
}
