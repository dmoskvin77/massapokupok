<?php
/**
 * Менеджер управления голосами за закупку
 */
class ZakupkaVoteManager extends BaseEntityManager
{
	// по списку закупок
	public function getByHeadIds($inpIds)
	{
		if(!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("headId IN ($ids)", null, "id"));
		return $res;
	}

	// по списку закупок
	// плюс актор
	public function getByHeadIdsAndUserId($inpIds, $userid)
	{
		$userid = intval($userid);

		if(!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("headId IN ($ids) AND userId = {$userid}", null, "id"));
		return $res;
	}

	// запрос по id организатора
	public function getByUserIdAndZHId($userid, $headid)
	{
		$userid = intval($userid);
		$headid = intval($headid);
		if ($headid > 0 && $userid > 0)
		{
			$condition = "userId = {$userid} AND headId = {$headid}";
			$sql = new SQLCondition($condition);
			return $this->get($sql);
		}
	}

	// удаление строк закупки по id головы
	// т.е. удаление рядов
	public function removeByHeadId($headid)
	{
		$headid = intval($headid);
		if ($headid > 0)
		{
			$query = "DELETE FROM zakupkaVote WHERE headId = {$headid}";
			$this->executeNonQuery($query);
		}
	}

	// все строки по headid
	public function getByHeadId($headid)
	{
		$headid = intval($headid);
		if ($headid > 0)
		{
			$condition = "headId = {$headid}";
			$sql = new SQLCondition($condition);
			return $this->get($sql);
		}
	}

}
