<?php
/**
 * Менеджер
 */
class PayManager extends BaseEntityManager
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


	// получить список оплат пользователя
	public function getIdsByUserId($userId)
	{
		$condition = "userId = {$userId}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "dateCreate DESC";
		return $this->get($sql);

	}

	// оплаты по закупке
	public function getByHeadId($headId)
	{
		$condition = "headId = {$headId}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "userId";
		return $this->get($sql);

	}


	// получить список оплат для организатора
	public function getIdsByOrgId($orgId)
	{
		$condition = "orgId = {$orgId}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "dateCreate DESC";
		return $this->get($sql);

	}

	// кол-во оплат новых от участников организатору, которые ещё не обработаны
	public function countNewPaysByOrgId($orgId)
	{
		$newPayCount = 0;

		$sql = "SELECT COUNT(*) AS cnt FROM pay WHERE orgId = {$orgId} AND status = '".Pay::STATUS_NEW."'";
		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$newPayCount = $newPayCount + intval($res['cnt']);

		return $newPayCount;
	}


	// кол-во новых оплат по закупке
	public function countNewPaysByHeadId($headId)
	{
		$newPayCount = 0;

		$sql = "SELECT COUNT(*) AS cnt FROM pay WHERE headId = {$headId} AND status = '".Pay::STATUS_NEW."'";
		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$newPayCount = $newPayCount + intval($res['cnt']);

		return $newPayCount;
	}

}
