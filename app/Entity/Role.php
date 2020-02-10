<?php
/**
 * Роль оператора
 * 
 */
class Role extends Entity
{
	public $entityTable = 'role';
	public $primaryKey = 'id';
	public $name = null;	
	public $permissions = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
	function Role()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
        	'permissions' => self::ENTITY_FIELD_STRING,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
