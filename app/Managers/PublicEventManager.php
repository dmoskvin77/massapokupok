<?php
/**
 * Менеджер уведомлений о событиях
 */

class PublicEventManager extends BaseEntityManager
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


	// кол-во непрочитанных сообщений
	public function countNewMessages($userId)
	{
		$newMessCount = 0;

		$sql21 = "SELECT COUNT(*) AS cnt FROM publicEvent WHERE toUserId = {$userId} AND dateRead IS NULL";
		$res21 = $this->getOneByAnySQL($sql21);
		if ($res21)
			$newMessCount = $newMessCount + intval($res21['cnt']);

		return $newMessCount;
	}


	// общее кол-во сообщений
	public function countAllMessages($userId)
	{
		$allMessCount = 0;

		$sql21 = "SELECT COUNT(*) AS cnt FROM publicEvent WHERE toUserId = {$userId}";
		$res21 = $this->getOneByAnySQL($sql21);
		if ($res21)
			$allMessCount = $allMessCount + intval($res21['cnt']);

		return $allMessCount;
	}


	// получить список уведомлений для пользователя
	public function getByUserId($userId, $pastInterval = 0)
	{
		$timeCondition = time() - $pastInterval;
		$condition = "toUserId = {$userId} AND (dateRead IS NULL OR dateCreate >= {$timeCondition})";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "dateCreate DESC";
		return $this->get($sql);

	}


	// установка даты прочтения на уведомления
	public function setAsRead($inpIds)
	{
		if (!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$ts = time();
		$sql = "UPDATE publicEvent SET dateRead = {$ts} WHERE id IN ({$ids})";
		$this->executeNonQuery($sql);
		return true;

	}


	// по закупке
	public function getByHeadId($headId)
	{
		$condition = "headId = {$headId}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "userId";
		return $this->get($sql);

	}


	// строит и выполняет групповую вставку событий
	// участникам по закупке
	public function buildInsertOnZakupka($ownerSiteId, $ownerOrgId, $userIds, $fromUserId, $fromNickName, $headId, $headName, $message, $meetId = 0)
	{
		$ts = time();
		// разбиваем массив id участников на пачки по 300
		$slcIds = array_chunk($userIds, 300, false);
		foreach ($slcIds AS $oneChunk)
		{
			$sql = "INSERT INTO publicEvent (fromUserId, fromNickName, toUserId, headId, headName, meetId, message, dateCreate, ownerSiteId, ownerOrgId) VALUES ";
			if (count($oneChunk))
			{
				$i = 0;
				foreach ($oneChunk AS $oneUid)
				{
					$i++;
					$sql .= "({$fromUserId}, '{$fromNickName}', {$oneUid}, {$headId}, '{$headName}', {$meetId}, '{$message}', {$ts}, {$ownerSiteId}, {$ownerOrgId})";
					if (count($oneChunk) == $i)
						$sql .= ";";
					else
						$sql .= ",";
				}
			}

			$this->executeNonQuery($sql);

		}

	}


	// удаление сообщений по встрече
	public function removeAllUnacceptedByMeetId($meetId)
	{
		$sql = "DELETE FROM publicEvent WHERE dateMeetAccept IS NULL AND meetId = {$meetId}";
		$this->executeNonQuery($sql);
	}

}
