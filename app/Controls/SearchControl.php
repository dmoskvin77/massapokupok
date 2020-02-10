<?php
/**
* Поиск, который будет развиваться
*/
class SearchControl extends IndexControl
{
	public $pageTitle = "Поиск участника";

	public function render()
	{
		$q = Request::getVar('q');
		if (!$q || $q == '')
			Enviropment::redirectBack("Поисковый запрос был слишком пустым");

		$this->addData("q", $q);

		// поищем пользователей
		$um = new UserManager();
		$users = $um->searchUsers($this->ownerSiteId, $this->ownerOrgId, $q, 5);
		if ($users)
			$this->addData("users", $users);

		$ts = time();
		$this->addData("ts", $ts);

	}

}
