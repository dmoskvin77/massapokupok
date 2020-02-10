<?php
/**
 * Менеджер
 */
class MainCommisionManager extends BaseEntityManager
{
	/*
	 * Функция отдает список по массиву id
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($inpIds)
	{
		if (!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("id IN ($ids)", null, "id"));

		return Utility::sort($inpIds, $res);
	}

	// поиск по типу
	public function getByType($type, $ownerSiteId = null, $ownerOrgId = null, $tsCreateStart = 0, $tsCreateFinish = 0)
	{
		$condition = "type = '{$type}' ";

		if ($tsCreateStart > 0)
			$condition .= " AND dateCreate >= {$tsCreateStart} ";

		if ($tsCreateFinish > 0)
			$condition .= " AND dateCreate <= {$tsCreateFinish} ";

        if ($ownerSiteId !== null)
            $condition .= " AND ownerSiteId = {$ownerSiteId} ";

        if ($ownerOrgId !== null)
            $condition .= " AND ownerOrgId = {$ownerOrgId} ";

		$sql = new SQLCondition($condition);
		$sql->orderBy = "orgId";
		return $this->get($sql);
	}

	// получить по статусу
	public function getByStatus($status = null, $type = null, $ownerSiteId, $ownerOrgId)
	{
		$sql = "SELECT DISTINCT id FROM mainCommision WHERE ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";
		if ($status) {
			$sql .= " AND status = '{$status}' ";
		}
        if ($type) {
            $sql .= " AND type = '{$type}' ";
        }
		$sql .= "ORDER BY dateCreate DESC";
		$res = $this->getColumn($sql);
		return $res;
	}


    public function getMainOwnerCommissions($status = null, $type = null)
    {
        $sql = "SELECT DISTINCT id FROM mainCommision  ";
        if ($status) {
            $sql .= " WHERE status = '{$status}' ";
        }
        if ($type && $status) {
            $sql .= " AND type = '{$type}' ";
        }
        if ($type && !$status) {
            $sql .= " WHERE type = '{$type}' ";
        }
        $sql .= "ORDER BY dateCreate DESC";
        $res = $this->getColumn($sql);
        return $res;
    }

	public function getClickedOrdersAddedAmountTotal($ownerSiteId, $ownerOrgId, $tsCreateStart = 0, $tsCreateFinish = 0)
    {
        $created = 0;
        $sql = "SELECT
					SUM(needAmount) AS total
				FROM
					mainCommision
				WHERE
					ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

		if ($tsCreateStart > 0)
			$sql .= " AND dateCreate >= {$tsCreateStart} ";

		if ($tsCreateFinish > 0)
			$sql .= " AND dateCreate <= {$tsCreateFinish} ";

		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$created = $res['total'];

        return $created;
	}

    public function getClickedOrdersPayedAmountTotal($ownerSiteId, $ownerOrgId, $tsCreateStart = 0, $tsCreateFinish = 0)
    {
        $payed = 0;
        $sql = "SELECT
					SUM(payAmount) AS total
				FROM
					mainCommision
				WHERE
					ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

        if ($tsCreateStart > 0)
            $sql .= " AND dateConfirm >= {$tsCreateStart} ";

        if ($tsCreateFinish > 0)
            $sql .= " AND dateConfirm <= {$tsCreateFinish} ";

        $res = $this->getOneByAnySQL($sql);
        if ($res)
            $payed = $res['total'];

        return $payed;
    }

}
