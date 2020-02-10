<?php
/**
 * Компонент отображает меню категорий
 */
class CategorymenuControl extends BaseControl implements IComponent
{
	private $cat;

	function __construct($cat = null)
	{
		$this->cat = $cat;
	}

	// рендер
	public function render()
	{
        $cm = new CategoryManager();
		// делаем выборку и сортировку
		$all = $cm->getAll($this->ownerSiteId, $this->ownerOrgId);
		$this->addData("all", $all);

		// активная категория
		$getCat = Request::getInt("cat");
		if (!$this->cat && $getCat)
			$this->cat = $getCat;

		$this->addData("cat", $this->cat);
		$this->addData("mode", Request::getVar("mode"));
		$this->addData("city", SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "city"));

	}

}
