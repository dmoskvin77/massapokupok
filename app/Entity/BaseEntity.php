<?php
/**
* Базовый контрол сущностей
*
*/
abstract class BaseEntity extends Entity 
{
	public $primaryKey = "id";
	
	public function __construct()
	{
		if(!$this->entityTable)
			$this->entityTable = strtolower(get_class($this));
	}
	
	const ENTITY_STATUS_NOTACTIVE 	= 0;
	const ENTITY_STATUS_ACTIVE 		= 1;
	const ENTITY_STATUS_DELETED 	= 2;
	const ENTITY_STATUS_BLOCKED 	= 3;
	const ENTITY_STATUS_PENDING 	= 4;	// на модерации
	
	function isNotActive() { return $this->entityStatus == self::ENTITY_STATUS_NOTACTIVE; }
	function isActive() { return $this->entityStatus == self::ENTITY_STATUS_ACTIVE; }
	function isDeleted() { return $this->entityStatus == self::ENTITY_STATUS_DELETED; }
	function isBlocked() { return $this->entityStatus == self::ENTITY_STATUS_BLOCKED; }
	function isPending() { return $this->entityStatus == self::ENTITY_STATUS_PENDING; }
}
?>