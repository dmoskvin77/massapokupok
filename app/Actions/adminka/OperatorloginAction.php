<?php
/**
 * Действие БО для входа оператора в админку
 *
 */
class OperatorloginAction extends BaseAction
{
	public function execute()
	{
		$username = Request::getVar("email");
		$password = Request::getVar("password");

		$password = md5(md5($password).md5($password));

		$opm = new OperatorManager();
		$op = $opm->getOne(new SQLCondition("login = '{$username}' AND password = '{$password}' AND ownerSiteId = {$this->ownerSiteId} AND ownerOrgId = {$this->ownerOrgId}"));
		if (!$op)
		{
			// пытаемся задетектить брутфорс
			SecurityLogManager::detectPasswordBrutforce();
			FormRestore::add("operator-login");
			Adminka::redirectBack("Неправильное имя пользователя или пароль");
		}
		else
		{
			Context::setActor($op);
			// при нормальном входе очистим счетчик
			SecurityLogManager::clearPasswordBrutforce();
			Adminka::redirect("mainpage");
		}
	}
}
