<?php
/**
* Сущность основные настройки системы
*
*/
class Settings extends Entity
{
	public $entityTable = 'settings';
	public $primaryKey = 'id';
	public $name = null;	
	public $description = null;
	public $type = 'TYPE_TEXT';
	public $value = null;
	public $info = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const TYPE_TEXT = 'TYPE_TEXT';
	const TYPE_TEXTAREA = 'TYPE_TEXTAREA';
	const TYPE_CHECKBOX = 'TYPE_CHECKBOX';

	function Settings()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			"name" => self::ENTITY_FIELD_STRING,
			"description" => self::ENTITY_FIELD_STRING,
			"type" => self::ENTITY_FIELD_STRING,
			"value" => self::ENTITY_FIELD_STRING,
			"info" => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}	
}
