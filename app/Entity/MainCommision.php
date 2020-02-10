<?php
/**
 * Комиссия сайту
 *
 */
class MainCommision extends Entity
{
	public $entityTable = 'mainCommision';
	public $primaryKey = 'id';
	public $baseAmount = 0;
	public $needAmount = 0;
	public $payAmount = 0;
	public $dateCreate = null;
	public $dateConfirm = null;
	public $userInfo = null;
	public $type = null;
	public $way = null;
	public $status = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	// за что проводится оплата
	// за закупки
	const TYPE_CLICK = "TYPE_CLICK";

	// оплата вручную (перевод на карту СБ и т.д.)
	const WAY_HAND_PAY = "WAY_HAND_PAY";
	const WAY_EK = "WAY_EK";

	const STATUS_NEW = "STATUS_NEW";
	const STATUS_SUCCED = "STATUS_SUCCED";
	const STATUS_CANCEL = "STATUS_CANCEL";

	function MainCommision()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'baseAmount' => self::ENTITY_FIELD_STRING,
			'needAmount' => self::ENTITY_FIELD_STRING,
			'payAmount' => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateConfirm' => self::ENTITY_FIELD_INT,
			'userInfo' => self::ENTITY_FIELD_STRING,
			'type' => self::ENTITY_FIELD_STRING,
			'way' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}

	// описание статусов операции
    public static function getStatusDesc($stat = null)
    {
    	$statList = array(
				self::STATUS_NEW => "В обработке",
				self::STATUS_SUCCED => "Подтверждено",
				self::STATUS_CANCEL => "Отменено",
			);

		return $stat ? $statList[$stat] : $statList;
    }

	// описание за что оплачено
    public static function getTypeDesc($type = null)
    {
    	$typeList = array(
				self::TYPE_CLICK => "Оплата за подтвержденный заказ",
			);

		return $type ? $typeList[$type] : $typeList;
    }

	// описание способа оплаты
    public static function getWayDesc($way = null)
    {
    	$wayList = array(
				self::WAY_HAND_PAY => "Ручной перевод",
		        self::WAY_EK => "Единый кошелёк",
			);

		return $way ? $wayList[$way] : $wayList;
    }
}
