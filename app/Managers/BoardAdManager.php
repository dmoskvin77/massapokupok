<?php
/**
 * Менеджер
 */
class BoardAdManager extends BaseEntityManager
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


	// получить новые объявления (статус NEW)
	public function getByStatus($ownerSiteId, $ownerOrgId, $status = null)
	{
		if (!$status)
			$status = BoardAd::STATUS_ACTIVE;

		$sql = new SQLCondition("status = '{$status}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		$sql->orderBy = "dateUpdate DESC";
		$rez = $this->get($sql);
		return $rez;
	}


	// все объявления пользователя
	public function getByUserId($userId)
	{
		$userId = intval($userId);
		if (!$userId)
			return false;

		$sql = new SQLCondition("userId = {$userId} AND status != '".BoardAd::STATUS_DELETE."'");
		$sql->orderBy = "dateUpdate DESC";
		$rez = $this->get($sql);
		return $rez;
	}


	// пересчёт кол-ва объявлений в заданнов типе и категории
	public function rebuildCounters($typeId, $catId = 0)
	{
		$typeId = intval($typeId);
		$catId = intval($catId);

		if ($typeId)
		{
			$btm = new BoardTypeManager();
			$countByType = intval($btm->countByType($typeId));

			// Logger::info("countByType: {$countByType}");
			// Logger::info("UPDATE boardType SET cnt = {$countByType} WHERE id = {$typeId}");

			// апдейтим
			$sql = "UPDATE boardType SET cnt = {$countByType} WHERE id = {$typeId}";
			$this->executeNonQuery($sql);
		}

		if ($catId)
		{
			$bcm = new BoardCategoryManager();
			$countByCat = intval($bcm->countByCat($catId));
			// апдейтим 2
			$sql = "UPDATE boardCategory SET cnt = {$countByCat} WHERE id = {$catId}";
			$this->executeNonQuery($sql);
		}

		return true;
	}


	// получить активные объявления указанного типа и категории
	public function getTypeCatId($gotTypeId, $gotCatId = null)
	{
		$gotTypeId = intval($gotTypeId);
		if (!$gotTypeId)
			return false;

		$gotCatId = intval($gotCatId);

		if ($gotCatId)
			$sql = new SQLCondition("typeId = {$gotTypeId} AND catId = {$gotCatId} AND status = '".BoardAd::STATUS_ACTIVE."'");
		else
			$sql = new SQLCondition("typeId = {$gotTypeId} AND status = '".BoardAd::STATUS_ACTIVE."'");

		$sql->orderBy = "dateUpdate DESC";
		$rez = $this->get($sql);
		return $rez;
	}
}
