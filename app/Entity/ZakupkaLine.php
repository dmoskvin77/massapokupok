<?php
/**
 * Сущность коллекция (закупка) - одна строка с товаром (!)
 *
 */
class ZakupkaLine extends Entity
{
	public $entityTable = 'zakupkaLine';
	public $primaryKey = 'id';
	public $headId = null;
	public $productId = null;
	public $prodLink = null;
	public $orgId = null;
	public $wholePrice = 0;
	public $oldWholePrice = null;
	public $finalPrice = null;
	public $dateCreate = null;
	public $dateUpdate = null;
	public $minValue = null;
	public $minName = null;
	public $isGrow = null;
	public $shouldClose = null;
	public $rowNumbers = null;
	public $orderedValue = null;
	public $sizes = null;
	public $sizesChoosen = null;
	public $sizesComplete = null;
	public $status = self::STATUS_ACTIVE;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_ACTIVE = "STATUS_ACTIVE";		// активна (или null)
	const STATUS_HIDDEN = "STATUS_HIDDEN";      // орг спрятал ряд
	const STATUS_STOP = "STATUS_STOP";			// нельзя уменьшать заказанное кол-во товара
	const STATUS_CLOSED = "STATUS_CLOSED";		// закрыта

	function ZakupkaLine()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'productId' => self::ENTITY_FIELD_INT,
			'prodLink' => self::ENTITY_FIELD_STRING,
			'orgId' => self::ENTITY_FIELD_INT,
			'wholePrice' => self::ENTITY_FIELD_STRING,
			'oldWholePrice' => self::ENTITY_FIELD_STRING,
			'finalPrice' => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateUpdate' => self::ENTITY_FIELD_INT,
			'minValue' => self::ENTITY_FIELD_INT,
			'minName' => self::ENTITY_FIELD_STRING,
			'isGrow' => self::ENTITY_FIELD_INT,
			'shouldClose' => self::ENTITY_FIELD_INT,
			'rowNumbers' => self::ENTITY_FIELD_INT,
			'orderedValue' => self::ENTITY_FIELD_INT,
			'sizes' => self::ENTITY_FIELD_STRING,
			'sizesChoosen' => self::ENTITY_FIELD_STRING,
			'sizesComplete' => self::ENTITY_FIELD_INT,
			'status' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
