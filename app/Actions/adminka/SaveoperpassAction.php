<?php
/**
 * Действие БО для сохранения пароля оператора
 * 
 */
class SaveoperpassAction extends AdminkaAction
{
	public function execute()
	{
		$newpass = FilterInput::add(new StringFilter("newpass", true, "Новый пароль"));
		if (!FilterInput::isValid())
			Adminka::redirect("operatorpassword", FilterInput::getMessages());

		$password = md5(md5($newpass).md5($newpass));
		$op = Context::getActor();
		$opm = new OperatorManager();
		$oper = $opm->getById($op->id);
		if ($oper)
		{
			if ($this->ownerSiteId != $oper->ownerSiteId || $this->ownerOrgId != $oper->ownerOrgId)
				Adminka::redirect("mainpage", "Нет прав на выполнение данной операции");

			$oper->password = $password;
			$opm->save($oper);
			Adminka::redirect("mainpage", "Пароль изменен");
		}
		else
		{
			Adminka::redirect("mainpage", "Не задан оператор");
		}
	}
}
