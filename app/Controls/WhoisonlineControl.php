<?php
/**
 * Компонент отображает участников на сайте и сколько их вообще
 */
class WhoisonlineControl extends BaseControl implements IComponent
{
	// рендер
	public function render()
	{
		$um = new UserManager();
		$users = $um->getAllOnline($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("users", $users);
		$this->addData("countOnline", count($users));

		$countUsers = $um->countAllUsers($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("countUsers", $countUsers);

		$ts = time();
		$this->addData("ts", $ts);

	}
}
