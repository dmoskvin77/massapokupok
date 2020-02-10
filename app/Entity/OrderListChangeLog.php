<?php
/**
 * Товары заказа
 *
 */
class OrderListChangeLog extends Entity
{
	public $entityTable = 'orderListChangeLog';
	public $primaryKey = 'id';
	public $orderId = null;
	public $userId = null;
	public $orgId = null;
	public $headId = null;
	public $zlId = null;
	public $prodNameOld = null;
	public $prodArtOld = null;
	public $optPriceOld = 0;
	public $countOld = 0;
	public $sizeOld = null;
	public $colorOld = null;
	public $commentOld = null;
	public $prodNameNew = null;
	public $prodArtNew = null;
	public $optPriceNew = 0;
	public $countNew = 0;
	public $sizeNew = null;
	public $colorNew = null;
	public $commentNew = null;
	public $dateCreate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function OrderListChangeLog()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'orderId' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'orgId' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'zlId' => self::ENTITY_FIELD_INT,
			'prodNameOld' => self::ENTITY_FIELD_STRING,
			'prodArtOld' => self::ENTITY_FIELD_STRING,
			'optPriceOld' => self::ENTITY_FIELD_STRING,
			'countOld' => self::ENTITY_FIELD_INT,
			'sizeOld' => self::ENTITY_FIELD_STRING,
			'colorOld' => self::ENTITY_FIELD_STRING,
			'commentOld' => self::ENTITY_FIELD_STRING,
			'prodNameNew' => self::ENTITY_FIELD_STRING,
			'prodArtNew' => self::ENTITY_FIELD_STRING,
			'optPriceNew' => self::ENTITY_FIELD_STRING,
			'countNew' => self::ENTITY_FIELD_INT,
			'sizeNew' => self::ENTITY_FIELD_STRING,
			'colorNew' => self::ENTITY_FIELD_STRING,
			'commentNew' => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
