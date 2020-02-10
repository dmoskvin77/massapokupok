<?php
/**
 * Товары заказа
 *
 */
class OrderList extends Entity
{
	public $entityTable = 'orderList';
	public $primaryKey = 'id';
	public $orderId = null;
	public $userId = null;
	public $orgId = null;
	public $headId = null;
	public $zlId = null;
	public $rp = null;				// номер ячейки в ряду
	public $num = 1;				// номер линии ряда
	public $stopDel = 0;
	public $isFull = 0;
	public $prodId = null;
	public $prodName = null;
	public $prodArt = null;
	public $optPrice = 0;
	public $opttoorgDlvrAmount = 0;
	public $count = 0;
	public $size = null;
	public $color = null;
	public $comment = null;
	public $status = null;
	public $dateCreate = null;
	public $dateUpdate = null;
	public $dateConfirm = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = "STATUS_NEW";
	const STATUS_CONFIRM = "STATUS_CONFIRM";
	const STATUS_REJECT = "STATUS_REJECT";

	function OrderList()
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
			'headId' => self::ENTITY_FIELD_INT,
			'zlId' => self::ENTITY_FIELD_INT,
			'rp' => self::ENTITY_FIELD_INT,
			'num' => self::ENTITY_FIELD_INT,
			'stopDel' => self::ENTITY_FIELD_INT,
			'isFull' => self::ENTITY_FIELD_INT,
			'prodId' => self::ENTITY_FIELD_INT,
			'prodName' => self::ENTITY_FIELD_STRING,
			'prodArt' => self::ENTITY_FIELD_STRING,
			'optPrice' => self::ENTITY_FIELD_STRING,
			'opttoorgDlvrAmount' => self::ENTITY_FIELD_STRING,
			'count' => self::ENTITY_FIELD_INT,
			'size' => self::ENTITY_FIELD_STRING,
			'color' => self::ENTITY_FIELD_STRING,
			'comment' => self::ENTITY_FIELD_STRING,
			"status" => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateUpdate' => self::ENTITY_FIELD_INT,
			'dateConfirm' => self::ENTITY_FIELD_INT,
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
