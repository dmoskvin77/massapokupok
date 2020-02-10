<?php
/**
* Добавить встречу
*/

class OrgaddmeetingAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$headid = FilterInput::add(new IntFilter("headid", true, "ID закупки"));
		$startTs = FilterInput::add(new StringFilter("startTs", true, "Дата и время начала встречи"));
		$finishTs = FilterInput::add(new StringFilter("finishTs", true, "Дата и время окончания встречи"));
		$userLimit = FilterInput::add(new IntFilter("userLimit", false, "ID закупки"));
		$message = Request::getVar("message");

		// преобразование даты
		$unixStartDate = Utility::pickerDateToTime($startTs);
		$unixFinishDate = Utility::pickerDateToTime($finishTs);

		if ($unixStartDate >= $unixFinishDate || $unixStartDate <= time())
			FilterInput::addMessage("Не верный интервал времени для встречи");

		// текущий организатор
		$actor = Context::getActor();
		$orgId = $actor->id;

		// закупка
		$zhm = new ZakupkaHeaderManager();
		$zheadObj = $zhm->getById($headid);
		if (!$zheadObj)
			FilterInput::addMessage("Не найдена закупка");
		else
		{
			if ($orgId != $zheadObj->orgId || $this->ownerSiteId != $zheadObj->ownerSiteId || $this->ownerOrgId != $zheadObj->ownerOrgId)
				FilterInput::addMessage("Нет прав на измение закупки");
		}

		// анализ введенных данных
		if (mb_strlen($message) > 10000000)
			FilterInput::addMessage("Слишком большой текст");

		if (!mb_strlen($message))
			FilterInput::addMessage("Не введен текст о месте встрече");

		if (!FilterInput::isValid())
		{
			FormRestore::add("form-meeting");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		$mtm = new MeetingManager();
		$mtObj = new Meeting();
		$mtObj->message = $message;
		$mtObj->headId = $headid;
		$mtObj->orgId = $orgId;
		$mtObj->ownerSiteId = $this->ownerSiteId;
		$mtObj->ownerOrgId = $this->ownerOrgId;

		if ($userLimit)
			$mtObj->userLimit = $userLimit;

		$mtObj->startTs = $unixStartDate;
		$mtObj->finishTs = $unixFinishDate;
		$mtObj->status = Meeting::STATUS_NEW;

		$mtObj = $mtm->save($mtObj);

		// в очередь ставим задачу разослать всем участникам закупки
		// приглашение на встречу
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask("addmeeting", $orgId, $actor->nickName, $headid, $zheadObj->name, $mtObj->id);

		// всё готово
		Enviropment::redirect("orgviewzakupka/headid/{$headid}/mode/meeting", "Встреча назначена");

	}

}
