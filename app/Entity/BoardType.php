<?php
/**
 * Сущность тип объявления (продам, куплю и т.д.)
 *
 */
class BoardType extends Entity
{
	public $entityTable = 'boardType';
	public $primaryKey = 'id';
	public $name = null;
	public $alias = null;
	public $cnt = 0;
	public $position = 0;
	public $status = 1;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function BoardType()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
			'alias' => self::ENTITY_FIELD_STRING,
			'cnt' => self::ENTITY_FIELD_INT,
			'position' => self::ENTITY_FIELD_INT,
			'status' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
