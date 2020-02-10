<?php

class PlacenewadvtoboardControl extends AuthorizedUserControl
{
	public $pageTitle = "Добавить объявление";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);
	}

}
