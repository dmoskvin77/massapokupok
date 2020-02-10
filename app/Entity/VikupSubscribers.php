<?php
/**
 * Подписки участников на выкупы
 *
 */
class VikupSubscribers extends Entity
{
	public $entityTable = 'vikupSubscribers';
	public $primaryKey = 'id';
	public $userId = null;
	public $vikupId = null;
	public $dateUpdate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function VikupSubscribers()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'vikupId' => self::ENTITY_FIELD_INT,
			'dateUpdate' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
