<?php
/**
* Управление данными, загруженными из csv
*/
class Csvfile
{
	// загрузить строки CSV файла в БД
	public static function saveLines($sessHash, $csvlines, $userId, $headId, $mode, $ownerSiteId, $ownerOrgId)
	{
		if ($mode == 'sliza')
			$delimeter = ";";
		else
			$delimeter = "\t";

		$ts = time();
		$bem = new BaseEntityManager();

		if (!count($csvlines))
			return false;

		$sql = "INSERT INTO csvfile (sessHash, userId, headId, tsCreated, field1, field2, field3, field4, field5, field6, field7, field8, field9, ownerSiteId, ownerOrgId) VALUES ";
		$sqlValues = array();
		foreach ($csvlines AS $oneLine)
		{
			if (strpos($oneLine, $delimeter) === false)
				continue;

            $lineParts = explode($delimeter, $oneLine);

            $makedArticle = null;
            if ($mode == 'sliza') {
                $makedArticle = substr(md5($lineParts[1]), 0, 10);
            }

			if (!count($lineParts))
				continue;

            if ($makedArticle && $mode == 'sliza')
                $lineParts[0] = $makedArticle;

			$sqlOneValue = "('{$sessHash}', {$userId}, {$headId}, {$ts}";
			$countFields = 0;
			foreach ($lineParts AS $oneField)
			{
				$countFields++;
				if ($countFields > 9)
					continue;

				$saveField = base64_encode(Request::clearInput($oneField));
				$sqlOneValue .= ", '{$saveField}'";

			}

			for ($i = $countFields; $i < 9; $i++)
				$sqlOneValue .= ", NULL";

			$sqlOneValue .= ", {$ownerSiteId}, {$ownerOrgId})";
			$sqlValues[] = $sqlOneValue;

		}

		if (!count($sqlValues))
			return false;

		$sql .= implode(',', $sqlValues);
		$bem->executeNonQuery($sql);
		return true;
	}


	// удалить загруженные ранее строки
	public static function removeLines($sessHash, $userId, $headId, $ownerSiteId, $ownerOrgId)
	{
		$bem = new BaseEntityManager();
		$sql = "DELETE FROM csvfile WHERE sessHash = '{$sessHash}' AND userId = {$userId} AND headId = {$headId} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$bem->executeNonQuery($sql);
		return true;
	}


	// получить количество загруженных строк
	public static function countLines($sessHash, $userId, $headId, $ownerSiteId, $ownerOrgId)
	{
		$bem = new BaseEntityManager();
		$sql = "SELECT COUNT(*) AS cnt FROM csvfile WHERE sessHash = '{$sessHash}' AND userId = {$userId} AND headId = {$headId} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$rez = $bem->getByAnySQL($sql);
		if ($rez && isset($rez[0]))
			$rez = $rez[0];

		if (isset($rez['cnt']))
			return intval($rez['cnt']);

		return false;
	}


	// получить данные одной строки
	public static function getOneString($sessHash, $userId, $headId, $ownerSiteId, $ownerOrgId)
	{
		$bem = new BaseEntityManager();
		$sql = "SELECT * FROM csvfile WHERE sessHash = '{$sessHash}' AND userId = {$userId} AND headId = {$headId} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ORDER BY RAND() LIMIT 1";
		$rez = $bem->getByAnySQL($sql);
		if ($rez && isset($rez[0]))
			return $rez[0];

		return false;
	}


	// получить все строки
	public static function getAllStrings($sessHash, $userId, $headId, $ownerSiteId, $ownerOrgId)
	{
		$bem = new BaseEntityManager();
		$sql = "SELECT * FROM csvfile WHERE sessHash = '{$sessHash}' AND userId = {$userId} AND headId = {$headId} AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$rez = $bem->getByAnySQL($sql);
		if ($rez)
			return $rez;

		return false;
	}

}

