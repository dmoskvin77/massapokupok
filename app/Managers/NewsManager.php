<?php
/**
* Менеджер для управления новостями
*/
class NewsManager extends BaseEntityManager 
{
	/**
	* Функция возвращает список id новостей
	* 
	* @return array int
	*/
	public function getNewsIds($ownerSiteId, $ownerOrgId, $limit = null)
	{
		$sql = "SELECT id FROM news WHERE ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ORDER BY showDate DESC";

		if ($limit != null)
			$sql .= " LIMIT ".$limit;

		return $this->getColumn($sql);
	}
	
	/**
	* Функция возвращает список новостей по заданным id
	*
	* @param array int $ids список id новостей
	* @return array News
	*/
	public function getByIds($newsIds)
	{
		if(!$newsIds)
			return null;
		
		$ids = implode(",", $newsIds);
		$res = $this->get(new SQLCondition("id in ($ids)", null, "showDate DESC"));
		
		return Utility::sort($newsIds, $res);
	}


	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

}
