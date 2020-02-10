<?php
/**
 * Контрол отказ доставить товар в офис
 *
 */

class OrgofficerejectControl extends AuthorizedOrgControl
{
	public $pageTitle = "Отказ доставки";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$officeorderid = Request::getInt("id");
		if (!$officeorderid)
			Enviropment::redirectBack("Не указан ID заказа доставки");

		$oom = new OfficeOrderManager();
		$ooObj = $oom->getById($officeorderid);
		if (!$ooObj)
			Enviropment::redirectBack("Не найден заказ доставки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($ooObj->headId);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		$this->addData("officeorderid", $officeorderid);

	}

}
