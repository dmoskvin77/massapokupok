<?php
/**
 * Менеджер
 */
class UrlListManager extends BaseEntityManager
{
	// ссылки на сайты по id оптовика
	public function getByOptovik($optId)
	{
		$condition = "optId = {$optId} AND status = 'STATUS_ENABLED'";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// сайты с парсером
	public function getByOptovikEstParser($optId)
	{
		$condition = "optId = {$optId} AND status = 'STATUS_ENABLED' AND canParse = 1 AND control IS NOT NULL";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// сайты без парсера
	public function getByOptovikNoParser($optId)
	{
		$condition = "optId = {$optId} AND status = 'STATUS_ENABLED' AND canParse = 0 AND parseRequest = 0";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}

	// получить по ссылке
	public function getByUrl($ownerSiteId, $ownerOrgId, $url)
	{
		$mainPart = Utility::prepareMainUrlPart($url, true);
		if (!$mainPart)
			return false;

		$condition = "(url = '{$url}' OR main = '{$mainPart}') AND status = 'STATUS_ENABLED' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$sql = new SQLCondition($condition);
		$rez = $this->get($sql);
		if (count($rez))
			return $rez[0];
	}

	/*
	 * Функция отдает список по массиву id
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($inpIds)
	{
		if (!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("id IN ($ids)", null, "id"));

		return Utility::sort($inpIds, $res);
	}

	// почти то же самое, но список id оптовиков
	public function getByOptIds($optIds)
	{
		if (!$optIds)
			return null;

		if (count($optIds) == 0)
			return null;

		$ids = implode(",", $optIds);
		$res = $this->get(new SQLCondition("optId IN ($ids) AND status = 'STATUS_ENABLED'", null, "id"));
		return $res;
	}


	// уникальна ли ссылка
	public function isUnique($ownerSiteId, $ownerOrgId, $url, $exceptIdsArray = array())
	{
		$mainPart = Utility::prepareMainUrlPart($url);
		if (!$mainPart)
			return false;

		if (count($exceptIdsArray))
			$rez = $this->get(new SQLCondition("(url = '{$url}' OR main = '{$mainPart}') AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} AND status = 'STATUS_ENABLED' AND id NOT IN (".implode(",", $exceptIdsArray).")"));
		else
			$rez = $this->get(new SQLCondition("(url = '{$url}' OR main = '{$mainPart}') AND status = 'STATUS_ENABLED' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));

		if ($rez)
			return false;
		else
			return true;

	}

}
