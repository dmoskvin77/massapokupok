<?php
/**
 *
 */

class OrgeditvikupControl extends AuthorizedOrgControl
{
	public $pageTitle = "Редактирование выкупа";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$id = Request::getInt("id");
		if (!$id)
			Enviropment::redirectBack("Не указан ID выкупа");

		$vm = new ZakupkaVikupManager();
		$vikupObj = $vm->getById($id);
		if (!$vikupObj)
			Enviropment::redirectBack("Не найден выкуп");

		if ($vikupObj->orgId != $actor->id || $this->ownerSiteId != $vikupObj->ownerSiteId || $this->ownerOrgId != $vikupObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// всё хорошо
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
