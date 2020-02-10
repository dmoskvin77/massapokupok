<?php 
/**
* Контрол для редактирования пользователя в админке
*/
class EdituserControl extends BaseAdminkaControl
{
	public $pageTitle = "Редактирование пользователя";
	
	public function render()
	{
		$userId = Request::getInt("userid");
		if (!$userId)
			Adminka::redirectBack("Не указан ID пользователя");

        $um = new UserManager();
        $userObj = $um->getById($userId);
        if (!$userObj)
            Adminka::redirectBack("Не найден заданный пользователь");

        if ($this->ownerSiteId != $userObj->ownerSiteId || $this->ownerOrgId != $userObj->ownerOrgId)
            Adminka::redirectBack("Нет прав на выполнение действия");

        $this->addData("user", $userObj);

	}
}