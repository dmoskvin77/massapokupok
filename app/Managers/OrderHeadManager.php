<?php
/**
 * Менеджер управления головами заказов
 */
class OrderHeadManager extends BaseEntityManager
{
	// сколько заказов в корзине
	public function getOrdersCount($userId)
	{
		$cnt = 0;
		$sql = "SELECT SUM(allProdCount) AS cnt FROM orderHead WHERE userId = {$userId}";
		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$cnt = $cnt + $res['cnt'];

		return $cnt;
	}


	// объект по пользователю
	public function getByUserId($userId)
	{
		$condition = "userId = {$userId}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "dateCreate DESC";
		return $this->get($sql);

	}


	// не полученные ещё заказы по коду
	public function getByCode($ownerSiteId, $ownerOrgId, $code)
	{
		$condition = "code = {$code} AND status != '".OrderHead::STATUS_USER."' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "dateCreate DESC";
		return $this->get($sql);

	}


	// все головы заказов по headid
	public function getByHeadId($headid, $onlyPositive = false)
	{
		$headid = intval($headid);
		if ($headid > 0)
		{
			$condition = "headId = {$headid}";
			if ($onlyPositive)
				$condition .= " AND optAmount > 0";

			$sql = new SQLCondition($condition);
			return $this->get($sql);
		}
	}


	public function setDeliveryAmount($headid, $orderid, $orderOptAmount, $amount, $oldAmount)
	{
		// всю сумму ставим в заказ
		$sql1 = "UPDATE orderHead SET opttoorgDlvrAmount = {$amount} WHERE id = {$orderid}";
		$this->executeNonQuery($sql1);

		$sql2 = "UPDATE orderList SET opttoorgDlvrAmount = optPrice * count / {$orderOptAmount} * {$amount} WHERE orderId = {$orderid}";
		$this->executeNonQuery($sql2);

		// как изменилась сумма
		$deltaAmount = $amount - $oldAmount;
		$sql3 = "UPDATE zakupkaHeader SET opttoorgDlvrAmount = opttoorgDlvrAmount + {$deltaAmount} WHERE id = {$headid}";
		$this->executeNonQuery($sql3);

		// нельзя уходить в минус
		$sql4 = "UPDATE zakupkaHeader SET opttoorgDlvrAmount = 0 WHERE id = {$headid} AND opttoorgDlvrAmount < 0";
		$this->executeNonQuery($sql4);

		return true;
	}

    public function setOrgRateByHeadId($headid, $orgrate)
    {
        $sql = "UPDATE orderHead SET orgRate = {$orgrate} WHERE headId = {$headid}";
        $this->executeNonQuery($sql);
    }

	// полый пересчёт заказа
	public function rebuildOrder($userId, $headId)
	{
		$orderHeadObj = $this->getByUserZakId($userId, $headId);
		if (!$orderHeadObj)
			throw new Exception("Не найден заказ");

		$olm = new OrderListManager();
		$orderLineList = $olm->getByUserIdHeadId($userId, $headId);

		// если заказ пустой, нет смысла его держать в базе, удаляем
		// но только если взаиморасчёты сведены к нулю
		if (count($orderLineList) || $orderHeadObj->payAmount + $orderHeadObj->payHold > $orderHeadObj->payBackAmount)
		{
			// нельзя удалять
		}
		else
		{
			$this->remove($orderHeadObj->id);
			return false;
		}

		// пересмотр всех сумм
		// allProdCount, confirmedProdCount, optAmount
		$allProdCount = 0;
		$confirmedProdCount = 0;
		$optAmount = 0;
		if ($orderLineList)
		{
			foreach ($orderLineList AS $oneLine)
			{
				$allProdCount = $allProdCount + $oneLine->count;
				if ($oneLine->status == OrderList::STATUS_CONFIRM)
					$confirmedProdCount++;

				if ($oneLine->status == OrderList::STATUS_CONFIRM || $oneLine->status == OrderList::STATUS_NEW)
					$optAmount = $optAmount + $oneLine->optPrice * $oneLine->count;

			}
		}

		$orderHeadObj->allProdCount = $allProdCount;
		$orderHeadObj->confirmedProdCount = $confirmedProdCount;
		$orderHeadObj->optAmount = $optAmount;
		$this->save($orderHeadObj);

		// после ребилда надо ставить задачу на пересчёт суммы набранности закупки в очередь
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask('calcnarate', null, null, $orderHeadObj->headId);

		return true;
	}


	// добавить новый заказ, если нет его её
	public function addNewOrder($ownerSiteId, $ownerOrgId, $userId, $headId)
	{
		$ts = time();
		$code = rand(100, 999);
		$sql = "INSERT IGNORE INTO `orderHead` (userId, headId, code, dateCreate, dateUpdate, status, ownerSiteId, ownerOrgId) VALUES ({$userId}, {$headId}, {$code}, {$ts}, {$ts}, 'STATUS_NEW', {$ownerSiteId}, {$ownerOrgId})";
		$this->executeNonQuery($sql);
	}


