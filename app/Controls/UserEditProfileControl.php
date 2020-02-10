<?php
/**
* Контрол редактирования профайла
*/
class UserEditProfileControl extends AuthorizedUserControl
{
	public $pageTitle = "Редактирование профайла";

	public function render()
	{
		$this->addData("actor", $this->actor);
	}
}
