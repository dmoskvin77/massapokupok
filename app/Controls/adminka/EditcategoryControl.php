<?php
/**
 * Контрол для редактирования/создания категории
 */
class EditcategoryControl extends BaseAdminkaControl
{
	public function render()
	{
		$cat = null;
		$id = Request::getInt("id");
		$cm = new CategoryManager();
		if ($id)
			$cat = $cm->getById($id);

		if (!$cat)
		{
			$cat = new Category();
			$cat->ownerSiteId = $this->ownerSiteId;
			$cat->ownerOrgId = $this->ownerOrgId;
		}
		else
		{
			if ($this->ownerSiteId != $cat->ownerSiteId || $this->ownerOrgId != $cat->ownerOrgId)
				Adminka::redirect("managecategories", "Нет прав на выполнение данного действия");
		}

		$this->addData("category", $cat);

		// все прочие категории
		$all = $cm->getAll($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("all", $all);

	}
}