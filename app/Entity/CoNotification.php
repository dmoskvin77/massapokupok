<?php
/**
 * Нотификации по каментам
 */
class CoNotification extends Entity
{
	public $entityTable = 'coNotification';
	public $primaryKey = 'id';
	public $headId = null;
	public $userId = null;
	public $zakName = null;
	public $dateUpdate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
	function CoNotification()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
	        'zakName' => self::ENTITY_FIELD_STRING,
			'dateUpdate' => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }
}
