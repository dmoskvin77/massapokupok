<?php
/**
 * Менеджер
 */
class zParseWorkManager extends BaseEntityManager
{
	public function cleanAll($ownerSiteId, $ownerOrgId, $controlName)
	{
		$sql = "DELETE FROM zParseWork WHERE control = '{$controlName}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$this->executeNonQuery($sql);
		return true;

	}

	// ссылки на одну и ту же страницу каталога страниц с товарами
	public function getByCatLnk($ownerSiteId, $ownerOrgId, $lnk, $controlName)
	{
		$rez = $this->get(new SQLCondition("catLink = '{$lnk}' AND control = '{$controlName}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
		return $rez;
	}

	// ссылки на страницы каталога страниц с товарами
	public function getByCatLnksByGuidCtrl($ownerSiteId, $ownerOrgId, $controlName)
	{
		$rez = $this->get(new SQLCondition("catLink IS NOT NULL AND control = '{$controlName}' AND status = '".zParseWork::STATUS_NEW."' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
		return $rez;
	}

	// ссылки на страницы товара
	public function getByProdLnk($ownerSiteId, $ownerOrgId, $lnk, $controlName)
	{
		$rez = $this->get(new SQLCondition("prodLink = '{$lnk}' AND control = '{$controlName}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
		return $rez;
	}

	// ссылки на страницы с товарами
	public function getByProdLnksByGuidCtrl($ownerSiteId, $ownerOrgId, $controlName, $status = zParseWork::STATUS_NEW)
	{
		$rez = $this->get(new SQLCondition("prodLink IS NOT NULL AND control = '{$controlName}' AND status = '{$status}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
		return $rez;
	}

	// получить список по ids
	public function getByIds($ids)
	{
		if (count($ids) == 0)
			return null;

		$ids = implode(", ", $ids);
		$sql = new SQLCondition("id IN ({$ids}) AND params IS NOT NULL AND status = '".zParseWork::STATUS_PROCESSED."'");
		$list = $this->get($sql);
		return $list;
	}

}
