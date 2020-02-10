<?php
/**
 * Менеджер
 */
class SiteCommisionManager extends BaseEntityManager
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


	// оплаты по закупке
	public function getByHeadId($headId, $type = SiteCommision::TYPE_ZAK)
	{
		$condition = "headId = {$headId} AND type = '{$type}'";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "orgId";
		return $this->get($sql);
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


	// получить список оплат для организатора
	public function getByOrgId($orgId = null, $status = null, $notPayed = null)
	{
		if ($orgId)
			$condition = "orgId = {$orgId}";
		else
			$condition = "orgId > 0";

		if ($status)
			$condition .= " AND status = '{$status}'";

		if ($notPayed === true)
			$condition .= " AND payAmount = 0";

		if ($notPayed === false)
			$condition .= " AND payAmount > 0";

		$sql = new SQLCondition($condition);
		$sql->orderBy = "dateCreate DESC";
		return $this->get($sql);
	}

	// новые выставленные оргу счета
	public function countNewPaysByOrgId($orgId)
	{
		$newPayCount = 0;
		$sql = "SELECT COUNT(*) AS cnt FROM siteCommision WHERE orgId = {$orgId} AND status = '" . Pay::STATUS_NEW . "'";
		$res = $this->getOneByAnySQL($sql);
		if ($res) {
			$newPayCount = $newPayCount + intval($res['cnt']);
		}

		return $newPayCount;
	}

	// получить по статусу
	public function getByStatus($status = null, $type = null, $ownerSiteId, $ownerOrgId)
	{
		$sql = "SELECT DISTINCT id FROM siteCommision WHERE ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";
		if ($status) {
			$sql .= " AND status = '{$status}' ";
		}
        if ($type) {
            $sql .= " AND type = '{$type}' ";
        }
		// только комиссия по закупкам
		$sql .= " AND type = 'TYPE_ZAK' ";
		$sql .= "ORDER BY dateCreate DESC";
		$res = $this->getColumn($sql);
		return $res;
	}

	// платежи по главному владельцу всего saas
	public function getMainOwnerCommissions($status = null, $type = null)
	{
        // все допы, кроме комиссии по закупкам
        // их контролирует главный аккаунт
        $sql = "SELECT DISTINCT id FROM siteCommision WHERE type != 'TYPE_ZAK' ";
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

}
