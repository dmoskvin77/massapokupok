<?php
/**
 * Менеджер 
 */
class ViewAreaManager extends BaseEntityManager
{
	public function getByCityView($cityId, $countryId = 0)
	{
		$countryId = intval($countryId);
		if ($countryId == 0 || $countryId == null)
			$countryId = 1;

		$sql = "SELECT id FROM viewArea WHERE vkCityId = {$cityId} AND vkCountryId = {$countryId} LIMIT 1";

		$res = $this->getColumn($sql);
		if (isset($res[0]))
		{
			if ($res[0] > 0)
			{
				$res = $this->getById($res[0]);
				return $res;
			}
		}
	}

	// список клубов (окружение по городам выбирается с главной страницы)
	public function getClubViewList($ids)
	{
		$sql = "SELECT
					va.id AS id, cl.name AS clname
				FROM
					viewArea AS va
				LEFT JOIN
					club AS cl
				ON
					va.vkClubId = cl.vkClubId
				WHERE
					va.vkClubId IS NOT NULL
					AND va.id IN(".$ids.")
				ORDER BY
					va.vkClubId";

		$res = $this->getByAnySQL($sql);
		return $res;
	}

}
?>