	// получить голову заказа по $userId, $headId
	public function getByUserZakId($userId, $headId)
	{
		$condition = "userId = {$userId} AND headId = {$headId}";
		$sql = new SQLCondition($condition);
		return $this->getOne($sql);
	}


	// получим список пользователей, которые заказывали что-то в данной закупке
	public function getUserIds($headid)
	{
		$headid = intval($headid);
		if ($headid > 0)
		{
			$sql = "SELECT
						DISTINCT userId
					FROM
						orderHead
					WHERE
						headId  = {$headid} AND optAmount > 0";

			$res = $this->getColumn($sql);
			return $res;
		}

	}


	// голова заказов по headid и id заказчика
	public function getByHeadAndUserId($headid, $userid)
	{
		$headid = intval($headid);
		$userid = intval($userid);

		if ($headid > 0 && $userid > 0)
		{
			$condition = "headId = {$headid} AND userId = {$userid}";
			$sql = new SQLCondition($condition);
			return $this->getOne($sql);
		}

	}

	// огромная функция для добавление заказа в ячейку ряда
	// функцию можно вызывать только внутри транзакции
	public function placeRowOrder($actor, $headObj, $zlineObj, $prodObj, $rp, $num, $ownerSiteId, $ownerOrgId)
	{
		$ts = time();
		$zhm = new ZakupkaHeaderManager();
		$zlm = new ZakupkaLineManager();

		// добавить голову заказа, если её ещё нет
		$this->addNewOrder($ownerSiteId, $ownerOrgId, $actor->id, $headObj->id);
		$orderHeadObj = $this->getByUserZakId($actor->id, $headObj->id);
		if (!$orderHeadObj)
			throw new Exception("Не найден заказ");

		if ($ownerSiteId != $orderHeadObj->ownerSiteId || $ownerOrgId != $orderHeadObj->ownerOrgId)
			throw new Exception("Нет прав для выполнения данного действия");

		// добавить новую строку
		$olm = new OrderListManager();
		$olObj = new OrderList();
		$olObj->orderId = $orderHeadObj->id;
		$olObj->userId = $actor->id;
		$olObj->orgId = $headObj->orgId;
		$olObj->headId = $zlineObj->headId;
		$olObj->zlId = $zlineObj->id;
		$olObj->rp = $rp;
		$olObj->num = $num;
		$olObj->prodId = $zlineObj->productId;
		$olObj->prodName = $prodObj->name;
		$olObj->prodArt = $prodObj->artNumber;
		$olObj->optPrice = $zlineObj->wholePrice;
		$olObj->count = 1;
		$olObj->status = OrderList::STATUS_NEW;
		$olObj->dateCreate = $ts;
		$olObj->dateUpdate = $ts;
		$olObj->ownerSiteId = $ownerSiteId;
		$olObj->ownerOrgId = $ownerOrgId;

		// увеличим кол-во заказов в закупке
		$headObj->orderCount = $headObj->orderCount + 1;
		$zhm->save($headObj);

		// проапдейтим ряд, запишем ник участника туда
		$dbSizes = @unserialize($zlineObj->sizes);
		$gotChoosenSizes = @unserialize($zlineObj->sizesChoosen);

		// когда там одна только полоска, а rowNumbers > 1,
		// то надо добавить к sizesChoosen ещё массив в конец для '_'.$i полосок

		// это у нас первый заказ
		if (!$gotChoosenSizes)
		{
			$gotChoosenSizes = array();
			if (count($dbSizes))
			{
				for ($i = 1; $i <= $zlineObj->rowNumbers; $i++)
				{
					foreach ($dbSizes AS $dbcsKey => $dbcsVal)
						$gotChoosenSizes[$dbcsKey."_".$i] = $dbcsVal;

				}
			}
		}
		else
		{
			// добавить в $gotChoosenSizes пустые ячейки для rowNumbers
			// если весь ряд заполнен (занят)
			if (count($dbSizes)*$zlineObj->rowNumbers > count($gotChoosenSizes))
			{
				for ($i = 1; $i <= $zlineObj->rowNumbers; $i++) {
					foreach ($dbSizes AS $dbcsKey => $dbcsVal) {
						if (!isset($gotChoosenSizes[$dbcsKey . "_" . $i]))
							$gotChoosenSizes[$dbcsKey . "_" . $i] = $dbcsVal;
					}
				}
			}
		}

		$oneSizeChoosen = "";
		$newChoosenSizes = array();
		$szCounter = 0;
		$usrCounter = 0;
		if (count($gotChoosenSizes))
		{
			foreach ($gotChoosenSizes AS $oneChoosenSizeKey => $oneChoosenSizeVal)
			{
				$szCounter++;
				if ($oneChoosenSizeKey == $rp.'_'.$num)
				{
					$newChoosenSizes[$oneChoosenSizeKey] = array("id" => $actor->id, "nick" => $actor->nickName);
					$oneSizeChoosen = $oneChoosenSizeVal;
					// пользователь сейчас тоже заказал позицию
					$usrCounter++;
				}
				else
					$newChoosenSizes[$oneChoosenSizeKey] = $oneChoosenSizeVal;

				// кол-во заказанных позиций
				if (is_array($oneChoosenSizeVal))
					$usrCounter++;
			}
		}

		if (is_array($oneSizeChoosen))
			throw new Exception("Выбранная ячейка ряда была выкуплена, пожалуйста выберете другую");

		// не надо ли добавить новую линию в ряд
		// из-за того, что размер полностью выкуплен
		$countValues = array();
		$cv1 = array_count_values($dbSizes);
		$maxRowLines = round(count($newChoosenSizes)/count($dbSizes));
		if (count($cv1))
		{
			foreach ($cv1 AS $cvKey => $cvVal)
				$countValues[$cvKey] = $cvVal * $maxRowLines;

		}

		// анализ надо ли добавлять ещё ряд
		$shouldAddANewRow = false;
		if (count($dbSizes) && count($newChoosenSizes) && count($countValues))
		{
			foreach ($newChoosenSizes AS $gchKey => $gchVal)
			{
				$splKeyArray = explode('_', $gchKey);
				if (is_array($gchVal))
					$countValues[$dbSizes[$splKeyArray[0]]] = $countValues[$dbSizes[$splKeyArray[0]]] - 1;
			}

			foreach ($countValues AS $cvVal)
			{
				if ($cvVal == 0)
				{
					// надо добавить ещё одну линию в ряд
					$shouldAddANewRow = true;
					// дело сделано
					break;
				}

			}

		}


		if ((string) $oneSizeChoosen != '' && (string) $oneSizeChoosen != '-' && (string) $oneSizeChoosen != '+')
			$olObj->size = (string) $oneSizeChoosen;

		$olm->save($olObj);

		$zlineObj->sizesChoosen = @serialize($newChoosenSizes);

		// если вписался крайний пользователь, добавим ещё ряд
		// либо надо добавить, потому что выкуплен размер
		if (($shouldAddANewRow && $zlineObj->isGrow == 1) || ($szCounter > 0 && $usrCounter >= $szCounter && $zlineObj->isGrow == 1))
			$zlineObj->rowNumbers = $zlineObj->rowNumbers + 1;

		$zlineObj = $zlm->save($zlineObj);

		// проапдейтить заказ
		// для этого запустим ф-ю полного пересчёта данных
		$this->rebuildOrder($actor->id, $headObj->id);

		// по тому же условию, если у ряда стоит чекбокс "закрывать при наполнении",
		// закроем все заказы от удаления, поставив stopDel в единичку
		if ($szCounter > 0 && $usrCounter >= $szCounter)
		{
			$olm->setFull($zlineObj->id, $zlineObj->rowNumbers);
			if ($zlineObj->shouldClose == 1)
				$olm->setStopDel($zlineObj->id, $zlineObj->rowNumbers);
		}

		// в очередь добавим задание пересчитать набранность закупки
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask("calcnarate", null, null, $headObj->id);

		return true;
	}


