<?php
/**
* Сущность рассылка на вконтакт
*
*/
class allvkNotice extends BaseEntity
{
	public $entityTable = 'allvkNotice';
	public $status = null;
	public $vkId = null;
	public $body = null;
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
			"vkId" => self::ENTITY_FIELD_INT,
			"body" => self::ENTITY_FIELD_STRING,
			"ccId" => self::ENTITY_FIELD_INT,
			"zcId" => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
