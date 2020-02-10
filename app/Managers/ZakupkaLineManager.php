<?php
/**
 * Менеджер управления строками закупки
 */
class ZakupkaLineManager extends BaseEntityManager
{
	// запрос по id организатора
	public function getByProdIdAndZHId($prodid, $headid)
	{
		$prodid = intval($prodid);
		$headid = intval($headid);
		if ($headid > 0 && $prodid > 0)
		{
			$condition = "productId = {$prodid} AND headId = {$headid}";
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
			$query = "DELETE FROM zakupkaLine WHERE headId = {$headid}";
			$this->executeNonQuery($query);
		}
	}

	// поиск товара организатора по ссылке
	public function getByParseLink($lnk, $orgId, $headid)
	{
		$sql = new SQLCondition("prodLink = '{$lnk}' AND orgId = {$orgId} AND headId = {$headid}");
		$list = $this->get($sql);
		if (count($list))
			return $list[0];
	}

	// все строки по headid
	public function getByHeadId($headid, $hideHidden = false)
	{
		$headid = intval($headid);
		if ($headid > 0)
		{
			$condition = "headId = {$headid}";
			if ($hideHidden)
				$condition .= " AND status != '".ZakupkaLine::STATUS_HIDDEN."'";

			$sql = new SQLCondition($condition);
			return $this->get($sql);
		}
	}

	// получить "координаты позиции" ряда с ближайшей свободной ячейкой
	public function getAvailablePosition($lid, $val, $default = "")
	{
		if (!$val)
			$val = $default;

		$zlineObj = $this->getById($lid);
		// какие вообще есть размеры
		$dbSizes = @unserialize($zlineObj->sizes);
		// и какие размеры уже выбраны
		$gotChoosenSizes = @unserialize($zlineObj->sizesChoosen);

		// сделаем наш $gotChoosenSizes правильным, с нужным кол-вом пустых ячеек
		if (!$gotChoosenSizes)
		{
			$gotChoosenSizes = array();
			if (count($dbSizes))
			{
				for ($i = 1; $i <= $zlineObj->rowNumbers; $i++)
				{
					foreach ($dbSizes AS $dbcsKey => $dbcsVal)
						$gotChoosenSizes[$dbcsKey."_".$i] = $dbcsVal;

				}
			}
		}
		else
		{
			// добавить в $gotChoosenSizes пустые ячейки для rowNumbers
			// если весь ряд заполнен (занят)
			if (count($dbSizes)*$zlineObj->rowNumbers > count($gotChoosenSizes))
			{
				for ($i = 1; $i <= $zlineObj->rowNumbers; $i++) {
					foreach ($dbSizes AS $dbcsKey => $dbcsVal) {
						if (!isset($gotChoosenSizes[$dbcsKey . "_" . $i]))
							$gotChoosenSizes[$dbcsKey . "_" . $i] = $dbcsVal;
					}
				}
			}
		}

		// имеем правильный $gotChoosenSizes
		foreach ($gotChoosenSizes AS $oneChoosenSizeKey => $oneChoosenSizeVal)
		{
			if (!is_array($oneChoosenSizeVal) && $oneChoosenSizeVal == $val)
			{
				// свободная ячейка найдена!
				$paramsParts = explode('_', $oneChoosenSizeKey);
				if (count($paramsParts) == 2)
					return array("rp" => $paramsParts[0], "num" => $paramsParts[1]);

				break;
			}

		}

		return false;

	}

}
