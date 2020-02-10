<?php
/**
* Сущность рассылка почтой
*/
class allMail extends BaseEntity
{
	public $entityTable = 'allMail';
	public $status = null;
	public $subj = null;
	public $body = null;
	public $toMail = null;
	public $mailFrom = null;
	public $mailFromName = null;
	public $ccId = null;
	public $zcId = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = 0;
	const STATUS_SENT = 1;
	const STATUS_PENDING = 2;
	
	function getFields()
    {
        return array
        (
			"id" => self::ENTITY_FIELD_INT,
			"status" => self::ENTITY_FIELD_STRING,
			"subj" => self::ENTITY_FIELD_STRING,
			"body" => self::ENTITY_FIELD_STRING,
			"toMail" => self::ENTITY_FIELD_STRING,
	        "mailFrom" => self::ENTITY_FIELD_STRING,
	        "mailFromName" => self::ENTITY_FIELD_STRING,
			"ccId" => self::ENTITY_FIELD_INT,
			"zcId" => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
