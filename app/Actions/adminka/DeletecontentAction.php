<?php
/**
 * Действие БО для удаления страницы CMS
 */
class DeletecontentAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("managecontent", "Не указан ID страницы");
			
		$cm = new ContentManager();
		$content = $cm->getById($id);
		if (!$content)
			Adminka::redirect("managecontent", "Страница не найдена");

		if ($this->ownerSiteId != $content->ownerSiteId || $this->ownerOrgId != $content->ownerOrgId)
			Adminka::redirect("managecontent", "Нет прав на выполнение данного действия");

		$cm->remove($id);

		Adminka::redirect("managecontent", "Страница успешно удалена");

	}

}
