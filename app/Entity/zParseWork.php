<?php
/**
 * Сущность категории товара
 *
 */
class zParseWork extends Entity
{
	public $entityTable = 'zParseWork';
	public $primaryKey = 'id';
	public $control = null;
	public $mode = null;
	public $iteration = null;
	public $prodLink = null;
	public $catLink = null;
	public $params = null;
	public $status = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = 'STATUS_NEW';				// новое
	const STATUS_PROCESSED = 'STATUS_PROCESSED';	// обработанное

	function zParseWork()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'control' => self::ENTITY_FIELD_STRING,
			'mode' => self::ENTITY_FIELD_STRING,
			'iteration' => self::ENTITY_FIELD_INT,
			'prodLink' => self::ENTITY_FIELD_STRING,
			'catLink' => self::ENTITY_FIELD_STRING,
			'params' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
