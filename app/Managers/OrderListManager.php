<?php
/**
 * Менеджер управления строками заказов
 */
class OrderListManager extends BaseEntityManager
{
	// заказа нет в наличии
	public function rejectOrder($zlorderid, $userId, $headId)
	{
		$sql = "UPDATE `orderList` SET status = '".OrderList::STATUS_REJECT."' WHERE id = ".$zlorderid;
		$this->executeNonQuery($sql);

		$ohm = new OrderHeadManager();
		$ohm->rebuildOrder($userId, $headId);

		return true;
	}

	// ряд заполнился
	public function setFull($zlId, $rowNumbers)
	{
		$rowNumbers--;
		if ($rowNumbers) {
			$sql = "UPDATE orderList SET isFull = 1 WHERE zlId = {$zlId} AND num = {$rowNumbers}";
			$this->executeNonQuery($sql);
		}
	}

	// установим в заказы участников флаг о том, что заказ удалять нельзя
	public function setStopDel($zlId, $rowNumbers)
	{
		$sql = "UPDATE orderList SET stopDel = 1 WHERE zlId = {$zlId} AND num < {$rowNumbers}";
		$this->executeNonQuery($sql);
	}

	// заказ есть в наличии
	public function confirmOrder($zlorderid, $userId, $headId)
	{
		$ts = time();
		$sql = "UPDATE `orderList` SET status = '".OrderList::STATUS_CONFIRM."', dateConfirm = {$ts} WHERE id = ".$zlorderid;
		$this->executeNonQuery($sql);

		$ohm = new OrderHeadManager();
		$ohm->rebuildOrder($userId, $headId);

		return true;
	}

	// записать сумму за доставку в строку заказа, скорректировать сумму доставки по заказу
	// скорректировать сумму за доставку по закупке в целом
	public function setDeliveryAmount($headId, $orderId, $olineId, $amount, $oldAmount)
	{
		$sql1 = "UPDATE orderList SET opttoorgDlvrAmount = {$amount} WHERE id = {$olineId}";
		$this->executeNonQuery($sql1);

		// как изменилась сумма
		$deltaAmount = $amount - $oldAmount;
		$sql2 = "UPDATE orderHead SET opttoorgDlvrAmount = opttoorgDlvrAmount + {$deltaAmount} WHERE id = {$orderId}";
		$this->executeNonQuery($sql2);

		// нельзя уходить в минус
		$sql3 = "UPDATE orderHead SET opttoorgDlvrAmount = 0 WHERE id = {$orderId} AND opttoorgDlvrAmount < 0";
		$this->executeNonQuery($sql3);

		$sql4 = "UPDATE zakupkaHeader SET opttoorgDlvrAmount = opttoorgDlvrAmount + {$deltaAmount} WHERE id = {$headId}";
		$this->executeNonQuery($sql4);

		// нельзя уходить в минус
		$sql5 = "UPDATE zakupkaHeader SET opttoorgDlvrAmount = 0 WHERE id = {$headId} AND opttoorgDlvrAmount < 0";
		$this->executeNonQuery($sql5);

		return true;
	}


	// нет ли такого же заказа по кол-ву
	public function getSameOrdLine($orderId, $userId, $orgId, $headId, $zlId)
	{
		$sql = new SQLCondition("orderId = {$orderId} AND userId = {$userId} AND orgId = {$orgId} AND headId = {$headId} AND zlId = {$zlId}");
		$obj = $this->getOne($sql);
		return $obj;
	}


	// получить строки
	public function getLinesByHeadIds($inpIds, $status = null)
	{
		if (!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$additionalCondition = "";
		if ($status)
			$additionalCondition = " AND status = '{$status}'";

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("orderId IN ($ids){$additionalCondition}", null, "id"));
		return $res;

	}


	// все строки заказов по headid
	public function getByHeadId($headid, $orderBy = "userId, zlId, dateCreate")
	{
		$headid = intval($headid);
		if ($headid > 0)
		{
			$condition = "headId = {$headid}";
			$sql = new SQLCondition($condition);
			$sql->orderBy = $orderBy;
			return $this->get($sql);
		}
	}


	// строки заказов по userId + headid
	public function getByUserIdHeadId($userid, $headid)
	{
		$condition = "userId = {$userid} AND headId = {$headid}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "userId, zlId, dateCreate";
		return $this->get($sql);
	}


	// все строки заказов по zlId
	public function getByZlId($zlId)
	{
		$zlId = intval($zlId);
		if ($zlId > 0)
		{
			$condition = "zlId = {$zlId}";
			$sql = new SQLCondition($condition);
			return $this->get($sql);
		}
	}


	// меняем цены у заказов с указанным zlId одним запросом
	public function orgChangeZlPrice($zlId, $newPrice)
	{
		$sql = "UPDATE orderList SET optPrice = '{$newPrice}' WHERE zlId = {$zlId}";
		$this->executeNonQuery($sql);
		return true;
	}

	// удаление строк заказа закупки по id головы
	public function removeByHeadId($headid)
	{
		$headid = intval($headid);
		if ($headid > 0)
		{
			$query = "DELETE FROM orderList WHERE headId = {$headid}";
			$this->executeNonQuery($query);
		}
	}

    public function getClickedOrdersTotal($ownerSiteId, $ownerOrgId, $tsCreateStart = 0, $tsCreateFinish = 0)
    {
        $total = 0;
        $sql = "SELECT
					COUNT(*) AS total
				FROM
					orderList
				WHERE
					ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

        if ($tsCreateStart > 0)
            $sql .= " AND dateConfirm >= {$tsCreateStart} ";

        if ($tsCreateFinish > 0)
            $sql .= " AND dateConfirm <= {$tsCreateFinish} ";

        $res = $this->getOneByAnySQL($sql);
        if ($res)
            $total = $res['total'];

        return $total;
    }

}
