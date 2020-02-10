 <?php
/**
* Действие БО для одобрения комментария к контенту
*/
class AproveCOCommAction extends AdminkaAction
{
	public function execute()
	{
		$commId = Request::getInt("id");
		if (!$commId)
			Adminka::redirect("manageboard", "Не задан комментарий");

		$cocomm = new CoCommentManager();
		$currComm = $cocomm->getById($commId);
		if (!$currComm)
			Adminka::redirect("manageboard", "Не найден комментарий");
		else
		{
			if ($this->ownerSiteId != $currComm->ownerSiteId || $this->ownerOrgId != $currComm->ownerOrgId)
				Adminka::redirect("manageboard", "Нет прав на выполнение выбранного действия");
		}

		if ($currComm->type == CoComment::COMMENT_ZAKUPKA)
		{
			$zhm = new ZakupkaHeaderManager();
			$zakObj = $zhm->getById($currComm->headId);
			if (!$zakObj)
				Adminka::redirectBack("Не найдена закупка");

			$com = new CoNotificationManager();
			$com->saveAddNotification($this->ownerSiteId, $this->ownerOrgId, $zakObj->id, $currComm->toId, $zakObj->name);
		}

		$currComm->status = CoComment::STATUS_MODERATED;
		$cocomm->save($currComm);

		$amm = new AllMailManager();
		$amm->allowPending($commId);

		Adminka::redirect("manageboard", "Комментарий одобрен");

	}

}
