<?php
/**
* Ajax-овый контрол для запросов из html
*/

class AjaxControl extends BaseControl implements IAjaxControl
{
	public function render()
	{
		$actor = Context::getActor();
		if (!$actor)
		{
			echo json_encode("noActor");
			exit;
		}

		$job = Request::getVar("job");

		// установка наличия на заказе
		if ($job == "orgorderconfirm")
		{
			$mode = Request::getVar("mode");
			$headid = Request::getInt("headid");
			$zlorderid = Request::getInt("zlorderid");

			if ((!$headid && !$zlorderid) || !$mode)
			{
				echo json_encode("noInputData");
				exit;
			}

			$orderLineObj = null;
			$olm = new OrderListManager();
			if ($zlorderid)
			{
				$orderLineObj = $olm->getById($zlorderid);
				if (!$orderLineObj)
				{
					echo json_encode("noOrder");
					exit;
				}

				if ($this->ownerSiteId != $orderLineObj->ownerSiteId || $this->ownerOrgId != $orderLineObj->ownerOrgId)
				{
					echo json_encode("noOrder");
					exit;
				}

				$headid = $orderLineObj->headId;

				// проверим надо ли что-то делать
				if ($mode == "oneorderreject" && $orderLineObj->status == OrderList::STATUS_REJECT)
				{
					echo json_encode("alreadyRejected");
					exit;
				}

				if ($mode == "oneorderconfirm" && $orderLineObj->status == OrderList::STATUS_CONFIRM)
				{
					echo json_encode("alreadyConfirmed");
					exit;
				}

			}

			if (!$headid)
			{
				echo json_encode("noHeadId");
				exit;
			}

			$zhm = new ZakupkaHeaderManager();
			// найдём закупку, проверим того ли она орга
			$zhObj = $zhm->getById($headid);
			if (!$zhObj)
			{
				echo json_encode("noZakupka");
				exit;
			}

			if ($zhObj->orgId != $actor->id || $this->ownerSiteId != $zhObj->ownerSiteId || $this->ownerOrgId != $zhObj->ownerOrgId)
			{
				echo json_encode("noAccess");
				exit;
			}

			if (!in_array($zhObj->status, array(ZakupkaHeader::STATUS_STOP, ZakupkaHeader::STATUS_CHECKED, ZakupkaHeader::STATUS_SEND, ZakupkaHeader::STATUS_DELIVERED)))
			{
				echo json_encode("invalidStatus");
				exit;
			}

			// стартуем транзакцию
			$isAction = false;
			$zhm->startTransaction();
			try
			{
				// режимы для проставления наличия по одному заказу
				if ($mode == "oneorderreject" && $orderLineObj)
				{
					$olm->rejectOrder($orderLineObj->id, $orderLineObj->userId, $orderLineObj->headId);
					$isAction = true;
				}

				if ($mode == "oneorderconfirm" && $orderLineObj)
				{
					$olm->confirmOrder($orderLineObj->id, $orderLineObj->userId, $orderLineObj->headId);
					$isAction = true;
				}

			}
			catch (Exception $e)
			{
				$zhm->rollbackTransaction();
				Logger::error($e->getMessage());
				echo json_encode("cancelTransaction");
				exit;
			}

			$zhm->commitTransaction();

			if ($isAction)
			{
				echo json_encode("done");
				exit;
			}
			else
			{
				echo json_encode("notDone");
				exit;
			}

			echo json_encode("noAction");
			exit;

		}


        // сохранить выбранный офис
        if ($job == "usersaveoffice")
        {
            $orderid = Request::getInt("orderid");
            if (!$orderid)
            {
                echo json_encode("noId");
                exit;
            }

            $officeid = Request::getInt("officeid");

            $oom = new OfficeOrderManager();

            if ($officeid > 0)
            {
                if (!$oom->saveOfficeToOdrer($orderid, $actor->id, $officeid))
                {
                    echo json_encode("notsaved");
                    exit;
                }
            }
            else
            {
                // заберёт у организатора, но этот кейс тоже нужно зафиксировать в БД
                if (!$oom->saveOrgAsOfficeToOdrer($orderid, $actor->id))
                {
                    echo json_encode("notsaved");
                    exit;
                }
            }

            echo json_encode("done");
            exit;

        }

        // информация об офисе
        if ($job == "usergetoffice")
        {
            $officeid = Request::getInt("officeid");
            if (!$officeid)
            {
                echo json_encode("noId");
                exit;
            }

            $om = new OfficeManager();
            $office = $om->getById($officeid);
            if (!$office)
            {
                echo json_encode("noOffice");
                exit;
            }

            echo json_encode((array) $office);
            exit;

        }


		// ставим сумму за доставку
		// в целом по всей закупке
		if ($job == "dlvrprall")
		{
			$amount = Request::getVar("amount");
			$headid = Request::getInt("headid");
			if (!$headid)
			{
				echo json_encode("noId");
				exit;
			}

			if ($amount)
				$amount = str_replace(',', '.', $amount);

			$amount = round(floatVal($amount), 2);

			if ($amount <= 0)
			{
				echo json_encode("noAmount");
				exit;
			}

			$zhm = new ZakupkaHeaderManager();
			$zakObj = $zhm->getById($headid);
			if (!$zakObj)
			{
				echo json_encode("noZak");
				exit;
			}

			if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			{
				echo json_encode("noRights");
				exit;
			}

			// всё нормально, распределяем
			$zhm->setDeliveryAmount($zakObj->id, $zakObj->curAmount, $amount);

			echo json_encode("done");
			exit;

		}


		// ставим сумму за доставку
		// по заказу, в id есть id заказа
		if ($job == "dlvrprwhlorder")
		{
			$amount = Request::getVar("amount");
			$id = Request::getInt("id");
			if (!$id)
			{
				echo json_encode("noId");
				exit;
			}

			if ($amount)
				$amount = str_replace(',', '.', $amount);

			$amount = round(floatVal($amount), 2);

			if ($amount <= 0)
			{
				echo json_encode("noAmount");
				exit;
			}

			$ohm = new OrderHeadManager();
			$orderHeadObj = $ohm->getById($id);
			if (!$orderHeadObj)
			{
				echo json_encode("noOrder");
				exit;
			}

			if ($this->ownerSiteId != $orderHeadObj->ownerSiteId || $this->ownerOrgId != $orderHeadObj->ownerOrgId)
			{
				echo json_encode("noOrder");
				exit;
			}

			$zhm = new ZakupkaHeaderManager();
			$zakObj = $zhm->getById($orderHeadObj->headId);
			if (!$zakObj)
			{
				echo json_encode("noZak");
				exit;
			}

			if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			{
				echo json_encode("noRights");
				exit;
			}

			// распределим сумму за доставку по строкам заказа
			$ohm->setDeliveryAmount($zakObj->id, $orderHeadObj->id, $orderHeadObj->optAmount, $amount, $orderHeadObj->opttoorgDlvrAmount);

			echo json_encode("done");
			exit;

		}

		// ставим сумму за доставку
		// по строке заказа, в id есть id строки заказа
		if ($job == "dlvrprordline")
		{
			$amount = Request::getVar("amount");
			$id = Request::getInt("id");
			if (!$id)
			{
				echo json_encode("noId");
				exit;
			}

			if ($amount)
				$amount = str_replace(',', '.', $amount);

			$amount = round(floatVal($amount), 2);

			if ($amount <= 0)
			{
				echo json_encode("noAmount");
				exit;
			}

			// это id сущности orderList
			$olm = new OrderListManager();
			$olineObj = $olm->getById($id);
			if (!$olineObj)
			{
				echo json_encode("noLine");
				exit;
			}

			if ($this->ownerSiteId != $olineObj->ownerSiteId || $this->ownerOrgId != $olineObj->ownerOrgId)
			{
				echo json_encode("noLine");
				exit;
			}

			$ohm = new OrderHeadManager();
			$orderHeadObj = $ohm->getById($olineObj->orderId);
			if (!$orderHeadObj)
			{
				echo json_encode("noOrder");
				exit;
			}

			if ($this->ownerSiteId != $orderHeadObj->ownerSiteId || $this->ownerOrgId != $orderHeadObj->ownerOrgId)
			{
				echo json_encode("noOrder");
				exit;
			}

			$zhm = new ZakupkaHeaderManager();
			$zakObj = $zhm->getById($orderHeadObj->headId);
			if (!$zakObj)
			{
				echo json_encode("noZak");
				exit;
			}

			if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			{
				echo json_encode("noRights");
				exit;
			}

			// запишем сумму за доставку в строку заказа
			$olm->setDeliveryAmount($zakObj->id, $orderHeadObj->id, $olineObj->id, $amount, $olineObj->opttoorgDlvrAmount);

			echo json_encode("done");
			exit;
		}

		// получить товар по артикулу
		if ($job == "getprodbyart")
		{
			$getArt = Request::getVar("art");
			if (!$getArt)
			{
				echo json_encode("noId");
				exit;
			}

			$pm = new ProductManager();
			$getProd = $pm->getByArt($this->ownerSiteId, $this->ownerOrgId, $getArt, $actor->id);
			if (!$getProd)
			{
				echo json_encode("noProd");
				exit;
			}

			echo json_encode($getProd);
			exit;
		}

		// орг переносит отмеченные заказы в офисы (одним запросом)
		if ($job == "ogrsetordertooffice")
		{
			$getIds = Request::getVar("orders");
			if (!$getIds)
			{
				echo json_encode("noIds");
				exit;
			}

			$idsArray = explode(',', $getIds);
			if (!count($idsArray))
			{
				echo json_encode("noIds");
				exit;
			}

			$newIdsArray = array();
			foreach ($idsArray as $oneId)
			{
				$oneId = intval($oneId);
				if ($oneId > 0) {
					$newIdsArray[] = $oneId;
				}
			}

			$oom = new OfficeOrderManager();
			$oom->orgSendOrdersToOffices($actor->id, $newIdsArray);

			echo json_encode("done");
			exit;
		}

        // информация о заказе
        if ($job == "getorderdetails")
        {
            $id = Request::getInt("id");
            if (!$id)
            {
                echo json_encode("noId");
                exit;
            }

            // TODO: надо добавиь защиту, т.е. проверять орг ли пользователь или у него
            // есть права на офисы и заказ в офисе, а пока просто отдадим информацию о заказе
            $ohm = new OrderHeadManager();
			$orderInfo = $ohm->getInfoById($this->ownerSiteId, $this->ownerOrgId, $id);
			echo json_encode($orderInfo);
			exit;
        }

		echo json_encode("noAction");
		exit;

	}

}
