<?php
/**
 * Сохранение информации о возврате денег от орга участнику
 *
 * oheadid - orderHead id
 *
*/

class OrgpaybackAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$oheadid = FilterInput::add(new IntFilter("oheadid", true, "ID заказа"));
		$amount = FilterInput::add(new StringFilter("amount", true, "Сумма"));
		$userInfo = FilterInput::add(new StringFilter("userInfo", true, "Информация о возврате"));

		$amount = round($amount, 2);
		if ($amount <= 0)
			FilterInput::addMessage("Сумма должна больше нуля");

		if (!FilterInput::isValid())
		{
			FormRestore::add("user-pay");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		$ohm = new OrderHeadManager();
		$oheadObj = $ohm->getById($oheadid);
		if (!$oheadObj)
			Enviropment::redirectBack("Не найден заказ");

		if ($this->ownerSiteId != $oheadObj->ownerSiteId || $this->ownerOrgId != $oheadObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$zhm = new ZakupkaHeaderManager();
		$headObj = $zhm->getById($oheadObj->headId);
		if (!$headObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($headObj->orgId != $this->actor->id || $this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
			Enviropment::redirect("orgordersissue", "Нет прав на выполнение данного действия");

		$actor = $this->actor;

		$pm = new PayManager();
		$pm->startTransaction();
		try
		{
			$ts = time();

			$payObj = new Pay();
			$payObj->userId = $oheadObj->userId;
			$payObj->orgId = $actor->id;
			$payObj->headId = $oheadObj->headId;
			$payObj->amount = (-1)*$amount;
			$payObj->dateCreate = $ts;
			$payObj->userInfo = $userInfo;
			// за заказ
			$payObj->type = Pay::TYPE_BACK;
			// ручной перевод
			$payObj->way = Pay::WAY_HAND_PAY;

			// статус - сразу подтверждено
			$payObj->status = Pay::STATUS_SUCCED;
			$payObj->dateConfirm = $ts;

			$payObj->ownerSiteId = $this->ownerSiteId;
			$payObj->ownerOrgId = $this->ownerOrgId;

			$pm->save($payObj);

			// в заказ добавим payBackAmount
			$oheadObj->payBackAmount = $oheadObj->payBackAmount + $amount;
			$ohm->save($oheadObj);

		}
		catch (Exception $e)
		{
			$pm->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirect("userarea", "Не удалось сохранить данные");
		}

		$pm->commitTransaction();

		Enviropment::redirect("finanses" ,"Возврат записан, ожидается подтверждение от участника");

	}

}
