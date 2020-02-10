<?php
/**
 * Компонент отображает табы
 */
class TabmenuControl extends BaseControl implements IComponent
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

		$curPage = Utils::getCurrenControlName();

		// echo $curPage;

		if ($curPage == 'userpay')
			$curPage = 'finanses';

		if (strpos($curPage, 'zparse') !== false || in_array($curPage, array('orgviewopt', 'orgeditopt', 'orgparsers')))
			$curPage = 'orgoptlist';

		if (in_array($curPage, array('orgaddzakupkahead', 'orgclonezakupka', 'orgbroadcast', 'orgviewzakupka', 'orgeditvikup')))
			$curPage = 'orgzhlist';

		if ($curPage == 'officeordersissue')
			$curPage = 'officemanager';

		$this->addData("curPage", $curPage);

		$orgTLinks = array();
		$userTLinks = array();

		if ($actor)
		{
			if ($actor->isOrg)
			{
				// сколько неподтвержденных оплат от участников
				$addPayCnt = "";
				$pm = new PayManager();
				$countNewPay = $pm->countNewPaysByOrgId($actor->id);
				if ($countNewPay)
					$addPayCnt = " <b>({$countNewPay})</b>";

				// счета на оплату коммиссии сайту
				$addCommCnt = "";
				$scm = new SiteCommisionManager();
				$countNewComm = $scm->countNewPaysByOrgId($actor->id);
				if ($countNewComm)
					$addCommCnt = " <b>({$countNewComm})</b>";

				$orgTLinks[] = array("cname" => "orgoptlist", "link" => 'http://'.$host.'/orgoptlist/view/my', "name" => "Поставщики", "title" => "Поставщики");
				$orgTLinks[] = array("cname" => "orgzhlist", "link" => 'http://'.$host.'/orgzhlist', "name" => "Мои закупки", "title" => "Мои закупки");
				$orgTLinks[] = array("cname" => "orgfinanses", "link" => 'http://'.$host.'/orgfinanses', "name" => "Оплаты мне{$addPayCnt}", "title" => "Оплаты мне{$addPayCnt}");
				$orgTLinks[] = array("cname" => "orgordersissue", "link" => 'http://'.$host.'/orgordersissue', "name" => "Раздача", "title" => "Раздача");
				$orgTLinks[] = array("cname" => "orgcommision", "link" => 'http://'.$host.'/orgcommision', "name" => "Счета{$addCommCnt}", "title" => "Счета");
			}
			else
			{

			}

			$userTLinks[] = array("cname" => "finanses", "link" => 'http://'.$host.'/finanses', "name" => "Мои оплаты", "title" => "Мои оплаты");
			$userTLinks[] = array("cname" => "myboardads", "link" => 'http://'.$host.'/myboardads', "name" => "Мои объявления", "title" => "Мои объявления");

			// права пользователя
			$upm = new UserPermissionsManager();
			$permissions = $upm->getByUserId($actor->id);
			if (count($permissions))
			{
				foreach ($permissions AS $onepermission)
				{
					if ($onepermission->type == UserPermissions::TYPE_OFFICE_MANAGER)
					{
						$userTLinks[] = array("cname" => "officemanager", "link" => 'http://'.$host.'/officemanager', "name" => "Офисы раздач", "title" => "Офисы раздач");
					}

				}
			}

		}

		$this->addData("orgmenu", $orgTLinks);
		$this->addData("usermenu", $userTLinks);

	}

}

