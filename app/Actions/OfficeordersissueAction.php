<?php
/**
* Выдать заказ участнику
*
*/
class OfficeordersissueAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		$id = Request::getInt("id");
		if (!$id)
			Enviropment::redirectBack("Не указан ID заказа");

		$ohm = new OrderHeadManager();
		$orderObj = $ohm->getById($id);
		if (!$orderObj)
			Enviropment::redirect("officeordersissue", "Не найден заказ");

		$oom = new OfficeOrderManager();
		$isInOfficeObj = $oom->isAwaitingInOffice($id, $orderObj->userId);
		if (!$isInOfficeObj)
			Enviropment::redirect("officeordersissue", "Выбранного заказа нет в офисе");

		if ($this->ownerSiteId != $orderObj->ownerSiteId || $this->ownerOrgId != $orderObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($orderObj->headId);
		if (!$zakObj)
			Enviropment::redirect("officeordersissue", "Не найдена закупка");

		if ($this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirect("officeordersissue", "Не достаточно прав для выдачи данного заказа");

		// ну а если всё хорошо, то ..
		$orderObj->status = OrderHead::STATUS_USER;
		$orderObj->dateUser = time();
		$ohm->save($orderObj);

		// а так же надо отметить выдачу officeorder
		$isInOfficeObj->tsUser = time();
		$isInOfficeObj->officeUserId = $actor->id;
		$isInOfficeObj->status = OfficeOrder::STATUS_USER;
		$oom->save($isInOfficeObj);

		Enviropment::redirect("officeordersissue", "Заказ выдан участнику");

	}

}
