<?php
/**
 * Контрол загрузка рядов из внешнего файла
 *
 */
class OrgimportzakupkaprocessControl extends AuthorizedOrgControl
{
	public $pageTitle = "Загрузка рядов из файла";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// всё хорошо, покажем форму рассылки
		$this->addData("headid", $headid);
		$this->addData("zakObj", $zakObj);

		$sessHash = Enviropment::getBasketGUID();
		$countLines = Csvfile::countLines($sessHash, $actor->id, $headid, $this->ownerSiteId, $this->ownerOrgId);
		$this->addData("countLines", $countLines);

		$oneLine = Csvfile::getOneString($sessHash, $actor->id, $headid, $this->ownerSiteId, $this->ownerOrgId);
		$this->addData("oneLine", $oneLine);

		// какие данные можно загрузить в ряды
		$loadArray = array(1 => "Наименование товара", 2 => "Артикул", 3 => "Цена", 4 => "Cсылка на картинку", 5 => "Описание", 6 => "Ряд (ячейка)");
		$this->addData("loadArray", $loadArray);

	}

}
