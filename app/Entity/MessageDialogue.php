<?php
/**
 * Диалог м/у
 */
class MessageDialogue extends Entity
{
	public $entityTable = 'messageDialogue';
	public $primaryKey = 'id';
	public $user1 = null;
	public $userNick1 = null;
	public $user2 = null;
	public $userNick2 = null;
	public $lastReadId12 = 0;
	public $lastReadId21 = 0;
	public $hasNew12 = 0;
	public $hasNew21 = 0;
	public $dateUpdate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function MessageDialogue()
	{
	}

	function getFields()
	{
        return array
        (
			'id' => self::ENTITY_FIELD_INT,
			'user1' => self::ENTITY_FIELD_INT,
			'userNick1' => self::ENTITY_FIELD_STRING,
			'user2' => self::ENTITY_FIELD_INT,
			'userNick2' => self::ENTITY_FIELD_STRING,
			'lastReadId12' => self::ENTITY_FIELD_INT,
			'lastReadId21' => self::ENTITY_FIELD_INT,
			'hasNew12' => self::ENTITY_FIELD_INT,
			'hasNew21' => self::ENTITY_FIELD_INT,
			'dateUpdate' => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
	}
}