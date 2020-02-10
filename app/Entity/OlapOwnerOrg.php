<?php
/**
 */
class OlapOwnerOrg extends Entity
{
	public $entityTable = 'olapOwnerOrg';
	public $primaryKey = 'id';

    public $dataDate = null;
	public $ownerOrgId = null;

    public $countUsers = 0;
    public $countActiveUsers = 0;
    public $countZak = 0;
    public $countZakOpen = 0;
    public $countZakFinished = 0;
    public $amountZak = 0;
    public $amountOrgCommisionAdded = 0;
    public $amountOrgCommisionPayed = 0;
    public $amountSiteCommisionAdded = 0;
    public $amountSiteCommisionPayed = 0;
    public $amountMainCommisionAdded = 0;
    public $amountMainCommisionPayed = 0;


	function OlapOwnerOrg()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
            'dataDate' => self::ENTITY_FIELD_STRING,
	        'ownerOrgId' => self::ENTITY_FIELD_INT,

            'countUsers' => self::ENTITY_FIELD_INT,
            'countActiveUsers' => self::ENTITY_FIELD_INT,
            'countZak' => self::ENTITY_FIELD_INT,
            'countZakOpen' => self::ENTITY_FIELD_INT,
            'countZakFinished' => self::ENTITY_FIELD_INT,

            'amountZak' => self::ENTITY_FIELD_STRING,
            'amountOrgCommisionAdded' => self::ENTITY_FIELD_STRING,
            'amountOrgCommisionPayed' => self::ENTITY_FIELD_STRING,
            'amountSiteCommisionAdded' => self::ENTITY_FIELD_STRING,
            'amountSiteCommisionPayed' => self::ENTITY_FIELD_STRING,
            'amountMainCommisionAdded' => self::ENTITY_FIELD_STRING,
            'amountMainCommisionPayed' => self::ENTITY_FIELD_STRING,

        );
    }
}
