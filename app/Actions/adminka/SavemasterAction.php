<?php
/**
 * Действие БО для сохранения master пароля
 * 
 */
class SavemasterAction extends AdminkaAction
{
	public function execute()
	{
		$master = FilterInput::add(new StringFilter("master", true, "Мастер-пароль"));
		if(!FilterInput::isValid())
			Adminka::redirect("masterpassword", FilterInput::getMessages());

		$pass = md5(md5($master).sha1($master));
		$sm = new SettingsManager();
		$sm->updateValue($this->ownerSiteId, $this->ownerOrgId, 'master', $pass);

		Adminka::redirect("mainpage", "Мастер-пароль изменен");

	}

}
