<?php
/**
* Контрол для визуального представления формы для
* регистрации нового покупателя
*/
class UserRegisterControl extends IndexControl
{
	public $pageTitle = "Регистрация нового пользователя";

	public function render()
	{
		if ($this->actor)
			Enviropment::redirect("userarea", "Вы уже вошли на сайт.");

		// настройка нужно ли вводить номер телефона
		if (SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'needphone') == 'on')
			$this->addData("needphone", true);

	}

}
