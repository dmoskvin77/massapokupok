<?php
/**
 * Менеджер
 */
class BoardTypeManager extends BaseEntityManager
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

	// получить все активные типы объявлений (разделы)
	public function getAllActive($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("status = 1 AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		$sql->orderBy = "position ASC";
		return $this->get($sql);
	}

	// кол-во активных объявлений по типу
	public function countByType($typeId)
	{
		$typeId = intval($typeId);
		if (!$typeId)
			return false;

		$adCount = 0;

		$sql = "SELECT COUNT(*) AS cnt FROM boardAd WHERE typeId = {$typeId} AND status = '".BoardAd::STATUS_ACTIVE."'";
		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$adCount = $adCount + intval($res['cnt']);

		return $adCount;
	}

}
