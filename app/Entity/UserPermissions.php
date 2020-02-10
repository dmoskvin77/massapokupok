<?php
/**
 * Сущность дополнительные права пользователя
 */
class UserPermissions extends Entity
{
	public $entityTable = 'userPermissions';
	public $primaryKey = 'id';
	public $userId = null;
	public $type = null;
	public $additionalData = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	const TYPE_OFFICE_MANAGER = "TYPE_OFFICE_MANAGER";

	function UserPermissions()
	{		
	}

    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'type' => self::ENTITY_FIELD_STRING,
			'additionalData' => self::ENTITY_FIELD_STRING,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }

	public static function getTypeDesc($type = null)
	{
		$types = array(
			self::TYPE_OFFICE_MANAGER => "Офис менеджер",
		);

		if ($type && !isset($types[$type]))
			return false;

		return $type ? $types[$type] : $types;
	}

}
