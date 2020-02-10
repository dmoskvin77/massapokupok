<?php
/**
 * Менеджер управления логом изменения заказов организаторами
 */
class OrderlistchangelogManager extends BaseEntityManager
{
	// строки заказов по userId + headid
	public function getByUserIdHeadId($userid, $headid)
	{
		$condition = "userId = {$userid} AND headId = {$headid}";
		$sql = new SQLCondition($condition);
		$sql->orderBy = "userId, zlId, dateCreate";
		return $this->get($sql);
	}
}
