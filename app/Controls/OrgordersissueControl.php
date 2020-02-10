<?php
/**
 * Орг выдает товары участникам
 *
 */

class OrgordersissueControl extends AuthorizedOrgControl
{
	public $pageTitle = "Раздача";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		// первое что покажем - форму для ввода кода выдачи
		// по нему получим список ников, у кого там что есть (по данному оргу)
		$code = Request::getInt("code");
		if ($code)
		{
			$ohm = new OrderHeadManager();
			$zhm = new ZakupkaHeaderManager();
			$ordersByCode = $ohm->getByCode($this->ownerSiteId, $this->ownerOrgId, $code);
			$ordersByOrg = array();
			if (count($ordersByCode))
			{
				// обираем список закупок
				$headIds = array();
				foreach ($ordersByCode AS $oneOrder)
				{
					if (!in_array($oneOrder->headId, $headIds))
						$headIds[] = $oneOrder->headId;
				}

				// выделим закупки только одного текущего организатора
				$zheadList = $zhm->getByIds($headIds, $actor->id);

				$headIds = array();
				$headDataById = array();
				if (count($zheadList))
				{
					foreach ($zheadList AS $oneZHead)
					{
						$headIds[] = $oneZHead->id;
						$headDataById[$oneZHead->id] = $oneZHead;
					}

					$this->addData("headDataById", $headDataById);

					$usersIds = array();
					$orderHeadIds = array();
					// придется ещё раз прокрутить массив заказов
					foreach ($ordersByCode AS $oneOrder)
					{
						if (in_array($oneOrder->headId, $headIds))
						{
							// на $oneOrder надо навесить долги участника
							$zhObj = $headDataById[$oneOrder->headId];

							$oneOrder->orderSumAmount = $oneOrder->optAmount + round($oneOrder->optAmount*$zhObj->orgRate/100, 2);
							// сколько человек должен денег
							// payHold не считаем, т.к. интересно только то что было 100% подтверждено
							$oneOrder->needAmount = round($oneOrder->orderSumAmount - $oneOrder->payAmount + $oneOrder->payBackAmount + $oneOrder->opttoorgDlvrAmount, 2);

							$usersIds[] = $oneOrder->userId;
							$ordersByOrg[$oneOrder->userId][] = $oneOrder;
							$orderHeadIds[] = $oneOrder->id;
						}
					}

					if (count($ordersByOrg))
					{
						$this->addData("ordersByOrg", $ordersByOrg);
						$this->addData("code", $code);
						// пользователей для ников выведем
						if (count($usersIds))
						{
							$um = new UserManager();
							$userList = $um->getByIds($usersIds);
							$this->addData("userList", $userList);
						}

						// а ещё надо бы список кто что заказал ))
						$olm = new OrderListManager();
						$orderLineList = $olm->getLinesByHeadIds($orderHeadIds, OrderList::STATUS_CONFIRM);
						if (count($orderLineList))
						{
							$orderLinesByUser = array();
							foreach ($orderLineList AS $oneLine)
								$orderLinesByUser[$oneLine->userId][$oneLine->orderId][] = $oneLine;

							$this->addData("orderList", $orderLinesByUser);
						}

					}

					// это не красиво, но затачивается с прицелом на ЦО,
					// поэтому в детализации заказа пока нет информации
					// о том где он (у кого)

				}

			}
			else
			{
				Context::setError("По заданному коду не найдено заказов к выдаче");
			}

		}

		// нужно убедиться, что заказ оплачен!!!
		// поиск нужен по номеру заказа!!!

	}

}