	// покупка из ряда, в котором вместо размеров количество
	public function placeRowValueOrder($actor, $headObj, $zlineObj, $prodObj, $val, $ownerSiteId, $ownerOrgId)
	{
		$ts = time();
		$zhm = new ZakupkaHeaderManager();
		$zlm = new ZakupkaLineManager();

		// добавить голову заказа, если её ещё нет
		$this->addNewOrder($ownerSiteId, $ownerOrgId, $actor->id, $headObj->id);
		$orderHeadObj = $this->getByUserZakId($actor->id, $headObj->id);
		if (!$orderHeadObj)
			throw new Exception("Не найден заказ");

		if ($ownerSiteId != $orderHeadObj->ownerSiteId || $ownerOrgId != $orderHeadObj->ownerOrgId)
			throw new Exception("Нет прав для выполнения данного действия");

		// добавить новую строку
		$olm = new OrderListManager();

		// для начала надо сделать поиск по данной совокупности
		$olObj = $olm->getSameOrdLine($orderHeadObj->id, $actor->id, $headObj->orgId, $zlineObj->headId, $zlineObj->id);
		if (!$olObj)
		{
			$olObj = new OrderList();
			$olObj->orderId = $orderHeadObj->id;
			$olObj->userId = $actor->id;
			$olObj->orgId = $headObj->orgId;
			$olObj->headId = $zlineObj->headId;
			$olObj->zlId = $zlineObj->id;
			$olObj->dateCreate = $ts;
			$olObj->count = $val;
			$olObj->ownerSiteId = $ownerSiteId;
			$olObj->ownerOrgId = $ownerOrgId;
		}
		else
		{
			$olObj->count = $val + $olObj->count;
		}

		$olObj->prodId = $zlineObj->productId;
		$olObj->prodName = $prodObj->name;
		$olObj->prodArt = $prodObj->artNumber;
		$olObj->optPrice = $zlineObj->wholePrice;

		$olObj->status = OrderList::STATUS_NEW;
		$olObj->dateUpdate = $ts;
		$olm->save($olObj);

		// кол-во заказанного в ряду увеличим
		$zlineObj->orderedValue = $zlineObj->orderedValue + $val;
		$zlm->save($zlineObj);

		// увеличим кол-во заказов в закупке
		$headObj->orderCount = $headObj->orderCount + $val;
		$zhm->save($headObj);

		// проапдейтить заказ
		// для этого запустим ф-ю полного пересчёта данных
		$this->rebuildOrder($actor->id, $headObj->id);

		// в очередь добавим задание пересчитать набранность закупки
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask("calcnarate", null, null, $headObj->id);

		return true;
	}

