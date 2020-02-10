<?php
/**
 * Сущность каменты к закупке
 *
 */
class ZhComment extends Entity
{
	public $entityTable = 'zhComment';
	public $primaryKey = 'id';

	public $headId = null;
	public $level = null;
	public $sourceId = null;
	public $rootId = null;
	public $weight = null;
	public $userId = null;
	public $toId = null;
	public $userType = null;
	public $toType = null;
	public $nickName = null;
	public $body = null;
	public $status = null;
	public $wasRead = null;
	public $isPrivate = null;
	public $isAnon = null;
	public $dateCreate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = 'STATUS_NEW';				// новое
	const STATUS_MODERATED = 'STATUS_MODERATED';	// отмодерированное

	const TYPE_USER = 'TYPE_USER';		// покупатель
	const TYPE_ORG = 'TYPE_ORG';		// организатор
	const TYPE_OPT = 'TYPE_OPT';		// оптовик
	const TYPE_ADMIN = 'TYPE_ADMIN';	// ваще админ


	function zhComment()
	{		
	}


	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'level' => self::ENTITY_FIELD_INT,
			'sourceId' => self::ENTITY_FIELD_INT,
			'rootId' => self::ENTITY_FIELD_INT,
			'weight' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'toId' => self::ENTITY_FIELD_INT,
			'userType' => self::ENTITY_FIELD_STRING,
			'toType' => self::ENTITY_FIELD_STRING,
			'nickName' => self::ENTITY_FIELD_STRING,
			'body' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
			'wasRead' => self::ENTITY_FIELD_INT,
			'isPrivate' => self::ENTITY_FIELD_INT,
			'isAnon' => self::ENTITY_FIELD_INT,
			'dateCreate' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}	
}
