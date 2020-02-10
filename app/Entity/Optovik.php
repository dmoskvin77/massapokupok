<?php
/**
 * Сущность оптовик (поставщик)
 *
 */
class Optovik extends Entity
{
	public $entityTable = 'optovik';
	public $primaryKey = 'id';
	public $userId = null;
	public $name = null;
	public $status = null;
	public $canParse = 0;
	public $parseRequest = 0;
	public $dateCreate = null;
	public $dateUpdate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = "STATUS_NEW";
	const STATUS_MODER = "STATUS_MODER";
	const STATUS_ACTIVE = "STATUS_ACTIVE";
	const STATUS_FREE = "STATUS_FREE";
	const STATUS_DECLINED = "STATUS_DECLINED";

	function Optovik()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
			'canParse' => self::ENTITY_FIELD_INT,
			'parseRequest' => self::ENTITY_FIELD_INT,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateUpdate' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
