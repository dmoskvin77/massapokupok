<?php
/**
 * Действие БО для входа оператора в админку
 *
 */
class OperatorlogoutAction extends AdminkaAction
{
	public function execute()
	{
		Context::logOff();
		sleep(1);
		Adminka::redirect("mainpage");

	}
}
