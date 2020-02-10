<?php
/**
 * Базовый класс для всех действий, которые выполняются в админке.
 * Они все требуют авторизации
 */
class AdminkaAction extends BaseAction implements IPublicAction
{
	public function preExecute()
	{
		parent::preExecute();

		$op = Context::getActor();
		if ($op == null)
			Adminka::redirect("adminkaindex");
			
		// и только те, которым это не запрещено
		$ok = Adminka::checkPermissions();
		if (!$ok)
		{
			SecurityLogManager::write(SecurityLog::TYPE_ACCESS_DENIED);
			Adminka::showAccessDenied();
		}
	}
	
	/**
	 * Это действие нельзя выполнять напрямую
	 *
	 */
	public function execute()
	{
		Adminka::redirect("adminkaindex");
	}
}
