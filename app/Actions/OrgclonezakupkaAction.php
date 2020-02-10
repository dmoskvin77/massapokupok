<?php
/**
* Рассылка участникам закупки
*
*/
class OrgclonezakupkaAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$headid = FilterInput::add(new IntFilter("headid", true, "ID закупки"));
		$vikupId = FilterInput::add(new IntFilter("vikupId", false, "ID выкупа"));
		$vikupName = FilterInput::add(new StringFilter("vikupName", false, "Общее название выкупа"));

		// новые даты
		$newdate_1 = FilterInput::add(new StringFilter("newdate_1", false, "Новая дата выкупа 1"));
		$newdate_2 = FilterInput::add(new StringFilter("newdate_2", false, "Новая дата выкупа 2"));
		$newdate_3 = FilterInput::add(new StringFilter("newdate_3", false, "Новая дата выкупа 3"));

		if (!$vikupId && (!$vikupName || $vikupName == ''))
			FilterInput::addMessage("Не указан выкуп");

		// текущий организатор
		$actor = $this->actor;
		$orgId = $actor->id;

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			FilterInput::addMessage("Не указан выкуп");
		else
		{
			if ($zakObj->orgId != $orgId || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
				FilterInput::addMessage("Нет прав на выполнение данного действия");
		}

		$vikupObj = null;
		$vm = new ZakupkaVikupManager();
		if (!$vikupId)
			$vikupObj = new ZakupkaVikup();
		else
			$vikupObj = $vm->getById($vikupId);

		if (!$vikupObj)
			FilterInput::addMessage("Не найден выкуп");

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
			FormRestore::add("org-clone");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// добавить реквизиты
		if (!$vikupId)
		{
			$vikupObj->name = $vikupName;
			$vikupObj->orgId = $orgId;
			$vikupObj->dateCreate = time();
			$vikupObj->ownerSiteId = $this->ownerSiteId;
			$vikupObj->ownerOrgId = $this->ownerOrgId;
			if (count($calendarDates))
				$vikupObj->calendarData = @serialize(array_unique($calendarDates));
			else
				$vikupObj->calendarData = null;
		}
		else
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

			$presenceDates = array_merge($presenceDates, $calendarDates);
			sort($presenceDates);

			if (count($presenceDates))
				$vikupObj->calendarData = @serialize(array_unique($presenceDates));
			else
				$vikupObj->calendarData = null;
		}

		// сохраним выкуп с новой инфой
		$vikupObj = $vm->save($vikupObj);

		// собственно клонируем
		$zakObj->vikupId = $vikupObj->id;
		$zakObj = $zhm->save($zakObj);

		// новая закупка
		$newZakObj = new ZakupkaHeader();

		if ($zakObj->vikupId)
			$newZakObj->vikupId = $zakObj->vikupId;

		if ($zakObj->name)
			$newZakObj->name = $zakObj->name;

		if ($zakObj->categoryId1)
			$newZakObj->categoryId1 = $zakObj->categoryId1;

		if ($zakObj->categoryId2)
			$newZakObj->categoryId2 = $zakObj->categoryId2;

		if ($zakObj->categoryId3)
			$newZakObj->categoryId3 = $zakObj->categoryId3;

		$newZakObj->status = ZakupkaHeader::STATUS_NEW;
		$newZakObj->dateCreate = time();
		$newZakObj->dateUpdate = time();
		$newZakObj->orgId = $orgId;
		$newZakObj->ownerSiteId = $this->ownerSiteId;
		$newZakObj->ownerOrgId = $this->ownerOrgId;

		if ($zakObj->entityStatus)
			$newZakObj->entityStatus = $zakObj->entityStatus;

		if ($zakObj->optId)
			$newZakObj->optId = $zakObj->optId;

		if ($zakObj->orgRate) {
			if ($newZakObj->orgRate != $zakObj->orgRate) {
				// проапдейтим orgRate по этой закупке в таблице `orderHead`
                $ohm = new OrderHeadManager();
                $ohm->setOrgRateByHeadId($headid, $zakObj->orgRate);
			}
			$newZakObj->orgRate = $zakObj->orgRate;
		}

		if ($zakObj->minAmount)
			$newZakObj->minAmount = $zakObj->minAmount;

		if ($zakObj->minValue)
			$newZakObj->minValue = $zakObj->minValue;

		if ($zakObj->description)
			$newZakObj->description = $zakObj->description;

		if ($zakObj->specialNotes)
			$newZakObj->specialNotes = $zakObj->specialNotes;

		if ($zakObj->useForm)
			$newZakObj->useForm = $zakObj->useForm;

		if ($zakObj->pageUrl)
			$newZakObj->pageUrl = $zakObj->pageUrl;

		if ($zakObj->picFile1)
			$newZakObj->picFile1 = $zakObj->picFile1;

		if ($zakObj->picSrv1)
			$newZakObj->picSrv1 = $zakObj->picSrv1;

		if ($zakObj->picVer1)
			$newZakObj->picVer1 = $zakObj->picVer1;

		if ($zakObj->docFile1)
			$newZakObj->docFile1 = $zakObj->docFile1;

		if ($zakObj->docFile2)
			$newZakObj->docFile2 = $zakObj->docFile2;

		if ($zakObj->docFile3)
			$newZakObj->docFile3 = $zakObj->docFile3;

		if ($zakObj->docSrv1)
			$newZakObj->docSrv1 = $zakObj->docSrv1;

		if ($zakObj->docSrv2)
			$newZakObj->docSrv2 = $zakObj->docSrv2;

		if ($zakObj->docSrv3)
			$newZakObj->docSrv3 = $zakObj->docSrv3;

		if ($zakObj->currency)
			$newZakObj->currency = $zakObj->currency;

		$newZakObj = $zhm->save($newZakObj);


		// ряды придетсся копировать асинхронно, т.к. это "тяжёлая" задача
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask("copyzlines", $actor->id, $actor->nickName, $zakObj->id, $zakObj->name, null, $newZakObj->id);

		// затем перебрасываем в редактирование новой закупки
		Enviropment::redirect("orgviewzakupka/headid/".$newZakObj->id, "Клонирование прошло УСПЕШНО! Ряды появятся через 10 минут!");

	}

}
