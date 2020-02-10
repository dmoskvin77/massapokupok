<?php
/**
* Рассылка участникам закупки
*
*/
class OrgeditvikupAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$vikupId = FilterInput::add(new IntFilter("vikupId", true, "ID выкупа"));
		$vikupName = FilterInput::add(new StringFilter("vikupName", true, "Общее название выкупа"));

		// новые даты
		$newdate_1 = FilterInput::add(new StringFilter("newdate_1", false, "Новая дата выкупа 1"));
		$newdate_2 = FilterInput::add(new StringFilter("newdate_2", false, "Новая дата выкупа 2"));
		$newdate_3 = FilterInput::add(new StringFilter("newdate_3", false, "Новая дата выкупа 3"));

		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		$vm = new ZakupkaVikupManager();
		$vikupObj = $vm->getById($vikupId);
		if (!$vikupObj)
			FilterInput::addMessage("Не найден выкуп");
		else
		{
			if ($vikupObj->orgId != $orgId || $this->ownerSiteId != $vikupObj->ownerSiteId || $this->ownerOrgId != $vikupObj->ownerOrgId)
				FilterInput::addMessage("Нет прав на выполнение данного действия");
		}

		$unixNewdate1 = Utility::pickerDateToTime($newdate_1);
		$unixNewdate2 = Utility::pickerDateToTime($newdate_2);
		$unixNewdate3 = Utility::pickerDateToTime($newdate_3);

		if ($unixNewdate1 && $unixNewdate1 < time())
			FilterInput::addMessage("Первая дата в прошлом, пожалуйста поправьте");

		if ($unixNewdate2 && $unixNewdate2 < time())
			FilterInput::addMessage("Вторая дата в прошлом, пожалуйста поправьте");

		if ($unixNewdate3 && $unixNewdate3 < time())
			FilterInput::addMessage("Третья дата в прошлом, пожалуйста поправьте");

		$calendarDates = array();
		if ($unixNewdate1)
			$calendarDates[] = $unixNewdate1;

		if ($unixNewdate2)
			$calendarDates[] = $unixNewdate2;

		if ($unixNewdate3)
			$calendarDates[] = $unixNewdate3;

		sort($calendarDates);

		if (!FilterInput::isValid())
		{
			FormRestore::add("edit-clone");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// сохраним изменения
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

		$presenceDates = array_merge($presenceDates, $calendarDates);
		sort($presenceDates);

		$vikupObj->name = $vikupName;

		if (count($presenceDates))
			$vikupObj->calendarData = @serialize(array_unique($presenceDates));
		else
			$vikupObj->calendarData = null;


		// сохраним выкуп с новой инфой
		$vm->save($vikupObj);

		// затем перебрасываем в редактирование новой закупки
		Enviropment::redirect("orgzhlist", "Изменения записаны");

	}

}
