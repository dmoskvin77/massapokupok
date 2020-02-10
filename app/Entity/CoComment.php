<?php
/**
 * Сущность каменты к контенту
 *
 */
class CoComment extends Entity
{
	public $entityTable = 'coComment';
	public $primaryKey = 'id';

	public $type = null;
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
	const STATUS_ANSWERED = 'STATUS_ANSWERED';		// отвеченное (в заказах)

	// типы пользователей
	const TYPE_USER = 'TYPE_USER';		// покупатель
	const TYPE_ADMIN = 'TYPE_ADMIN';	// ваще админ

	// типы комментариев
	const COMMENT_CONTENT = 'COMMENT_CONTENT';
	const COMMENT_ZAKUPKA = 'COMMENT_ZAKUPKA';

	const COMMENT_ORDER = 'COMMENT_ORDER';
	const COMMENT_PROFI = 'COMMENT_PROFI';

	function CoComment()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'type' => self::ENTITY_FIELD_STRING,
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
