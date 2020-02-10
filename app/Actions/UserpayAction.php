<?php
/**
 * Сохранение информации об оплате заказа
 *
 * oheadid - orderHead id
 *
*/

class UserpayAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$oheadid = FilterInput::add(new IntFilter("oheadid", true, "ID заказа"));
		$amount = FilterInput::add(new StringFilter("amount", true, "Сумма"));
		$userInfo = FilterInput::add(new StringFilter("userInfo", true, "Информация об оплате"));

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
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		$zhm = new ZakupkaHeaderManager();
		$headObj = $zhm->getById($oheadObj->headId);
		if (!$headObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		$actor = $this->actor;

        $officePayHold = 0;
        $officePrice = 0;
        $officePayAmount = 0;
        $officePayBackAmount = 0;

        // получим доставку и сумму по ней
        $oom = new OfficeOrderManager();
        $officeOrderObj = $oom->getByOrderIdUserId($oheadid, $actor->id);
        if ($officeOrderObj)
        {
            $officePayHold = $officeOrderObj->payHold;
            $officePrice = $officeOrderObj->price;
            $officePayAmount = $officeOrderObj->payAmount;
            $officePayBackAmount = $officeOrderObj->payBackAmount;
        }

        $needPayOffice = $officePrice - $officePayHold - $officePayAmount + $officePayBackAmount;

		$pm = new PayManager();
		$pm->startTransaction();
		try
		{
			$payObj = new Pay();
			$payObj->userId = $actor->id;
			$payObj->orgId = $headObj->orgId;
			$payObj->headId = $oheadObj->headId;
			$payObj->amount = $amount;
			$payObj->dateCreate = time();
			$payObj->userInfo = $userInfo;
			// за заказ
			$payObj->type = Pay::TYPE_ORDER;
			// ручной перевод
			$payObj->way = Pay::WAY_HAND_PAY;
			// статус - новая операция (в обработке)
			$payObj->status = Pay::STATUS_NEW;
			$payObj->ownerSiteId = $this->ownerSiteId;
			$payObj->ownerOrgId = $this->ownerOrgId;
			$pm->save($payObj);

			// в заказ добавим payHold
            $oheadObj->payHold = $oheadObj->payHold + ( $amount - $needPayOffice );
			$ohm->save($oheadObj);

            // в заказе доставки в офис тоже добавим payHold
            if ($officeOrderObj)
            {
                $officeOrderObj->payHold = $officeOrderObj->payHold + $needPayOffice;
                $oom->save($officeOrderObj);
            }

		}
		catch (Exception $e)
		{
			$pm->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirect("userarea", "Не удалось сохранить данные");
		}

		$pm->commitTransaction();

		Enviropment::redirect("finanses" ,"Информация об оплате отправлена организатору");

	}

}
