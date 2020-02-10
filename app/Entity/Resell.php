<?php
/**
 * Товары заказа
 *
 */
class Resell extends Entity
{
	public $entityTable = 'resell';
	public $primaryKey = 'id';
	public $orderId = null;
	public $userId = null;
	public $orgId = null;
	public $extUrlId = null;
	public $extUrlTxt = null;
	public $headId = null;
	public $zlId = null;
	public $prodId = null;
	public $prodName = null;
	public $prodArt = null;
	public $picFile1 = null;
	public $picFile2 = null;
	public $picFile3 = null;
	public $picSrv1 = null;
	public $picSrv2 = null;
	public $picSrv3 = null;
	public $picVer1 = null;
	public $picVer2 = null;
	public $picVer3 = null;
	public $optPrice = 0;
	public $resellPrice = 0;
	public $count = 0;
	public $size = null;
	public $color = null;
	public $comment = null;
	public $status = null;
	public $dateCreate = null;
	public $dateUpdate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = "STATUS_NEW";
	const STATUS_EXPIRED = "STATUS_EXPIRED";
	const STATUS_DELETE = "STATUS_DELETE";

	function Resell()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'orderId' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'orgId' => self::ENTITY_FIELD_INT,
			'extUrlId' => self::ENTITY_FIELD_INT,
			'extUrlTxt' => self::ENTITY_FIELD_STRING,
			'headId' => self::ENTITY_FIELD_INT,
			'zlId' => self::ENTITY_FIELD_INT,
			'prodId' => self::ENTITY_FIELD_INT,
			'prodName' => self::ENTITY_FIELD_STRING,
			'prodArt' => self::ENTITY_FIELD_STRING,
			'picFile1' => self::ENTITY_FIELD_STRING,
			'picFile2' => self::ENTITY_FIELD_STRING,
			'picFile3' => self::ENTITY_FIELD_STRING,
			'picSrv1' => self::ENTITY_FIELD_STRING,
			'picSrv2' => self::ENTITY_FIELD_STRING,
			'picSrv3' => self::ENTITY_FIELD_STRING,
			'picVer1' => self::ENTITY_FIELD_INT,
			'picVer2' => self::ENTITY_FIELD_INT,
			'picVer3' => self::ENTITY_FIELD_INT,
			'optPrice' => self::ENTITY_FIELD_STRING,
			'resellPrice' => self::ENTITY_FIELD_STRING,
			'count' => self::ENTITY_FIELD_INT,
			'size' => self::ENTITY_FIELD_STRING,
			'color' => self::ENTITY_FIELD_STRING,
			'comment' => self::ENTITY_FIELD_STRING,
			"status" => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateUpdate' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}

	// описание статусов
    public static function getStatusDesc($stat = null)
    {
    	$statList = array(
				self::STATUS_NEW => "Активен",
				self::STATUS_CONFIRM => "Подтвержден",
				self::STATUS_REJECT => "Нет в наличии"
			);

		return $stat ? $statList[$stat] : $statList;
    }

}
