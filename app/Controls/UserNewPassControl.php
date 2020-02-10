<?php 
/**
* Контрол для визуального представления формы для
* генерации нового пароля
*/
class UserNewPassControl extends IndexControl
{
	public $pageTitle = "Генерация пароля покупателя";
	
	public function render()
	{
		if ($this->actor instanceof User)
			Enviropment::redirect("userarea", "Вы авторизованы на сайте. Разлогиньтесь для восстановления пароля");

		// хлебные крошки вверху страницы
		$allCrumbs = array();
		// $allCrumbs[] = array("link" => "/", "name" => "Главная", "title" => "");
		// $allCrumbs[] = array("link" => "/usernewpass", "name" => "Генерация пароля", "title" => "Генерация пароля покупателя");
		$this->Crumbs = $allCrumbs;

		$goto = Request::getVar("goto");
		$this->addData("goto", $goto);		
		
	}
}
