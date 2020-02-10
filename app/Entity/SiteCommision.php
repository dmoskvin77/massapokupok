<?php
/**
 * Комиссия сайту
 *
 */
class SiteCommision extends Entity
{
	public $entityTable = 'siteCommision';
	public $primaryKey = 'id';
	public $orgId = null;
	public $headId = null;
	public $orgPersent = 0;
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
	const TYPE_ZAK = "TYPE_ZAK";
	// оплата парсера
	const TYPE_ADDPARSER = "TYPE_ADDPARSER";
	// оплата подключения кошелька
	const TYPE_CONNECTW1 = "TYPE_CONNECTW1";

	// оплата вручную (перевод на карту СБ и т.д.)
	const WAY_HAND_PAY = "WAY_HAND_PAY";
	const WAY_EK = "WAY_EK";

	const STATUS_NEW = "STATUS_NEW";
	const STATUS_SUCCED = "STATUS_SUCCED";
	const STATUS_CANCEL = "STATUS_CANCEL";

	function SiteCommision()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'orgId' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'orgPersent' => self::ENTITY_FIELD_STRING,
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
				self::TYPE_ZAK => "Оплата закупок",
		        self::TYPE_ADDPARSER => "Создание парсера",
				self::TYPE_CONNECTW1 => "Подключеие к Единому Кошельку"
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
