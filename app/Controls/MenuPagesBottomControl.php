<?php
/**
 * Компонент отображает верхнее меню
 */
class MenuPagesBottomControl extends BaseControl implements IComponent
{
	// рендер
	public function render()
	{
		$cm = new ContentManager();
		$menu = $cm->getMenu($this->ownerSiteId, $this->ownerOrgId, Content::MENU_BOTTOM);
		if (count($menu))
		{
			$menuNew = array();
			$mappingArray = Request::getMappingArray();
			foreach ($menu AS $item)
			{
				if (array_key_exists($item['alias'], $mappingArray))
					$item['link'] = $item['alias'];
				else
					$item['link'] = 'page/name/'.$item['alias'];

				$menuNew[] = $item;
			}

			$this->addData("menu", $menuNew);
		}
	}	
}
