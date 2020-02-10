<?php
/**
 * Компонент отображает верхнее меню
 */
class MenuPagesTopControl extends BaseControl implements IComponent
{
	// рендер
	public function render()
	{
		$cm = new ContentManager();
		$menu = $cm->getMenu($this->ownerSiteId, $this->ownerOrgId, Content::MENU_TOP);
		$this->addData("menu", $menu);
	}
}
