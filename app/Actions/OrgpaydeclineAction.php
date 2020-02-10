<?php
/**
* Действие для отклонения оплаты участника за закупку
*/

class OrgpaydeclineAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$payid = Request::getInt('payid');
		if (!$payid)
			Enviropment::redirectBack("Не верный ID операции");

		$pm = new PayManager();
		$payObj = $pm->getById($payid);
		if (!$payObj)
			Enviropment::redirectBack("Не найдена операция оплаты");

		if ($payObj->orgId != $this->actor->id || $this->ownerSiteId != $payObj->ownerSiteId || $this->ownerOrgId != $payObj->ownerOrgId)
			Enviropment::rredirectBack("Не достаточно прав для данного действия");

		$userId = $payObj->userId;
		$headId = $payObj->headId;
		$amount = $payObj->amount;

		$om = new OrderHeadManager();
		$orderHeadObj = $om->getByHeadAndUserId($headId, $userId);
		if (!$orderHeadObj)
			Enviropment::redirectBack("Не найден заказ");

		if ($this->ownerSiteId != $orderHeadObj->ownerSiteId || $this->ownerOrgId != $orderHeadObj->ownerOrgId)
			Enviropment::rredirectBack("Не достаточно прав для данного действия");

        // получим доставку и сумму по ней
        $oom = new OfficeOrderManager();
        $officeOrderObj = $oom->getByOrderIdUserId($orderHeadObj->id, $userId);

		// стартуем транзакцию
		$pm->startTransaction();
		try
		{
			// надо поменять статус платежа в pay
			$payObj->dateConfirm = time();
			$payObj->status = Pay::STATUS_CANCEL;
			$pm->save($payObj);

            $otherPayment = $amount - $orderHeadObj->payHold;

            // убрать из заказа pendingAmount в сумме платежа
            if ($otherPayment > 0)
            {
                $orderHeadObj->payHold = 0;
                // $otherPayment можно использовать в счёт погашения доставки
                if ($officeOrderObj)
                {
                    $needPayOffice = $officeOrderObj->price - $officeOrderObj->payHold - $officeOrderObj->payAmount + $officeOrderObj->payBackAmount;
                    if ($needPayOffice > 0)
                    {
                        $otherPaymentAfterPayOffice = $otherPayment - $officeOrderObj->payHold;
                        $officeOrderObj->payHold = $officeOrderObj->payHold - $needPayOffice;
                        $oom->save($officeOrderObj);
                    }
                }
            }
            else
            {
                $orderHeadObj->payHold = $orderHeadObj->payHold - $amount;
            }

			$om->save($orderHeadObj);

		}
		catch (Exception $e)
		{
			$pm->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirectBack("Ошибка отклонения операции");
		}

		$pm->commitTransaction();

		// всё готово
		Enviropment::redirectBack("Оплата отклонена");

	}

}
