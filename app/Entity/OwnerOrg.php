<?php
/**
 * Сущность отделившийся организатор
 */
class OwnerOrg extends Entity
{
	public $entityTable = 'ownerOrg';
	public $primaryKey = 'id';

	public $ownerSiteId = null;
	public $alias = null;
	public $tplFolder = null;
	public $orgId = null;
	public $status = self::STATUS_ENABLED;

    public $dateCreate = null;
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


	const STATUS_DISABLED = "STATUS_DISABLED";
	const STATUS_ENABLED = "STATUS_ENABLED";

	function OwnerOrg()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
	        'ownerSiteId' => self::ENTITY_FIELD_INT,
			'alias' => self::ENTITY_FIELD_STRING,
	        'tplFolder' => self::ENTITY_FIELD_STRING,
	        'orgId' => self::ENTITY_FIELD_INT,
			'status' => self::ENTITY_FIELD_STRING,

            'dateCreate' => self::ENTITY_FIELD_INT,
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
