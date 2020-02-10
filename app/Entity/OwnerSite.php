<?php
/**
 * Сущность сайт - владелец бизнеса - сайта
 */
class OwnerSite extends Entity
{
	public $entityTable = 'ownerSite';
	public $primaryKey = 'id';
	public $hostName = null;
	public $tplFolder = null;
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

	function OwnerSite()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'hostName' => self::ENTITY_FIELD_STRING,
	        'tplFolder' => self::ENTITY_FIELD_STRING,
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
