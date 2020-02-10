<?php
/**
* Сущность новость
*/
class News extends Entity
{
	public $entityTable = 'news';
	public $primaryKey = 'id';
	public $type = null;
	public $subject = null;	
	public $body = null;
	public $showDate = null;	
	public $creationDate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const TYPE_SYSTEM = 1;
	const TYPE_ORG = 2;
	const TYPE_USER = 3;

	function News()
	{
	}

	function getFields()
	{
		return array
		(
			"id" => self::ENTITY_FIELD_INT,
			"type" => self::ENTITY_FIELD_INT,
			"subject" => self::ENTITY_FIELD_STRING,
			"body" => self::ENTITY_FIELD_STRING,
			"showDate" => self::ENTITY_FIELD_INT,
			"creationDate" => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
