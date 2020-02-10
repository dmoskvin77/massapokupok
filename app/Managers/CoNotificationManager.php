<?php
/**
 * Менеджер управления нотификациями по каментам
 */
class CoNotificationManager extends BaseEntityManager
{
	// получить уведомления об ответах по закупке и участнику
	public function getByHeadUserId($headId, $userId)
	{
		$condition = "headId = {$headId} AND userId = {$userId}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// получить уведомления об ответах по участнику
	public function getByUserId($userId, $ts = null)
	{
		if (!$ts)
			$ts = time();

		$condition = "userId = {$userId} AND dateUpdate > {$ts}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// добавление нотификации
	public function saveAddNotification($ownerSiteId, $ownerOrgId, $headId, $userId, $zakName)
	{
		$ts = time();
		$sql = "INSERT INTO coNotification (headId, userId, zakName, dateUpdate, ownerSiteId, ownerOrgId) VALUES ({$headId}, {$userId}, '{$zakName}', {$ts}, {$ownerSiteId}, {$ownerOrgId}) ON DUPLICATE KEY UPDATE dateUpdate = {$ts}";
		$this->executeNonQuery($sql);
		return true;
	}

}
