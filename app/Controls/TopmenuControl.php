<?php
/**
 * Компонент отображает верхнее меню
 */
class TopmenuControl extends BaseControl implements IComponent
{
	private $current;

	function __construct($current = null)
	{
		$this->current = $current;
	}

	// рендер
	public function render()
	{
		$actor = Context::getActor();
		$this->addData("actor", $actor);
		$this->addData("curMenu", $this->current);

		$host = $_SERVER['HTTP_HOST'];
		$this->addData("host", $host);

		$forumUrl = "/forum";
		if ($this->ownerOrgId)
			$forumUrl = "/forum{$this->ownerSiteId}_{$this->ownerOrgId}";
		else
		{
			if ($this->ownerSiteId > 1)
				$forumUrl = "/forum{$this->ownerSiteId}";
		}

		$this->addData("forumUrl", $forumUrl);

		$curPage = Utils::getCurrenControlName();
		if ($curPage == 'orgviewzakupka' || $curPage == 'viewcollection')
			$curPage = 'zakupki';

		if ($curPage == 'chooseboardtype')
			$curPage = 'board';

		$this->addData("curPage", $curPage);

		// кол-во заказов
		$myzak_text = "Корзина";

		$allTLinks = array();

		if ($actor)
		{
			$allTLinks[] = array("cname" => "zakupki", "link" => 'http://'.$host.'/zakupki', "name" => "Закупки", "title" => "Закупки");
			$allTLinks[] = array("cname" => "baskethead", "link" => 'http://'.$host.'/baskethead', "name" => $myzak_text, "title" => "Корзина");

			// сколько заказов в корзине
			$ohm = new OrderHeadManager();
			$ordersCount = $ohm->getOrdersCount($actor->id);
			if ($ordersCount)
				$this->addData("ordersCount", $ordersCount);

		}
		else
		{
			$allTLinks[] = array("cname" => "userlogin", "link" => 'http://'.$host.'/userlogin', "name" => "Войти", "title" => "Войти");
			$allTLinks[] = array("cname" => "userregister", "link" => 'http://'.$host.'/userregister', "name" => "Регистрация", "title" => "Регистрация");
			$allTLinks[] = array("cname" => "zakupki", "link" => 'http://'.$host.'/zakupki', "name" => "Закупки", "title" => "Закупки");

		}

		if ($actor)
		{
			// сколько у него новых сообщений
			$msm = new MessageDialogueManager();
			$countNewMessages = intval($msm->countNewMessages($actor->id));

			// сколько участник ещё не видел публичных уведомлений
			$pem = new PublicEventManager();
			$countNewMessages = $countNewMessages + intval($pem->countNewMessages($actor->id));
			if ($countNewMessages)
				$this->addData("countNewMessages", $countNewMessages);

			$allTLinks[] = array("cname" => "messages", "link" => 'http://'.$host.'/messages', "name" => "Сообщения", "title" => "Сообщения");
		}

		// объявления для всех
		$allTLinks[] = array("cname" => "board", "link" => 'http://'.$host.'/board', "name" => "Пристрой", "title" => "Пристрой");

		$this->addData("menu", $allTLinks);

	}

}

