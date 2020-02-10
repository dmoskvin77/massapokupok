<?php
/**
* Сущность клуб (для вконтакта)
*
*/
class Club extends BaseEntity
{
	public $entityTable = 'club';
	public $name = null;
	public $vkClubId = null;
	public $entityStatus = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
	function getFields()
    {
        return array
        (
			"id" => self::ENTITY_FIELD_INT,
			"name" => self::ENTITY_FIELD_STRING,
			"vkClubId" => self::ENTITY_FIELD_INT,
			"entityStatus" => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