	// информация о заказе по его id
	public function getInfoById($ownerSiteId, $ownerOrgId, $id)
	{
		$orderHeadObject = $this->getById($id);
		if (!$orderHeadObject)
			return false;

		if ($orderHeadObject->ownerSiteId != $ownerSiteId || $orderHeadObject->ownerOrgId != $ownerOrgId)
			return false;

		// получить строки заказа
		// по подтвержденным товарам
		$olm = new OrderListManager();
		$orderLines = $olm->getLinesByHeadIds(array($orderHeadObject->id));
		if (!$orderLines)
			return false;

		$zhm = new ZakupkaHeaderManager();
		$zakupkaObj = $zhm->getById($orderHeadObject->headId);
		if (!$zakupkaObj)
			return false;

		$um = new UserManager();
		$orgObj = $um->getById($zakupkaObj->orgId);
		if (!$orgObj)
			return false;

		// собрать всё в один массив
		$lines = array();
		foreach ($orderLines as $oneLine)
			$lines = array("prodName" => $oneLine->prodName, "prodArt" => $oneLine->prodArt, "count" => $oneLine->count, "size" => $oneLine->size, "color" => $oneLine->color, "status" => $oneLine->status);

		$result = array("orderId" => $id, "orderCode" => $orderHeadObject->code, "orderOptAmount" => $orderHeadObject->optAmount, "orderPayHold" => $orderHeadObject->payHold, "orderPayAmount" => $orderHeadObject->payAmount, "orderPayBackAmount" => $orderHeadObject->payBackAmount, "orderOpttoorgDlvrAmount" => $orderHeadObject->opttoorgDlvrAmount, "headId" => $zakupkaObj->id, "headName" => $zakupkaObj->name, "orgId" => $orgObj->id, "orgNickName" => $orgObj->nickName, "lines" => $lines);
		return $result;

	}

	// получить по owner + период
	public function getOrgAddedCommisionAmount($ownerSiteId, $ownerOrgId, $tsCreateStart = 0, $tsCreateFinish = 0)
	{
		$addedAmount = 0;
		$sql = "SELECT
					SUM(optAmount*orgRate/100) AS addedAmount
				FROM
					orderHead
				WHERE
					ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

		if ($tsCreateStart > 0)
			$sql .= " AND dateCreate >= {$tsCreateStart} ";

		if ($tsCreateFinish > 0)
			$sql .= " AND dateCreate <= {$tsCreateFinish} ";

		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$addedAmount = $res['addedAmount'];

        return $addedAmount;
	}

    public function getOrgPayedCommisionAmount($ownerSiteId, $ownerOrgId, $tsCreateStart = 0, $tsCreateFinish = 0)
    {
        $payedAmount = 0;
        $sql = "SELECT
					SUM(((payAmount-payBackAmount)/((100+orgRate)/100))*(orgRate/100)) AS payedAmount
				FROM
					orderHead
				WHERE
					ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

        if ($tsCreateStart > 0)
            $sql .= " AND datePaymentConfirm >= {$tsCreateStart} ";

        if ($tsCreateFinish > 0)
            $sql .= " AND datePaymentConfirm <= {$tsCreateFinish} ";

        $res = $this->getOneByAnySQL($sql);
        if ($res)
            $payedAmount = $res['payedAmount'];

        return $payedAmount;
    }

}
