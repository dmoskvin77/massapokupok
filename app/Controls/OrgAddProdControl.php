<?php
/**
 * Контрол для добавления нового оптовика
 * 
 */
class OrgAddProdControl extends AuthorizedOrgControl
{
	public $pageTitle = "Добавление нового товара";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("orgId", $actor->id);

		// прокидываем режим - для редиректа в акшене сохранения данных
		$mode = Request::getVar("mode");
		$this->addData("mode", $mode);

		// id хэдера закупки для режима - addzakupkaline (добавление строки в закупку)
		$headid = Request::getVar("headid");
		$this->addData("headid", $headid);
		
	}
}
