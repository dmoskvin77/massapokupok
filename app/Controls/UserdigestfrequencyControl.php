<?php
/**
 * Формочка для изменении частоты получения рассылки по е-майл
 *
 */
class UserdigestfrequencyControl extends AuthorizedUserControl
{
	public $pageTitle = "Чаще или реже? Главное - регулярно!";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);
	}
}
