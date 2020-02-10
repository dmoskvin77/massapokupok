<?php
/**
 * Базовый класс для действий, требующих авторизации организатора
 *
 */
class AuthorizedOrgAction extends BaseAction implements IPublicAction 
{
	public $actor;

	public function preExecute()
	{
		parent::preExecute();

		$actor = Context::getActor();
		$this->actor = $actor;

		if ($actor == null || !($actor instanceof User))
			Enviropment::redirect("userlogin", "Для выполнения выбранного действия необходимо авторизоваться");

		if ($this->ownerSiteId != $actor->ownerSiteId || $this->ownerOrgId != $actor->ownerOrgId)
			Enviropment::redirect("userlogin", "Для выполнения выбранного действия необходимо авторизоваться");

		// метку присутствия на сайте обновим
		// 1 раз в 5 минут
		if (time() - $actor->dateLastVisit > 60 * 3)
		{
			$um = new UserManager();
			if ($actor->isBot)
				$um->updateBotVisitTime($actor->id);
			else
				$um->updateVisitTime($actor->id);
		}
	}

	public function execute()
	{
		// не отрабатываем этот Action
		Request::redirect("/");
	}
}
