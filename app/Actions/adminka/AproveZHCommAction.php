<?php
/**
* Действие БО для одобрения комментария
*/
class AproveZHCommAction extends AdminkaAction
{
	public function execute()
	{
		$commId = Request::getInt("id");
		if (!$commId)
			Adminka::redirect("managepagecomments", "Не задан ID комментария");

		$zhcomm = new ZhCommentManager();
		$currComm = $zhcomm->getById($commId);
		if (!$currComm)
			Adminka::redirect("managepagecomments", "Не найден комментарий");

		if ($this->ownerSiteId != $currComm->ownerSiteId || $this->ownerOrgId != $currComm->ownerOrgId)
			Adminka::redirect("managepagecomments", "Не найден комментарий");

		$currComm->status = ZhComment::STATUS_MODERATED;
		$zhcomm->save($currComm);

		$amm = new AllMailManager();
		$amm->allowPending(null, $commId);

		Adminka::redirect("managepagecomments", "Комментарий одобрен");

	}

}
