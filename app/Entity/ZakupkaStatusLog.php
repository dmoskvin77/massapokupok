<?php
/**
 * Лог смены статусов закупки
 */
class ZakupkaStatusLog extends Entity
{
	public $entityTable = 'zakupkaStatusLog';
	public $primaryKey = 'id';
	public $headId = null;
	public $status = null;
	public $dateCreate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
	function ZakupkaStatusLog()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'status' => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }
}
