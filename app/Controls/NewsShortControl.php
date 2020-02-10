<?php
/**
* Контрол для визуального представления новостей на сайте
*/
class NewsShortControl extends BaseControl implements IComponent
{
	public function render()
	{
		$this->addData("host", $this->host);
		$nm = new NewsManager();
		$ids = $nm->getNewsIds($this->ownerSiteId, $this->ownerOrgId, 7);
		if (count($ids))
			$this->addData("news", $nm->getByIds($ids));
	}
}
