<?php
/**
 * Контрол для визуального представления главной страницы сайта
 *
 */

class IndexControl extends BaseControl
{
	/** Это глобальные переменные шаблона */
	public $pageTitle = "";
	public $pageDesc = "";
	public $pageKeys = "";
	public $controlName = "";
	public $actor = null;
	public $page = null;

	public function preRender()
	{
		$this->actor = Context::getActor();
		parent::preRender();
		$this->layout = $this->tplFolder.'/index.html';

        if ($this->actor)
        {
			$this->addData("actor", $this->actor);
	        if (time() - $this->actor->dateLastVisit > 60 * 3)
	        {
		        $um = new UserManager();
		        if ($this->actor->isBot)
			        $um->updateBotVisitTime($this->actor->id);
		        else
		            $um->updateVisitTime($this->actor->id);
	        }

        }

	}

	public function render()
	{
		Enviropment::redirect("page");
	}

	public function postRender()
	{
		BaseApplication::writeSqlLog();
	}
}
