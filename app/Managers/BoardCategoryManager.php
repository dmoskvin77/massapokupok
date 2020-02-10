<?php
/**
 * Менеджер
 */
class BoardCategoryManager extends BaseEntityManager
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

	// получить категории объявлений (с указанным типом)
	public function getAllActive($ownerSiteId, $ownerOrgId, $boardTypeId = null)
	{
		$boardTypeId = intval($boardTypeId);
		if ($boardTypeId)
			$query = "status = 1 AND boardTypeId = {$boardTypeId} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		else
			$query = "status = 1 AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";

		$sql = new SQLCondition($query);
		$sql->orderBy = "boardTypeId ASC, position ASC";
		return $this->get($sql);
	}

	// кол-во активных объявлений по категории
	public function countByCat($catId)
	{
		$catId = intval($catId);
		if (!$catId)
			return false;

		$adCount = 0;

		$sql = "SELECT COUNT(*) AS cnt FROM boardAd WHERE catId = {$catId} AND status = '".BoardAd::STATUS_ACTIVE."'";
		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$adCount = $adCount + intval($res['cnt']);

		return $adCount;
	}

}
