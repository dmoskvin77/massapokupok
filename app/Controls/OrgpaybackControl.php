<?php
/**
 * Форма для сообщения о возврате оргом денег участнику
 *
 */
class OrgpaybackControl extends AuthorizedOrgControl
{
	public $pageTitle = "Возврат оплаты участнику";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$oheadid = Request::getInt('oheadid');
		if (!$oheadid)
			Enviropment::redirectBack("Не указан ID заказа");

		$ohm = new OrderHeadManager();
		$oheadObj = $ohm->getById($oheadid);
		if (!$oheadObj)
			Enviropment::redirectBack("Не найден заказ");

		if ($this->ownerSiteId != $oheadObj->ownerSiteId || $this->ownerOrgId != $oheadObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на редактирование закупки");

		$this->addData("oheadid", $oheadid);

		// закупка
		$zhm = new ZakupkaHeaderManager();
		$zakHeadObj = $zhm->getById($oheadObj->headId);
		if (!$zakHeadObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakHeadObj->orgId != $actor->id || $this->ownerSiteId != $zakHeadObj->ownerSiteId || $this->ownerOrgId != $zakHeadObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на редактирование закупки");

		// сумма с орг сбором
		$orderSumAmount = $oheadObj->optAmount + round($oheadObj->optAmount*$zakHeadObj->orgRate/100, 2);

		// оплачено
		$paid = $oheadObj->payAmount - $oheadObj->payBackAmount;

		// сколько человек должен денег
		$needAmount = round($orderSumAmount - $oheadObj->payAmount + $oheadObj->payBackAmount - $oheadObj->payHold + $oheadObj->opttoorgDlvrAmount, 2);

		$overAmount = 0;
		if ($needAmount < 0)
		{
			$overAmount = (-1)*$needAmount;
			$needAmount = 0;
		}

		$this->addData("overAmount", $overAmount);

	}

}
