<?php
/**
 * Сущность Яндекс касса кнопка
 * https://money.yandex.ru/fastpay/
 *
 */
class Yakassabutton extends Entity
{
	public $entityTable = 'yakassabutton';
	public $primaryKey = 'id';
	public $userId = null;
	public $buttonCode = null;
	public $status = 1;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function Yakassabutton()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'buttonCode' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
