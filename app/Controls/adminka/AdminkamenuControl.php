<?php
/**
 * Компонент отображает меню
 */
class AdminkamenuControl extends AuthorizedAdminkaControl implements IComponent 
{
	public function render()
	{
		// текущие права актора
		$this->addData("permissions", Adminka::getCurrentPermissions());
	}
}
