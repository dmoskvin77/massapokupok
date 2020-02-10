<?php
/**
 * Менеджер
 */
class ZhCommentManager extends BaseEntityManager
{

	// все немодерированные комментарии
	public function getAllNew($ownerSiteId, $ownerOrgId)
	{
		$sql = "SELECT
					comm.id, comm.dateCreate, comm.body, comm.userId,
					comm.headId, comm.level, comm.sourceId,
					comm.isAnon, us.nickName AS nick
				FROM
					zhComment AS comm
				INNER JOIN
					user AS us
				ON
					comm.userId = us.id
				WHERE
					comm.status = '".ZhComment::STATUS_NEW."' AND isPrivate IS NULL
					AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}
				ORDER BY
					comm.id";

		$res = $this->getByAnySQL($sql);

		return $res;

	}


	// отмодерированные комментарии к заголовку закупки по id
	// только верхний уровень
	public function getModByHeadId($headId)
	{
		$headId = intval($headId);

		if ($headId > 0)
		{
			$sql = "SELECT
						comm.id, comm.dateCreate, comm.body, comm.userId,
						comm.level, comm.sourceId, comm.nickName AS showName,
						comm.isAnon, us.nickName AS nick, comm.isPrivate, comm.toId
					FROM
						zhComment AS comm
					INNER JOIN
						user AS us
					ON
						comm.userId = us.id
					WHERE
						comm.status = '".ZhComment::STATUS_MODERATED."' AND comm.headId = {$headId}
						AND comm.sourceId IS NULL AND comm.level IS NULL
					ORDER BY
						comm.id";

			$res = $this->getByAnySQL($sql);

			return $res;
		}

	}


	// отмодерированные комментарии к каментариям
	public function getSubModByHeadId($rootId)
	{
		$rootId = intval($rootId);

		if ($rootId > 0)
		{
			$sql = "SELECT
						comm.id, comm.dateCreate, comm.body, comm.userId,
						comm.level, comm.sourceId, comm.nickName AS showName,
						comm.isAnon, us.nickName AS nick, comm.isPrivate, comm.toId
					FROM
						zhComment AS comm
					INNER JOIN
						user AS us
					ON
						comm.userId = us.id
					WHERE
						comm.rootId = {$rootId}
						AND comm.status = '".ZhComment::STATUS_MODERATED."'
					ORDER BY
						comm.weight";

			$res = $this->getByAnySQL($sql);

			return $res;
		}

	}

	// получим список пользователей, которые что-то писали или кому то писали
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
						headId  = {$headid}";

			$res = $this->getColumn($sql);

			return $res;

		}

	}

	public function getToIds($headid)
	{
		$headid = intval($headid);

		if ($headid > 0)
		{
			$sql = "SELECT
						DISTINCT toId
					FROM
						orderHead
					WHERE
						headId  = {$headid}";

			$res = $this->getColumn($sql);

			return $res;

		}

	}

	// новые сообщения для орга
	public function getNewByOrgId($orgId)
	{
		$orgId = intval($orgId);

		if ($orgId > 0)
		{
			$sql = "SELECT
						comm.id, comm.dateCreate, comm.body, comm.userId,
						comm.level, comm.sourceId, comm.nickName AS showName,
						comm.isAnon, comm.headId, us.nickName AS nick
					FROM
						zhComment AS comm
					INNER JOIN
						user AS us
					ON
						comm.userId = us.id
					WHERE
						comm.toId = {$orgId} AND comm.toType = '".ZhComment::TYPE_ORG."' AND wasRead IS NULL
						AND comm.status = '".ZhComment::STATUS_MODERATED."' AND comm.userId != {$orgId}
					ORDER BY
						comm.weight";

			$res = $this->getByAnySQL($sql);

			return $res;
		}

	}


	// новые сообщения для покупателя
	public function getNewByUserId($userId)
	{
		$userId = intval($userId);

		if ($userId > 0)
		{
			$sql = "SELECT
						comm.id, comm.dateCreate, comm.body, comm.userId,
						comm.level, comm.sourceId, comm.nickName AS showName,
						comm.isAnon, comm.headId, us.nickName AS nick
					FROM
						zhComment AS comm
					INNER JOIN
						user AS us
					ON
						comm.userId = us.id
					WHERE
						comm.toId = {$userId} AND comm.toType = '".ZhComment::TYPE_USER."' AND wasRead IS NULL
						AND comm.status = '".ZhComment::STATUS_MODERATED."'
					ORDER BY
						comm.weight";

			$res = $this->getByAnySQL($sql);

			return $res;
		}

	}

}
