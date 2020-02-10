<?php
/**
 * Менеджер
 */
class CategoryManager extends BaseEntityManager
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

	// получить все категории
	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

}
