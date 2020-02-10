<?php
/**
* Голова заказа. Должна содержать агреггированные данные о заказе
 *
 * 1) id участника
 * 2) id закупки
 * 3) сумма товаров по цене оптовика (по цене, указанной в закупке)
 * 4) сумма всех оплат участником
 * 5) сумма всех возвратов оргом
 * 6) дата создания записи
 * 7) дата обновления записи
 * 8) кол-во заказанных товаров
 * 9) кол-во подтвержденных заказов
 * 10) статус заказа
 * 11) дата получения
 *
*
*/
class OrderHead extends Entity
{
	public $entityTable = 'orderHead';
	public $primaryKey = 'id';
	public $userId = null;
	public $headId = null;
	public $code = null;
	public $orgRate = 0;
	public $optAmount = 0;
	public $payHold = 0;
	public $payAmount = 0;
	public $payBackAmount = 0;
	public $opttoorgDlvrAmount = 0;
	public $dateCreate = null;
	public $dateUpdate = null;
    public $datePaymentConfirm = null;
	public $allProdCount = 0;
	public $confirmedProdCount = 0;
	public $status = null;
	public $dateUser = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_NEW = 'STATUS_NEW';
	const STATUS_ORG = 'STATUS_ORG';
	const STATUS_USER = 'STATUS_USER';

	function OrderHead()
	{
	}

	function getFields()
	{
        return array
        (
			"id" => self::ENTITY_FIELD_INT,
			"userId" => self::ENTITY_FIELD_INT,
			"headId" => self::ENTITY_FIELD_INT,
	        "code" => self::ENTITY_FIELD_INT,
			"orgRate" => self::ENTITY_FIELD_INT,
			"optAmount" => self::ENTITY_FIELD_STRING,
			"payHold" => self::ENTITY_FIELD_STRING,
			"payAmount" => self::ENTITY_FIELD_STRING,
			"payBackAmount" => self::ENTITY_FIELD_STRING,
	        "opttoorgDlvrAmount" => self::ENTITY_FIELD_STRING,
			"dateCreate" => self::ENTITY_FIELD_INT,
			"dateUpdate" => self::ENTITY_FIELD_INT,
            "datePaymentConfirm" => self::ENTITY_FIELD_INT,
			"allProdCount" => self::ENTITY_FIELD_INT,
			"confirmedProdCount" => self::ENTITY_FIELD_INT,
			"status" => self::ENTITY_FIELD_STRING,
			"dateUser" => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }
}
