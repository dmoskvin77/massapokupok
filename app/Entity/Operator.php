<?php
/**
 * Сущность оператор (тот кто банит и т.д.)
 * 
 */
class Operator extends BaseEntity
{
	public $entityTable = 'operator';
	public $login = null;	
	public $password = null;
	public $name = null;
	public $phone1 = null;
	public $roleId = null;
	public $dateCreate = null;
	public $dateLastVisit = null;
	public $entityStatus = null;
	public $status = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'login' => self::ENTITY_FIELD_STRING,
        	'password' => self::ENTITY_FIELD_STRING,
        	'name' => self::ENTITY_FIELD_STRING,
        	'phone1' => self::ENTITY_FIELD_STRING,
        	'roleId' => self::ENTITY_FIELD_INT,
        	'dateCreate' => self::ENTITY_FIELD_INT,
        	'dateLastVisit' => self::ENTITY_FIELD_INT,
        	'entityStatus' => self::ENTITY_FIELD_INT,
        	'status' => self::ENTITY_FIELD_STRING,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
