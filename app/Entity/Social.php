<?php
/**
* Сущность социальный профиль
*
*/
class Social extends Entity
{
	public $entityTable = 'social';
	public $primaryKey = 'id';
	public $userId = null;
	public $network = null;
	public $first_name = null;
	public $last_name = null;
	public $profile = null;
	public $uid = null;
	public $identity = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function Social()
	{
	}

	function getFields()
    {
        return array
        (
			"id" => self::ENTITY_FIELD_INT,
			"userId" => self::ENTITY_FIELD_INT,
			"network" => self::ENTITY_FIELD_STRING,
			"first_name" => self::ENTITY_FIELD_STRING,
			"last_name" => self::ENTITY_FIELD_STRING,
			"profile" => self::ENTITY_FIELD_STRING,
			"uid" => self::ENTITY_FIELD_INT,
			"identity" => self::ENTITY_FIELD_STRING,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }
}
