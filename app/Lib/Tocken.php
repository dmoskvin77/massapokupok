<?php
/**
* Управление токенами для защиты CSRF
*/
class Tocken
{
	// создать токен
	public static function createTocken($token, $sessHash, $action, $userId, $ownerSiteId, $ownerOrgId)
	{
		$ts = time();
		$bem = new BaseEntityManager();
		$sql = "INSERT IGNORE INTO tocken (token, sessHash, action, userId, tsCreated, ownerSiteId, ownerOrgId) VALUES ('{$token}', '{$sessHash}', '{$action}', {$userId}, {$ts}, {$ownerSiteId}, {$ownerOrgId})";
		$bem->executeNonQuery($sql);
		return $token;
	}

	// проверить есть ли токен
	// токен жив лишь 10 минут
	public static function hasTocken($token, $sessHash, $action, $userId, $ownerSiteId, $ownerOrgId)
	{
		$ts = time();
		$bem = new BaseEntityManager();
		$sql = "SELECT tsCreated FROM tocken WHERE token = '{$token}' AND sessHash = '{$sessHash}' AND action = '{$action}' AND userId = {$userId} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$res = $bem->getByAnySQL($sql);
		if (!$res)
			return false;

		if (!isset($res[0]))
			return false;
		else
			$ret = $res[0];

		if (!isset($ret['tsCreated']))
			return false;

		$tsCreated = intval($ret['tsCreated']);
		if (!$tsCreated)
			return false;

		// ключ протух
		if (time() - $tsCreated > 60*10)
			return false;

		return true;

	}

}

