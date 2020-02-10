<?php
/**
* Контрол покажет инфу об организаторе
*/
class ViewUserControl extends AuthorizedUserControl
{
	public $pageTitle = "Информация об пользователе";

	public function render()
	{
		$id = Request::getInt("id");
		$this->addData("backurl", Utility::getRefUrl());

		$ts = time();
		$this->addData("ts", $ts);

		if ($id > 0)
		{
			$om = new UserManager();
			$curUser = $om->getById($id);
			if (!$curUser)
				Enviropment::redirectBack("Не найден участник");
			else
			{
				if ($this->ownerSiteId != $curUser->ownerSiteId || $this->ownerOrgId != $curUser->ownerOrgId)
					Enviropment::redirectBack("Не найден участник");

				$this->addData("curuser", $curUser);
			}

		}
		else
			Enviropment::redirectBack("Не задан ID участника");


		// хлебные крошки вверху страницы
		$allCrumbs = array();
		// $allCrumbs[] = array("link" => "/", "name" => "Главная", "title" => "");
		$this->Crumbs = $allCrumbs;

		// хлебные крошки вверху страницы
		$allCrumbs = array();
		// $allCrumbs[] = array("link" => "/", "name" => "Главная", "title" => "");
		$this->Crumbs = $allCrumbs;

	}

}
