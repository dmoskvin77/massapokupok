<?php
/**
* Сущность город
*
*/
class City extends BaseEntity
{
	public $entityTable = 'city';
	public $name = null;
	public $predName = null;
	public $vkCityId = null;
	public $vkCountryId = null;
	public $entityStatus = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
	function getFields()
    {
        return array
        (
			"id" => self::ENTITY_FIELD_INT,
			"name" => self::ENTITY_FIELD_STRING,
			"predName" => self::ENTITY_FIELD_STRING,
			"vkCityId" => self::ENTITY_FIELD_INT,
			"vkCountryId" => self::ENTITY_FIELD_INT,
			"entityStatus" => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
