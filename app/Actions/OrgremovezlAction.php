<?php
/**
* Орг удаляет ряд в закупке
*/

class OrgremovezlAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		// закупка
		$headid = Request::getInt("headid");
		// ряд
		$id = Request::getInt("id");
		if (!$id)
			Enviropment::redirectBack("Не указан ID ряда");

		$zlm = new ZakupkaLineManager();
		$zlineObj = $zlm->getById($id);
		if (!$zlineObj)
			Enviropment::redirectBack("Не найден ряд");

		$actor = $this->actor;
		if ($zlineObj->orgId != $actor->id || $this->ownerSiteId != $zlineObj->ownerSiteId || $this->ownerOrgId != $zlineObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$zlineObj->status = ZakupkaLine::STATUS_HIDDEN;
		$zlm->save($zlineObj);

		$isRemoved = false;
		// если нет заказов по данному ряду,
		// то удаляем его к херам собачьим
		$olm = new OrderListManager();
		$getOrders = $olm->getByZlId($zlineObj->id);
		if (!count($getOrders))
		{
			$zlm->remove($id);
			$isRemoved = true;
		}

		// всё готово
		if ($isRemoved)
			Enviropment::redirectBack("Ряд удален");
		else
			Enviropment::redirectBack("Есть заказы, удалить не удалось, ряд скрыт");

	}

}
