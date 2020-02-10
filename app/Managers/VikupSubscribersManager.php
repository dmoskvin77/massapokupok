<?php
/**
 * Менеджер
 */
class VikupSubscribersManager extends BaseEntityManager
{
	/*
	 * Функция отдает список по массиву id
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($inpIds)
	{
		if(!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("id IN ($ids)", null, "id"));

		return Utility::sort($inpIds, $res);
	}

	public function getByUserId($userId)
	{
		$condition = "userId = {$userId}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// insert ignore
	public function subscribeToVikup($ownerSiteId, $ownerOrgId, $vikupId, $userId)
	{
		$vikupId = intval($vikupId);
		$userId = intval($userId);
		if (!$vikupId || !$userId)
			return false;

		$ts = time();
		$sql = "INSERT IGNORE INTO vikupSubscribers (userId, vikupId, dateUpdate, ownerSiteId, ownerOrgId) VALUES ({$userId}, {$vikupId}, {$ts}, {$ownerSiteId}, {$ownerOrgId})";
		$this->executeNonQuery($sql);
		return true;
	}

	// delete
	public function unsubscribeVikup($ownerSiteId, $ownerOrgId, $vikupId, $userId)
	{
		$vikupId = intval($vikupId);
		$userId = intval($userId);
		if (!$vikupId || !$userId)
			return false;

		$sql = "DELETE FROM vikupSubscribers WHERE userId = {$userId} AND vikupId = {$vikupId} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$this->executeNonQuery($sql);
		return true;
	}

	// получить список заинтересованных
	public function getUserIds($vikupId)
	{
		$sql = "SELECT
					DISTINCT userId
				FROM
					vikupSubscribers
				WHERE
					vikupId  = {$vikupId}";

		$res = $this->getColumn($sql);
		return $res;

	}

}
