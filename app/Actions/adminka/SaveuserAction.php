<?php
/**
 * Действие БО для сохранения новости
 */
class SaveuserAction extends AdminkaAction
{
	public function execute()
	{
		$userId = FilterInput::add(new IntFilter("userId", false, "id"));
        $orgPersent = Request::getVar("orgPersent");

        if (!$userId)
            Adminka::redirectBack("Не указан ID пользователя");

        $um = new UserManager();
        $userObj = $um->getById($userId);
        if (!$userObj)
            Adminka::redirectBack("Не найден заданный пользователь");

        if ($this->ownerSiteId != $userObj->ownerSiteId || $this->ownerOrgId != $userObj->ownerOrgId)
            Adminka::redirectBack("Нет прав на выполнение действия");

        $userObj->orgPersent = $orgPersent;
        $userObj = $um->save($userObj);
        Adminka::redirect("manageusers", "Данные сохранены");

	}

}