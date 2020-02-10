<?php
/**
 * Записи на встречу
 */
class Meeting extends Entity
{
	public $entityTable = 'meeting';
	public $primaryKey = 'id';
	public $message = null;
	public $headId = null;
	public $orgId = null;
	public $userCount = 0;
	public $userLimit = 0;
	public $startTs = null;
	public $finishTs = null;
	public $status = self::STATUS_NEW;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = "STATUS_NEW";
	const STATUS_OVER = "STATUS_OVER";

	function Meeting()
	{
	}

	function getFields()
	{
	return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'message' => self::ENTITY_FIELD_STRING,
			'headId' => self::ENTITY_FIELD_INT,
			'orgId' => self::ENTITY_FIELD_INT,
			'userCount' => self::ENTITY_FIELD_INT,
			'userLimit' => self::ENTITY_FIELD_INT,
			'startTs' => self::ENTITY_FIELD_INT,
			'finishTs' => self::ENTITY_FIELD_INT,
			'status' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
