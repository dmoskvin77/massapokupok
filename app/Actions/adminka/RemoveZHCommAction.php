<?php
/**
* Действие БО для удаления комментария
*/
class RemoveZHCommAction extends AdminkaAction
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
			Adminka::redirect("managepagecomments", "Нет прав на выполнение данного действия");

		$amm = new AllMailManager();
		$amm->removePending(null, $commId);

		$zhcomm->remove($commId);

		Adminka::redirect("managepagecomments", "Комментарий одобрен");

	}

}
