<?php
/**
* Выдать заказ участнику
*
*/
class OrgordersissueAction extends AuthorizedOrgAction implements IPublicAction
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
			Enviropment::redirect("orgordersissue", "Не найден заказ");

		if ($this->ownerSiteId != $orderObj->ownerSiteId || $this->ownerOrgId != $orderObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($orderObj->headId);
		if (!$zakObj)
			Enviropment::redirect("orgordersissue", "Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirect("orgordersissue", "Не достаточно прав для выдачи данного заказа");

		// ну а если всё хорошо, то ..
		$orderObj->status = OrderHead::STATUS_USER;
		$orderObj->dateUser = time();
		$ohm->save($orderObj);

		Enviropment::redirect("orgordersissue", "Заказ выдан участнику");

	}

}
