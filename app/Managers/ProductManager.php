<?php
/**
 * Менеджер управления товарами
 */
class ProductManager extends BaseEntityManager
{
	// поиск похожего артикула
	public function getIdsLikeArtikul($ownerSiteId, $ownerOrgId, $art)
	{
		if ($art)
		{
			$sql = "SELECT id FROM product WHERE artNumber LIKE '".$art."%' ORDER BY id DESC";
			$res = $this->getColumn($sql);

			if ($res)
				return $res;
			else
				return null;

		}

	}


	// поиск по указанному артикулу
	public function getByArt($ownerSiteId, $ownerOrgId, $art, $orgId)
	{
		$sql = new SQLCondition("artNumber = '{$art}' AND orgId = {$orgId}");
		$list = $this->get($sql);
		if (count($list))
			return $list[0];
	}


	// поиск товара организатора по ссылке
	public function getByParseLink($ownerSiteId, $ownerOrgId, $lnk, $orgId)
	{
		$sql = new SQLCondition("prodLink = '{$lnk}' AND orgId = {$orgId}");
		$list = $this->get($sql);
		if (count($list))
			return $list[0];
	}


	// получить список по ids
	public function getByIds($ids, $status = null)
	{
		if (count($ids) == 0)
			return null;

		$statusSQL = "";
		if ($status != null)
			$statusSQL = " AND status = '{$status}' ";

		$ids = implode(", ", $ids);
		$sql = new SQLCondition("id IN ({$ids})" . $statusSQL);
		$list = $this->get($sql);
		return $list;
	}


	// добавление нового
	public function addNewProd($ownerSiteId, $ownerOrgId, $addData, $prodid = null)
	{
		// проверим порядочность
		$actor = Context::getActor();

		// взлом какой-то ...
		if ($actor->id != $addData['orgId'])
			return;

		$prodid = intval($prodid);
		if ($prodid > 0)
		{
			$curProd = $this->getById($prodid);
			if ($curProd)
				$newProd = $curProd;
			else
				return;
		}
		else
		{
			$newProd = new Product();
			$newProd->ownerSiteId = $ownerSiteId;
			$newProd->ownerOrgId = $ownerOrgId;
		}

		if (isset($addData['orgId']))
		{
			if ($addData['orgId'] != null)
				$newProd->orgId = $addData['orgId'];
		}

		$newProd->name = $addData['name'];
		$newProd->artNumber = $addData['artNumber'];
		$newProd->description = $addData['description'];

		$srvurl = 'http://'.$_SERVER['HTTP_HOST'];

		if ($addData['picFile1'] != null) { $newProd->picFile1 = $addData['picFile1']; $newProd->picSrv1 = $srvurl; }
		if ($addData['picFile2'] != null) { $newProd->picFile2 = $addData['picFile2']; $newProd->picSrv2 = $srvurl; }
		if ($addData['picFile3'] != null) { $newProd->picFile3 = $addData['picFile3']; $newProd->picSrv3 = $srvurl; }

		$newProd->entityStatus = Product::ENTITY_STATUS_PENDING;	// на модерацию
		$newProd->status = Product::STATUS_NEW;
		$newProd->dateCreate = time();

		$newProd = $this->save($newProd);

		return $newProd;

	}

}
