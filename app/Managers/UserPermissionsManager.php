<?php
/**
 * Менеджер
 */
class UserPermissionsManager extends BaseEntityManager
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

	// получить всё и сразу
	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

	// по пользователю
	public function getByUserId($userId)
	{
		$sql = new SQLCondition("userId = {$userId}");
		return $this->get($sql);
	}

	// по пользователю и типу
	public function getByUserIdAndType($userId, $type)
	{
		$sql = new SQLCondition("userId = {$userId} AND type = '{$type}'");
		return $this->get($sql);
	}

	// удалить права
	public function removePermission($userId, $type)
	{
		$sql = "DELETE FROM userPermissions WHERE userId = {$userId} AND type = '{$type}'";
		$this->executeNonQuery($sql);
	}

}
