<?php
/**
 * Компонент для отображения страниц, созданных админом
 *
 */
class PageControl extends BaseControl
{
	/** Это глобальные переменные шаблона */
	public $pageTitle = "";
	public $pageDesc = "";
	public $pageKeys = "";
	public $controlName = "";
	public $page = null;
	public $actor = null;
	public $host = null;

	public $slr1 = 1;
	public $slr2 = 1;
	public $slr3 = 1;

	// параметры для вывода конкретного сайта
	public $vkGroupId = null;
    public $okGroupId = null;

	// показывать ли информацию о пользователях на главной
	public $showUsers = null;

	function __construct($page = null)
	{
	    $this->page = $page;
	}

	public function postRender()
	{
		BaseApplication::writeSqlLog();
	}

	public function render()
	{
		$this->layout = $this->tplFolder.'/index.html';

		// менюшки и прочие обязательные вещи
		$this->pageTitle = "Сайт совместных покупок ".SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city");

		// посмотрим нет ли в сессии параметров для маппинга ссылок
		$mappingParams = Context::getArray();
		if ($mappingParams)
		{
			if (isset($mappingParams['control']) && $mappingParams['control'] == 'page' && isset($mappingParams['param']) && $mappingParams['param'] == 'name' && isset($mappingParams['value']))
				$this->page = $mappingParams['value'];

			Context::setArray(0);

		}

		// каккую страницу cms запросили
		if (!$this->page)
		{
			$this->page = Request::getVar("name", "start");

			$givedControlName = mb_strtolower($this->page, 'utf8');
			$mappingArray = Request::getMappingArray();
			if (array_key_exists($givedControlName, $mappingArray))
			{
				// ответить 301, если страница переадресована на красивый URL
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: /{$givedControlName}");
				exit();
			}

		}

		// первая страница будет сильно отличаться
		// от других страниц CMS
		// поэтому меняем макет динамически
		if ($this->page == "start")
		{
			// определим URL, где хостится приложение
			$mainHost = $this->siteDomain;
            if ($this->orgSubDomain)
            {
                $mainHost = $this->orgSubDomain.".".$mainHost;
            }

			// с какого сайта пришел запрос
			$host = $_SERVER['HTTP_HOST'];
			$this->host = $host;

			// Это наш родной сайт
			if ($mainHost == $host)
			{
				// сюда данные для главной
				$this->layout = $this->tplFolder.'/pageindex.html';

				// получить id группы vkontakte
				$this->vkGroupId = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "vkgroupid");
                // Одноклассники
                $this->okGroupId = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "okgroupid");
				// показывать ли информацию о пользователях на главной
				$this->showUsers = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "showusers");

				// рандомы для 3-х слайдов
				$this->slr1 = rand(1, 3);
				$this->slr2 = rand(1, 3);
				$this->slr3 = rand(1, 3);

			}
			else
			{
				Request::send404();
			}
		}
		else
		{
			// сюда можно насувать всяких данных
			// для страниц вида /page/name/la-la-la
			$cm = new ContentManager();
			$content = $cm->getByAlias($this->ownerSiteId, $this->ownerOrgId, $this->page);
			if (!$content)
				Request::send404();

			if ($content->menu != Content::MENU_TOP)
			{
				$this->pageTitle = $content->pageTitle;
				$this->pageDesc = $content->pageDesc;
				$this->pageKeys = $content->pageKeys;
			}
			else
			{
				$this->pageTitle = $content->title;
			}

			$this->addData("content", $content);
			$this->addData("text", str_replace("&quot;", '"', htmlspecialchars_decode($content->text, ENT_NOQUOTES)));

			// каменты
			$cocomm = new CoCommentManager();
			// заголовки
			$commlist = $cocomm->getModByHeadId($content->id, CoComment::COMMENT_CONTENT);
			// головы каментов
			$this->addData("commlist", $commlist);
			// пройдем циклом заголовки и получим цепочки ответов к каждому
			$subcomments = array();
			// все сабкомменты
			$allSubcomments = $cocomm->getAllSubComments($content->id, CoComment::COMMENT_CONTENT);
			if (count($allSubcomments) > 0)
			{
				foreach ($allSubcomments as $comheaditem)
					$subcomments[$comheaditem['rootId']][] = $comheaditem;

				// субкаменты
				$this->addData("subcomments", $subcomments);
			}

		}

		// пользователь
		$this->actor = Context::getActor();
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

}
