<?php
/**
 * Сущность категории товара
 *
 */
class Category extends Entity
{
	public $entityTable = 'category';
	public $primaryKey = 'id';
	public $parentId = null;
	public $name = null;
	public $path = null;
	public $level = 1;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function Category()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'parentId' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
			'path' => self::ENTITY_FIELD_STRING,
			'level' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
