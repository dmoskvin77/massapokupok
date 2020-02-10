<?php
/**
 * Сущность голосование за закупку
 *
 */
class ZakupkaVote extends Entity
{
	public $entityTable = 'zakupkaVote';
	public $primaryKey = 'id';
	public $headId = null;
	public $orgId = null;
	public $userId = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function ZakupkaVote()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'headId' => self::ENTITY_FIELD_INT,
			'orgId' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
