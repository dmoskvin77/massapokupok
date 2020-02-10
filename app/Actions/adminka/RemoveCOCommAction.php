<?php
/**
* Действие БО для удаления комментария к контенту
*/
class RemoveCOCommAction extends AdminkaAction
{
	public function execute()
	{
		$commId = Request::getInt("id");
		if (!$commId)
			Adminka::redirect("managepagecomments", "Не задан комментарий");

		$cocomm = new CoCommentManager();

		$currComm = $cocomm->getById($commId);
		if (!$currComm)
			Adminka::redirect("managepagecomments", "Не найден комментарий");

		if ($this->ownerSiteId != $currComm->ownerSiteId || $this->ownerOrgId != $currComm->ownerOrgId)
			Adminka::redirect("managepagecomments", "Нет прав на выполнения данного действия");

		$amm = new AllMailManager();
		$amm->removePending($commId);

		$cocomm->remove($commId);

		Adminka::redirect("managepagecomments", "Комментарий удален");

	}

}
