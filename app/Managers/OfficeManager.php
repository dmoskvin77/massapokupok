<?php
/**
 * Менеджер
 */
class OfficeManager extends BaseEntityManager
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

	// получить все офисы
	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

	// получить новые объявления (статус NEW)
	public function getByStatus($ownerSiteId, $ownerOrgId, $status = null)
	{
		if (!$status)
			$status = BoardAd::STATUS_ACTIVE;

		$sql = new SQLCondition("status = '{$status}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		$sql->orderBy = "name";
		$rez = $this->get($sql);
		return $rez;
	}

}
