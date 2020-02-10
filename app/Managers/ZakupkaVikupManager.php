<?php
/**
 * Менеджер
 */
class ZakupkaVikupManager extends BaseEntityManager
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

	// по оргу
	public function getByOrgId($orgId)
	{
		$orgId = intval($orgId);
		if ($orgId > 0)
		{
			$condition = "orgId = {$orgId}";
			$sql = new SQLCondition($condition);
			return $this->get($sql);

		}
	}

	// юезопасный плюс один
	public function saveIncrement($id)
	{
		$sql = "UPDATE zakupkaVikup SET countZheads = countZheads + 1 WHERE id = {$id}";
		$this->executeNonQuery($sql);
		return true;
	}

}
?>