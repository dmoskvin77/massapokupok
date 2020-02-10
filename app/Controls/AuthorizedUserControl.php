<?php
/**
 * Базовый класс для всех контролов, требующих авторизацию покупателя
 *
 */
class AuthorizedUserControl extends BaseControl
{
	/** Это глобальные переменные шаблона */
	public $pageTitle = "";
	public $pageDesc = "";
	public $pageKeys = "";
	public $controlName = "";
	public $page = null;
	public $actor = null;

	public function preRender()
	{
		parent::preRender();

		$this->actor = Context::getActor();

		// только юзеры могут видеть эти контролы
		if(!($this->actor instanceof User))
		{
			Enviropment::redirect("userlogin", "Необходимо авторизоваться");
		}
		else
		{
			// добавляем актора во все шаблоны где он авторизован
			$this->addData("actor", $this->actor);

			// метку присутствия на сайте обновим
			// 1 раз в 5 минут
			if (time() - $this->actor->dateLastVisit > 60 * 3)
			{
				$um = new UserManager();
				if ($this->actor->isBot)
					$um->updateBotVisitTime($this->actor->id);
				else
					$um->updateVisitTime($this->actor->id);
			}
		}

		$this->layout = $this->tplFolder."/userhome.html";

	}

	/**
	 * Не рисуем его
	 */
	public function render()
	{
		Request::send404();
	}

	public function postRender()
	{
		BaseApplication::writeSqlLog();
	}
}
