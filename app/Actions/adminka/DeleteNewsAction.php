<?php
/**
 * Действие БО - удаление новости
 */
class DeleteNewsAction extends AdminkaAction
{
	public function execute()
	{
		$newsId = Request::getInt("newsid");
		if (!$newsId)
			Adminka::redirect("managenews", "Новость не задана");
		
		$nm = new NewsManager();
		$news = $nm->getById($newsId);
		if (!$news)
			Adminka::redirect("managenews", "Новость не найдена");

		if ($this->ownerSiteId != $news->ownerSiteId || $this->ownerOrgId != $news->ownerOrgId)
			Adminka::redirect("managenews", "Нет прав на выполнение действия");

		$delId = $news->id;
		$nm->remove($delId);

		Adminka::redirect("managenews", "Новость удалена");

	}

}
