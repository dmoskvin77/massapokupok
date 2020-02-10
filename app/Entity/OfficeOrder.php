<?php
/**
 * Сущность контент (страницы сайта)
 */
class OfficeOrder extends Entity
{
	public $entityTable = 'officeOrder';
	public $primaryKey = 'id';
    public $officeId = null;
    public $orderId = null;
	public $userId = null;
	public $headId = null;
	public $orgId = null;
	public $status = null;
    public $rejectReason = null;
    public $price = 0;
    public $payHold = 0;
    public $payAmount = 0;
    public $payBackAmount = 0;
	public $payStatus = null;
	public $tsOrder = 0;
	public $tsOrg = 0;
	public $tsOffice = 0;
	public $tsUser = 0;
	public $officeUserId = null;
    public $ownerSiteId = 0;
    public $ownerOrgId = 0;
	
    // общие статусы офисной доставки
    const STATUS_NEW = "STATUS_NEW";
    const STATUS_REJECTED = "STATUS_REJECTED"; // отказано в доставке через офис
    // организатор принял заказ в обработку на доставку
    const STATUS_ORG = "STATUS_ORG";
    const STATUS_OFFICE = "STATUS_OFFICE";
    const STATUS_USER = "STATUS_USER";

    const STATUS_PAY_NONE = "STATUS_PAY_NONE";
    const STATUS_PAY_NEW = "STATUS_PAY_NEW";
    const STATUS_PAY_CONFIRMED = "STATUS_PAY_CONFIRMED";
	
	function OfficeOrder()
	{		
	}

    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
            'officeId' => self::ENTITY_FIELD_INT,
            'orderId' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'orgId' => self::ENTITY_FIELD_INT,
			'status' => self::ENTITY_FIELD_STRING,
            'rejectReason' => self::ENTITY_FIELD_STRING,
            'price' => self::ENTITY_FIELD_STRING,
            'payHold' => self::ENTITY_FIELD_STRING,
            'payAmount' => self::ENTITY_FIELD_STRING,
            'payBackAmount' => self::ENTITY_FIELD_STRING,
			'payStatus' => self::ENTITY_FIELD_STRING,
			'tsOrder' => self::ENTITY_FIELD_INT,
			'tsOrg' => self::ENTITY_FIELD_INT,
			'tsOffice' => self::ENTITY_FIELD_INT,
			'tsUser' => self::ENTITY_FIELD_INT,
	        "officeUserId" => self::ENTITY_FIELD_INT,
            "ownerSiteId" => self::ENTITY_FIELD_INT,
            "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }

	public static function getStatusDesc($status = null)
	{
        $statuses = array(
						self::STATUS_NEW => "Новая доставка",
                        self::STATUS_REJECTED => "Отказано",
						self::STATUS_ORG => "Обработано организатором",
						self::STATUS_OFFICE => "В офисе",
                        self::STATUS_USER => "Получено участником",
					);

		return $status ? $statuses[$status] : $statuses;
	}

    public static function getPayStatusDesc($status = null)
    {
        $statuses = array(
            self::STATUS_PAY_NONE => "Без оплаты",
            self::STATUS_PAY_NEW => "Новая оплата",
            self::STATUS_PAY_CONFIRMED => "Оплата подтверждена"
        );

        return $status ? $statuses[$status] : $statuses;
    }

}
