<?php
/**
* Сущность страна
*
*/
class Country extends BaseEntity
{
	public $entityTable = 'country';
	public $name = null;
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
			"vkCountryId" => self::ENTITY_FIELD_INT,
			"entityStatus" => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
