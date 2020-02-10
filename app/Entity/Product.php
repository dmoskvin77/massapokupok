<?php
/**
* Сущность товар
*
*/
class Product extends BaseEntity
{
	public $entityTable = 'product';
	public $orgId = null;
	public $name = null;
	public $artNumber = null;
	public $prodLink = null;
	public $entityStatus = null;
	public $status = null;
	public $rankValue = null;
	public $saleValue = null;
	public $dateCreate = null;
	public $dateUpdate = null;
	public $description = null;
	public $picFile1 = null;
	public $picFile2 = null;
	public $picFile3 = null;
	public $picSrv1 = null;
	public $picSrv2 = null;
	public $picSrv3 = null;
	public $picVer1 = 1;
	public $picVer2 = 1;
	public $picVer3 = 1;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_ENABLED = 'STATUS_ENABLED';
	const STATUS_DISABLED = 'STATUS_DESABLED';

	function getFields()
	{
        return array
        (
			"id" => self::ENTITY_FIELD_INT,
			"orgId" => self::ENTITY_FIELD_INT,
			"name" => self::ENTITY_FIELD_STRING,
			"artNumber" => self::ENTITY_FIELD_STRING,
	        "prodLink" => self::ENTITY_FIELD_STRING,
			"entityStatus" => self::ENTITY_FIELD_INT,
			"status" => self::ENTITY_FIELD_STRING,
			"rankValue" => self::ENTITY_FIELD_INT,
			"saleValue" => self::ENTITY_FIELD_STRING,
			"dateCreate" => self::ENTITY_FIELD_INT,
			"dateUpdate" => self::ENTITY_FIELD_INT,
			"description" => self::ENTITY_FIELD_STRING,
			"picFile1" => self::ENTITY_FIELD_STRING,
			"picFile2" => self::ENTITY_FIELD_STRING,
			"picFile3" => self::ENTITY_FIELD_STRING,
			"picSrv1" => self::ENTITY_FIELD_STRING,
			"picSrv2" => self::ENTITY_FIELD_STRING,
			"picSrv3" => self::ENTITY_FIELD_STRING,
			"picVer1" => self::ENTITY_FIELD_INT,
			"picVer2" => self::ENTITY_FIELD_INT,
			"picVer3" => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }
}
