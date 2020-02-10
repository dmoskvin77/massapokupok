<?php
/**
 * Действие БО для удаления категории
 *
 */
class DeletecategoryAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("managecategories", "Нет указан ID категории");

        $cm = new CategoryManager();
		$catObj = $cm->getById($id);
		if (!$catObj)
			Adminka::redirect("managecategories", "Не найдена категория");

		if ($this->ownerSiteId != $catObj->ownerSiteId || $this->ownerOrgId != $catObj->ownerOrgId)
			Adminka::redirect("managecategories", "Нет прав для выполнения данного действия");

		$cm->remove($catObj->id);

		Adminka::redirect("managecategories", "Категория удалена");

	}

}
