<?php
/**
 * Сущность категории товара
 * 
 */
class Pro extends Entity
{
	public $entityTable = 'pro';
	public $primaryKey = 'id';
	public $userId = null;
	public $validTo = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function Pro()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
        	'validTo' => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }	
}
