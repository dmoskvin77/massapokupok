<?php
/**
 * Менеджер управления подсказками в хинтах
 */
class HintManager extends BaseEntityManager
{
	/**
	 * Возвращает страницу по псевдониму
	 *
	 * @param string $alias
	 * @return Content
	 */
	public function getByAlias($ownerSiteId, $ownerOrgId, $alias)
	{
		$sql = new SQLCondition("alias = '{$alias}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		$page = $this->getOne($sql);
		return $page;
	}

	/**
	 * Проверяет существование страницы с таким именем
	 *
	 * @param string $alias
	 * @return Content
	 */
	public function isExists($ownerSiteId, $ownerOrgId, $alias)
	{
		$sql = new SQLCondition("alias = '{$alias}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		$page = $this->getOne($sql);
		return $page;
	}

	// все хинты в одном
	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

}
