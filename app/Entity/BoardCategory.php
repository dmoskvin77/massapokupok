<?php
/**
 * Сущность категории объявления (машина, квартира и т.д.) - что продаём
 *
 */
class BoardCategory extends Entity
{
	public $entityTable = 'boardCategory';
	public $primaryKey = 'id';
	public $boardTypeId = null;
	public $name = null;
	public $alias = null;
	public $cnt = 0;
	public $position = 0;
	public $status = 1;
	public $optionType1 = null;
	public $optionName1 = null;
	public $optionType2 = null;
	public $optionName2 = null;
	public $optionType3 = null;
	public $optionName3 = null;
	public $optionType4 = null;
	public $optionName4 = null;
	public $optionType5 = null;
	public $optionName5 = null;
	public $optionType6 = null;
	public $optionName6 = null;
	public $optionType7 = null;
	public $optionName7 = null;
	public $optionType8 = null;
	public $optionName8 = null;
	public $optionType9 = null;
	public $optionName9 = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function BoardCategory()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'boardTypeId' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
			'alias' => self::ENTITY_FIELD_STRING,
			'cnt' => self::ENTITY_FIELD_INT,
			'position' => self::ENTITY_FIELD_INT,
			'status' => self::ENTITY_FIELD_INT,
			'optionType1' => self::ENTITY_FIELD_STRING,
			'optionName1' => self::ENTITY_FIELD_STRING,
			'optionType2' => self::ENTITY_FIELD_STRING,
			'optionName2' => self::ENTITY_FIELD_STRING,
			'optionType3' => self::ENTITY_FIELD_STRING,
			'optionName3' => self::ENTITY_FIELD_STRING,
			'optionType4' => self::ENTITY_FIELD_STRING,
			'optionName4' => self::ENTITY_FIELD_STRING,
			'optionType5' => self::ENTITY_FIELD_STRING,
			'optionName5' => self::ENTITY_FIELD_STRING,
			'optionType6' => self::ENTITY_FIELD_STRING,
			'optionName6' => self::ENTITY_FIELD_STRING,
			'optionType7' => self::ENTITY_FIELD_STRING,
			'optionName7' => self::ENTITY_FIELD_STRING,
			'optionType8' => self::ENTITY_FIELD_STRING,
			'optionName8' => self::ENTITY_FIELD_STRING,
			'optionType9' => self::ENTITY_FIELD_STRING,
			'optionName9' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
