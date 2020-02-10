<?php
/**
 * Контрол для представления формы управления пользователями
 * 
 */
class ManageUsersControl extends BaseAdminkaControl
{
	public function render()
	{
		// сохраним переданные переменные
		$isalive = Request::getInt("isalive");
		$mode = Request::getVar("mode");
		$id = Request::getInt("id");
		$login = Request::getVar("login");
		$nickName = Request::getVar("nickName");

		// если не заполнили основные поля формы
		// 1 - выключен, 2 - включен
		if ($id == 0 && $login == '' && $nickName == '')
			$basicfilter = 1;
		else
			$basicfilter = 2;

		// свернем переменные фильтра в массив
		$sendArray = compact("mode", "id", "basicfilter", "login", "nickName");

		if ($isalive == 1)
			FormRestore::add("users-filter");

		// получим список id пользовалтелей по фильтру
		$um = new UserManager();
		$userIds = $um->getFilteredUserIds($this->ownerSiteId, $this->ownerOrgId, $sendArray);

		// пейджер
		$perPage = 30;
		$this->addData("perPage", $perPage);
		$this->addData("total", count($userIds));
		$this->addData("page", Request::getInt("page"));
		$userIds = FrontPagerControl::limit($userIds, $perPage, "page");

		if ($userIds)
			$this->addData("userList", $um->getByIds($userIds));

	}

}
