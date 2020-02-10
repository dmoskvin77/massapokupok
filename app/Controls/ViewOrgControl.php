<?php
/**
* Контрол покажет инфу об организаторе
*/
class ViewOrgControl extends AuthorizedUserControl
{
	public $pageTitle = "Информация об организаторе";

	public function render()
	{
		$id = Request::getInt("id");
		$this->addData("backurl", Utility::getRefUrl());

		$ts = time();
		$this->addData("ts", $ts);

		if ($id > 0)
		{
			$om = new UserManager();
			$curOrg = $om->getById($id);
			if ($curOrg)
			{
				if ($this->ownerSiteId != $curOrg->ownerSiteId || $this->ownerOrgId != $curOrg->ownerOrgId)
					Enviropment::redirectBack("Не найден организатор");

				if ($curOrg->isOrg == 1)
				{
					$this->addData("curorg", $curOrg);
					// получим список закупок орга
					$zhm = new ZakupkaHeaderManager();
					$list = $zhm->getActualByOrgId($curOrg->id, ZakupkaHeader::STATUS_ACTIVE);
					if (count($list))
						$this->addData("zhlist", $list);

					// так же поднимем "выкупы"
					$vm = new ZakupkaVikupManager();
					$vikupList = $vm->getByOrgId($curOrg->id);
					if ($vikupList)
					{
						// сериализованные даты надо десериализовать
						foreach ($vikupList AS $vikupValue)
						{
							$vikupValue->calendarData = @unserialize($vikupValue->calendarData);
							$cntCD = count($vikupValue->calendarData);
							if ($cntCD)
							{
								$i = 0;
								$calendarStr = '';
								if (count($vikupValue->calendarData) && is_array($vikupValue->calendarData))
								{
									foreach ($vikupValue->calendarData AS $oneCalendarDate)
									{
										$i++;
										$calendarStr .= Utility::pickerTimeToDate($oneCalendarDate);
										if ($i < $cntCD)
											$calendarStr .= ', ';

									}
								}

								$vikupValue->calendarData = $calendarStr;
							}
						}

						$this->addData("vikupList", $vikupList);

						// на какие выкупы подписан участник
						$actor = Context::getActor();
						$vsm = new VikupSubscribersManager();
						$subscribeList = $vsm->getByUserId($actor->id);
						if (count($subscribeList))
						{
							$userSubscriptions = array();
							foreach ($subscribeList AS $subItem)
								$userSubscriptions[$subItem->vikupId] = $subItem;

							$this->addData("userSubscriptions", $userSubscriptions);
						}
					}
				}
				else
					Enviropment::redirectBack("Участник не является организатором");

			}
			else
				Enviropment::redirectBack("Не найден организатор");

		}
		else
			Enviropment::redirectBack("Не задан ID организатора");


		// хлебные крошки вверху страницы
		$allCrumbs = array();
		// $allCrumbs[] = array("link" => "/", "name" => "Главная", "title" => "");
		$this->Crumbs = $allCrumbs;

	}

}
