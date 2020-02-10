<?php
/**
 * Сущность область видимости
 */
class ViewArea extends Entity
{
	public $entityTable = 'viewArea';
	public $primaryKey = 'id';
	public $vkClubId = null;
	public $vkCityId = null;
	public $vkCountryId = null;
	public $zakCount = null;
	public $siteId = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
	function ViewArea()
	{		
	}
	
    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'vkClubId' => self::ENTITY_FIELD_INT,
			'vkCityId' => self::ENTITY_FIELD_INT,
			'vkCountryId' => self::ENTITY_FIELD_INT,
			"zakCount" => self::ENTITY_FIELD_INT,
			'siteId' => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }
}
