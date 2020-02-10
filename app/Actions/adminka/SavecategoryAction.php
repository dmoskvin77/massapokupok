<?php
/**
 * Действие БО для сохранения категории
 *
 */
class SavecategoryAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		$name = FilterInput::add(new StringFilter("name", true, "Наименование"));
		$parentId = Request::getInt("parentId");
		if (!FilterInput::isValid())
		{
			FormRestore::add("edit-category");
			Adminka::redirectBack(FilterInput::getMessages());
		}

        $cm = new CategoryManager();
		$catObj = null;
		if ($id)
			$catObj = $cm->getById($id);

		if (!$catObj)
			 $catObj = new Category();
		else
		{
			if ($this->ownerSiteId != $catObj->ownerSiteId || $this->ownerOrgId != $catObj->ownerOrgId)
				Adminka::redirect("managecategories", "Нет прав для выполнения данного действия");
		}

		$catObj->name = $name;
		$catObj->ownerSiteId = $this->ownerSiteId;
		$catObj->ownerOrgId = $this->ownerOrgId;
		$catId = $cm->save($catObj);

		$iteration = 0;
		// максимальное кол-во уровней (пока только 2)
		$maxIterations = 2;
		$pathArray = array();
		$pathArray[] = $catId->id;

		$catObj->parentId = $parentId;

		if ($parentId)
			$pathArray[] = $parentId;

		$continueAnalize = true;
		while ($continueAnalize)
		{
			$iteration++;
			$parentObj = null;
			if ($parentId)
				$parentObj = $cm->getById($parentId);

			if (!$parentObj)
			{
				$continueAnalize = false;
			}
			else
			{
				if ($parentObj->parentId)
				{
					$pathArray[] = $parentObj->parentId;
					$parentId = $parentObj->parentId;
				}
				else
				{
					$continueAnalize = false;
				}
			}

			if ($iteration >= $maxIterations)
				$continueAnalize = false;
		}


		if ($catObj)
		{
			$catObj->level = count($pathArray);
			$catObj->path = "|".implode("|", array_reverse($pathArray))."|";
			$cm->save($catObj);
		}

		Adminka::redirect("managecategories", "Категория успешно сохранена");

	}

}
