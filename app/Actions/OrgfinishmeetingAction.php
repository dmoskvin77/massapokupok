<?php
/**
* Запись на встречу прекращается
*
*/
class OrgfinishmeetingAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		$id = Request::getInt("id");
		if (!$id)
			Enviropment::redirectBack("Не указан ID встречи");

		$mtm = new MeetingManager();
		$meetObj = $mtm->getById($id);
		if (!$meetObj)
			Enviropment::redirectBack("Не найдена встреча");

		if ($meetObj->orgId != $actor->id || $this->ownerSiteId != $meetObj->ownerSiteId || $this->ownerOrgId != $meetObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		if ($meetObj->status == Meeting::STATUS_OVER)
			Enviropment::redirectBack("Запись на встречу уже была завершена");

		$meetObj->status = Meeting::STATUS_OVER;
		$meetObj = $mtm->save($meetObj);

		// уведомления по всем тем, кто не записался удалим
		$pbm = new PublicEventManager();
		$pbm->removeAllUnacceptedByMeetId($meetObj->id);

		Enviropment::redirectBack("Запись на встречу завершена");

	}

}
