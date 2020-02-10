<?php
/**
 * Сущность ссылки пользователя (для оптовиков)
 *
 */
class UrlList extends Entity
{
	public $entityTable = 'urlList';
	public $primaryKey = 'id';
	public $optId = null;
	public $url = null;
	public $main = null;
	public $status = null;
	public $canParse = 0;
	public $control = null;
	public $parseRequest = 0;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_ENABLED = "STATUS_ENABLED";
	const STATUS_DISABLED = "STATUS_DISABLED";

	function UrlList()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'optId' => self::ENTITY_FIELD_INT,
			'url' => self::ENTITY_FIELD_STRING,
			'main' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
			'canParse' => self::ENTITY_FIELD_INT,
			'control' => self::ENTITY_FIELD_STRING,
			'parseRequest' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
