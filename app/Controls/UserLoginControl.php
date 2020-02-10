<?php
/**
* Контрол для визуального представления формы для
* входа пользователя на сайт
*/
class UserLoginControl extends IndexControl
{
	public $pageTitle = "Вход для покупателей";

	public function render()
	{
		if ($this->actor instanceof User)
			Enviropment::redirect("userarea", "Вы уже авторизованы на сайте");

        $this->controlName = "UserLogin";

		$goto = Request::getVar("goto");
		if (!$goto)
		{
			$goto = Request::prevUri();
			$goto = strtolower($goto);
			$goto = str_replace("http://".str_replace('www.', '', strtolower($_SERVER['HTTP_HOST'])), '', $goto);
			$goto = str_replace("https://".str_replace('www.', '', strtolower($_SERVER['HTTP_HOST'])), '', $goto);
			$goto = str_replace('http://', '', $goto);
			$goto = str_replace('https://', '', $goto);

			$firstChar = substr($goto, 0, 1);
			if ($firstChar == '/')
				$goto = substr($goto, 1);

			if ($goto == '/' || $goto == '' || $goto == 'userlogin' || $goto == 'logout')
				$goto = null;
		}

		if ($goto)
			$this->addData("goto", base64_encode($goto));

	}
}
