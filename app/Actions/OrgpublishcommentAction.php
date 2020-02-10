<?php
/**
* Действие для одобрения камента в закупке
*/

class OrgpublishcommentAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		// id комментария
		$id = Request::getInt('id');
		if (!$id)
			Enviropment::redirectBack("Не указан ID комментария");

		$cm = new CoCommentManager();
		$commObj = $cm->getById($id);
		if (!$commObj)
			Enviropment::redirectBack("Комментарий не найден");

		if ($this->ownerSiteId != $commObj->ownerSiteId || $this->ownerOrgId != $commObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		if ($commObj->type != CoComment::COMMENT_ZAKUPKA)
			Enviropment::redirectBack("Нет прав на публикацию данного комментария");

		if ($commObj->status == CoComment::STATUS_MODERATED)
			Enviropment::redirectBack("Комментарий уже был опубликован");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($commObj->headId);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		$actor = $this->actor;
		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на публикацию данного комментария");

		$com = new CoNotificationManager();
		if ($commObj->toId)
			$com->saveAddNotification($this->ownerSiteId, $this->ownerOrgId, $zakObj->id, $commObj->toId, $zakObj->name);
		else
			$com->saveAddNotification($this->ownerSiteId, $this->ownerOrgId, $zakObj->id, $commObj->userId, $zakObj->name);

		$commObj->status = CoComment::STATUS_MODERATED;
		$cm->save($commObj);

		// всё готово
		Enviropment::redirectBack("Комментарий опубликован");

	}

}
