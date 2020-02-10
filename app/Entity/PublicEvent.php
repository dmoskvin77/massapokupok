<?php
/**
 * Сущность уведомление (публичное)
 *
 */
class PublicEvent extends Entity
{
	public $entityTable = 'publicEvent';
	public $primaryKey = 'id';
	public $fromUserId = null;
	public $fromNickName = null;
	public $toUserId = null;
	public $headId = null;
	public $headName = null;
	public $meetId = null;
	public $dateMeetAccept = null;
	public $resellId = null;
	public $dateResellUpdate = null;
	public $message = null;
	public $dateCreate = null;
	public $dateRead = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function PublicEvent()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'fromUserId' => self::ENTITY_FIELD_INT,
			'fromNickName' => self::ENTITY_FIELD_STRING,
			'toUserId' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_STRING,
			'headName' => self::ENTITY_FIELD_STRING,
			'meetId' => self::ENTITY_FIELD_INT,
			'dateMeetAccept' => self::ENTITY_FIELD_INT,
			'resellId' => self::ENTITY_FIELD_INT,
			'dateResellUpdate' => self::ENTITY_FIELD_INT,
			'message' => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateRead' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
