<?php
/**
 * Менеджер
 */
class OptovikManager extends BaseEntityManager
{
	public function getByUserId($userId, $status = null)
	{
		if ($status)
			$condition = "userId = {$userId} AND status = '{$status}'";
		else
			$condition = "userId = {$userId} AND status IN ('STATUS_MODER', 'STATUS_ACTIVE', 'STATUS_DECLINED')";

		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

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

	// ищем поставщиков по типу
	public function getFree($ownerSiteId, $ownerOrgId)
	{
		$condition = "status IN ('STATUS_NEW', 'STATUS_FREE') AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

}
