<?php
/**
 * Контрол для формы входа в БО, в методе preRender проверяется
 * проверка допустимости выполнения к-либо действия текущим юзером
 */

class AuthorizedAdminkaControl extends BaseControl
{
	public $actor;
		
	public function preRender()
	{
		parent::preRender();

		// текущий оператор доступен как свойство контрола
		$this->actor = Context::getActor();
		if ($this->actor == null)
			Request::redirect("/adminka/index.php");
		
		// Только операторы могут видеть эти контролы
		if (!($this->actor instanceof Operator) )
			Request::redirect("/adminka/index.php");
		
		// и только те, которым это не запрещено
		$ok = Adminka::checkPermissions();
		if (!$ok)
		{
			SecurityLogManager::write(SecurityLog::TYPE_ACCESS_DENIED);
			Adminka::showAccessDenied();
		}
		
	}
	
	/**
	 * Этот контрол нельзя отрисовать
	 *
	 */
	public function render()
	{
		Request::send404();
	}	
}
