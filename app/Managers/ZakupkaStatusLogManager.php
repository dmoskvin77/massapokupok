<?php
/**
 * Менеджер управления логом изменения статусов закупки
 */
class ZakupkaStatusLogManager extends BaseEntityManager
{
	// получить встречи по закупке
	public function getByHeadId($headId)
	{
		$condition = "headId = {$headId}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// получить список закупок, которые открылись, начиная с указанной даты
	public function getOpenZakFromDate($ownerSiteId, $ownerOrgId, $ts = null)
	{
		if (!$ts)
			$ts = time();

		$condition = "status = '".ZakupkaHeader::STATUS_ACTIVE."' AND dateCreate > {$ts} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);

	}

}
