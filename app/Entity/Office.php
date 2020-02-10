<?php
/**
 * Сущность офисы раздач
 *
 */
class Office extends Entity
{
	public $entityTable = 'office';
	public $primaryKey = 'id';
	public $name = null;
	public $address = null;
	public $schedule = null;
	public $status = self::STATUS_ENABLED;
    public $price = 0;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const STATUS_DISABLED = "STATUS_DISABLED";
	const STATUS_ENABLED = "STATUS_ENABLED";

	function Office()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
			'address' => self::ENTITY_FIELD_STRING,
			'schedule' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
            'price' => self::ENTITY_FIELD_STRING,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
