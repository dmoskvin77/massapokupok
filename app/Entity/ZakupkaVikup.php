<?php
/**
 * Сущность выкуп закупки
 *
 */
class ZakupkaVikup extends Entity
{
	public $entityTable = 'zakupkaVikup';
	public $primaryKey = 'id';
	public $name = null;
	public $orgId = null;
	public $countZheads = 1;
	public $dateCreate = null;
	public $dateSentremind = 0;
	public $calendarData = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function ZakupkaVikup()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
			'orgId' => self::ENTITY_FIELD_INT,
			'countZheads' => self::ENTITY_FIELD_INT,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateSentremind' => self::ENTITY_FIELD_INT,
			'calendarData' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
