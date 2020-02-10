<?php
/**
 * Контрол для визуального представления окружения покупателя
 * после входа в систему
 *
 */
class BasketHeadControl extends AuthorizedUserControl
{
	public $pageTitle = "Корзина";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);

		// поднять головы заказов orderHead
		$ohm = new OrderHeadManager();
		$orderHeadList = $ohm->getByUserId($actor->id);
		if (count($orderHeadList))
		{
			// т.к. есть заказы, поднимем и список офисов для доставки
            $officeArray = array();
            $orgObj = array("id" => 0, "name" => "У организатора", "address" => null, "schedule" => null, "price" => 0, "status" => 'STATUS_ENABLED', "ownerSiteId" => $this->ownerSiteId, "ownerOrgId" => $this->ownerOrgId);
            $officeArray[0] = (object) $orgObj;

			$ofm = new OfficeManager();
			$offices = $ofm->getByStatus($this->ownerSiteId, $this->ownerOrgId, Office::STATUS_ENABLED);
			if ($offices)
			{
				foreach ($offices AS $oneOffice)
					$officeArray[$oneOffice->id] = $oneOffice;
			}

			$orderHeadIds = array();
			$zakHeadIds = array();
			foreach ($orderHeadList AS $oneOrderHead)
			{
				$orderHeadIds[] = $oneOrderHead->id;
				$zakHeadIds[] = $oneOrderHead->headId;
			}

			// для поднятых голов заказов поднять строки orderList
			$olm = new OrderListManager();
			$orderLineDBList = $olm->getLinesByHeadIds($orderHeadIds);
			$orderLineList = array();
			if (count($orderLineDBList))
			{
				foreach ($orderLineDBList AS $oneOrderLine)
					$orderLineList[$oneOrderLine->headId][] = $oneOrderLine;

			}

			$this->addData("orderLineList", $orderLineList);

			// для голов заказов поднять головы закупок zakupkaHeader
			$zhm = new ZakupkaHeaderManager();
			$zakHeadList = $zhm->getByIds($zakHeadIds);

			// собрать id организаторов
			$orgIds = array();
			// закупки с индексов - id закупки
			if (count($zakHeadList))
			{
				foreach($zakHeadList AS $oneZakHead)
				{
					$orgIds[$oneZakHead->orgId] = $oneZakHead->orgId;
					$oneZakHead->statusName = ZakupkaHeader::getStatusDesc($oneZakHead->status);
					$zakHeadListIdIdx[$oneZakHead->id] = $oneZakHead;
				}
			}

			$this->addData("zakHeadList", $zakHeadListIdIdx);

            // построим массив заказанного в офисы текущим пользователем
            $oom = new OfficeOrderManager();
            $officeOrders = null;
            $officeOrdersArray = array();
            $officeOrdersIdsOffices = array();
            if (count($orderHeadIds))
            {
                $officeOrders = $oom->getByOrderIds($orderHeadIds, $actor->id);
                // кроме того что это передадим в шаблон по id заказов
                // так ещё и соберем массив id офисов
                if (count($officeOrders))
                {
                    foreach ($officeOrders AS $oneOfficeOrder)
                    {
                        $officeOrdersArray[$oneOfficeOrder->orderId] = $oneOfficeOrder;
                        $officeOrdersIdsOffices[$oneOfficeOrder->officeId] = $oneOfficeOrder->officeId;
                    }

                    $officeOrdersOfficesArray = array();
                    $this->addData("officeOrders", $officeOrdersArray);

                    $officeOrdersObjOffices = $ofm->getByIds($officeOrdersIdsOffices);
                    if (count($officeOrdersObjOffices))
                    {
                        foreach ($officeOrdersObjOffices AS $oneOfficeOrderObj)
                        {
                            $listIsDisabled = false;
                            if ($oneOfficeOrderObj->status != OfficeOrder::STATUS_NEW || $oneOfficeOrderObj->payStatus != OfficeOrder::STATUS_PAY_NONE)
                                $listIsDisabled = true;

                            $oneOfficeOrderObj->listIsDisabled = $listIsDisabled;
                            $officeOrdersOfficesArray[$oneOfficeOrderObj->id] = $oneOfficeOrderObj;
                        }

                        $this->addData("officesFromOrders", $officeOrdersOfficesArray);

                    }
                }
            }

            if ($offices || $officeOrders)
                $this->addData("offices", $officeArray);

			// добавим ещё общую сумму заказов
			foreach ($orderHeadList AS $oneOrderHead)
			{
                // сколько стоит доставка в офис
                $gotOfficeOrder = 0;
                $officeOrderSumAmount = 0;
                $officeNeedAmount = 0;
                $officePayHold = 0;
                if (isset($officeOrdersArray[$oneOrderHead->id]))
                {
                    $gotOfficeOrder = $officeOrdersArray[$oneOrderHead->id];
                    $officeOrderSumAmount = $gotOfficeOrder->price;
                    $officeNeedAmount = $gotOfficeOrder->price - $gotOfficeOrder->payHold - $gotOfficeOrder->payAmount + $gotOfficeOrder->payBackAmount;
                    $officePayHold = $gotOfficeOrder->payHold;
                }

				// сумма с орг сбором
				$oneOrderHead->orderSumAmount = $oneOrderHead->optAmount + round($oneOrderHead->optAmount*$zakHeadListIdIdx[$oneOrderHead->headId]->orgRate/100, 2) + $officeOrderSumAmount;
				// сколько человек должен денег
				$oneOrderHead->needAmount = round($oneOrderHead->orderSumAmount - $oneOrderHead->payAmount + $oneOrderHead->payBackAmount - $oneOrderHead->payHold + $oneOrderHead->opttoorgDlvrAmount + $officeNeedAmount, 2);
                // на одобрении
                $oneOrderHead->payHold = $oneOrderHead->payHold + $officePayHold;
			}

			$this->addData("orderHeadList", $orderHeadList);

			$um = new UserManager();
			$orgList = $um->getByIds($orgIds);
			$this->addData("orgList", $orgList);

			// статусы строк заказа
			$this->addData("zakListStatusNames", OrderList::getStatusDesc());

		}
	}
}
