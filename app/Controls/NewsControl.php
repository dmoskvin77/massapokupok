<?php
/**
* Контрол для визуального представления новостей на сайте
*/
class NewsControl extends IndexControl
{
	public function render()
	{
		$nm = new NewsManager();
		$id = Request::getInt("id");
		if ($id == null)
			$ids = $nm->getNewsIds($this->ownerSiteId, $this->ownerOrgId);
		else
			$ids = array($id);

		$total = count($ids);
		$this->addData("total", $total);

		// пейджинг
		$perPage = 10;
		$this->addData("perPage", $perPage);
		$this->addData("page", Request::getInt("page") );
		$ids = FrontPagerControl::limit($ids, $perPage, "page");
		$this->addData("news", $nm->getByIds($ids));
	}
}
