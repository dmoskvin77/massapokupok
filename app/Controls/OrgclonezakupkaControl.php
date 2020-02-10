<?php
/**
 *
 */

class OrgclonezakupkaControl extends AuthorizedOrgControl
{
	public $pageTitle = "Клонирование закупки";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// всё хорошо
		$this->addData("headid", $headid);
		$this->addData("zakObj", $zakObj);

		if ($zakObj->vikupId)
		{
			$vm = new ZakupkaVikupManager();
			$vikupObj = $vm->getById($zakObj->vikupId);
			if ($vikupObj && $zakObj->orgId == $vikupObj->orgId)
			{
				$presenceDates = array();
				$oldPresenceDates = @unserialize($vikupObj->calendarData);
				if ($oldPresenceDates && count($oldPresenceDates))
				{
					foreach ($oldPresenceDates AS $oneUnixDate)
					{
						if ($oneUnixDate > time())
							$presenceDates[] = $oneUnixDate;
					}
				}

				if (count($presenceDates))
					$vikupObj->calendarData = @serialize(array_unique($presenceDates));
				else
					$vikupObj->calendarData = null;

				$vikupObj = $vm->save($vikupObj);

				if (isset($presenceDates[0]))
					$this->addData("newdate_1", $presenceDates[0]);

				if (isset($presenceDates[1]))
					$this->addData("newdate_2", $presenceDates[1]);

				if (isset($presenceDates[2]))
					$this->addData("newdate_3", $presenceDates[2]);

				$this->addData("vikup", $vikupObj);
			}

		}

	}

}
