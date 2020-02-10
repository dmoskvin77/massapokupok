<?php
/**
 * Очередь заданий для асинхронного выполнения сложных задач
 *
 */
class QueueMysql extends Entity
{
	public $entityTable = 'queueMysql';
	public $primaryKey = 'id';
	public $taskName = null;
	public $fromUserId = null;
	public $fromNickName = null;
	public $headId = null;
	public $headName = null;
	public $meetId = null;
	public $otherData = null;
	public $dateCreate = null;
	public $dateStart = null;
	public $dateFinish = null;
	public $isFinish = 0;
	public $isError = 0;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	function QueueMysql()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'taskName' => self::ENTITY_FIELD_STRING,
			'fromUserId' => self::ENTITY_FIELD_INT,
			'fromNickName' => self::ENTITY_FIELD_STRING,
			'headId' => self::ENTITY_FIELD_STRING,
			'headName' => self::ENTITY_FIELD_STRING,
			'meetId' => self::ENTITY_FIELD_INT,
			'otherData' => self::ENTITY_FIELD_STRING,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateStart' => self::ENTITY_FIELD_INT,
			'dateFinish' => self::ENTITY_FIELD_INT,
			'isFinish' => self::ENTITY_FIELD_INT,
			'isError' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}
}
