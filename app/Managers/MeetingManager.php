<?php
/**
 * Менеджер
 */
class MeetingManager extends BaseEntityManager
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

	// получить встречи по закупке
	public function getByHeadId($headId)
	{
		$condition = "headId = {$headId}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// безопасный инкремент счетчика записавшихся на встречу
	public function incrementUserAcceptedMeeting($id)
	{
		$sql = "UPDATE meeting SET userCount = userCount + 1 WHERE id = {$id}";
		$this->executeNonQuery($sql);
		return true;
	}

}
