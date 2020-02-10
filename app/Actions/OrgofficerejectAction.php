<?php
/**
* Рассылка участникам закупки
*
*/
class OrgofficerejectAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		// само сообщение
		$message = Request::getVar("message");
		if (!$message || $message == '')
			Enviropment::redirectBack("Сообщение не должно быть пустым");

		if (mb_strlen($message) > 255)
			Enviropment::redirectBack("Слишком длинное сообщение, можно 120 символов, введено: ".round(mb_strlen($message)/2));

		$officeorderid = Request::getInt("officeorderid");
		if (!$officeorderid)
			Enviropment::redirectBack("Не указан ID заказа доставки");

		$oom = new OfficeOrderManager();
		$ooObj = $oom->getById($officeorderid);
		if (!$ooObj)
			Enviropment::redirectBack("Не найден заказ доставки");

        $zhm = new ZakupkaHeaderManager();
        $zakObj = $zhm->getById($ooObj->headId);

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// сохраняем сообщение, убираем в БД стоимость офиса
        $ooObj->rejectReason = $message;
        $ooObj->status = OfficeOrder::STATUS_REJECTED;
        $ooObj->price = 0;
        $oom->save($ooObj);

		Enviropment::redirect("orgviewzakupka/headid/".$ooObj->headId."/mode/offices", "В доставке отказано");

	}

}
