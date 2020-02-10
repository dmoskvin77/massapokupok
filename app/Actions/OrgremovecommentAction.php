<?php
/**
* Действие для удаления камента к закупке
*/

class OrgremovecommentAction extends AuthorizedOrgAction implements IPublicAction
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
			Enviropment::redirectBack("Нет прав на удаление данного комментария");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($commObj->headId);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		$actor = $this->actor;
		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на публикацию данного комментария");

		$cm->remove($id);

		// всё готово
		Enviropment::redirectBack("Комментарий удален");

	}

}
