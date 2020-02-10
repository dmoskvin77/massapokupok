<?php
/**
* Рассылка участникам закупки
*
*/
class OrgbroadcastAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		// само сообщение
		$message = Request::getVar("message");
		if (!$message || $message == '')
			Enviropment::redirectBack("Сообщение не должно быть пустым");

		if (mb_strlen($message) > 10000000)
			Enviropment::redirectBack("Слишком длинное сообщение");

		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// TODO: надо сохранить рассылку в лог
		// для этого приидется добавить отдельную сущность "лог рассылок"

		// и поставить задачу в очередь
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask("orgbroadcast", $actor->id, $actor->nickName, $zakObj->id, $zakObj->name, null, base64_encode($message));

		Enviropment::redirect("orgviewzakupka/headid/".$headid, "Рассылка поставлена в очередь");

	}

}
