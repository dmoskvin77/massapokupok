<?php
/**
 * Сущность контент (страницы сайта)
 */
class Content extends Entity
{
	public $entityTable = 'content';
	public $primaryKey = 'id';
	public $alias = null;
	public $title = null;
	public $pageTitle = null;
	public $pageDesc = null;
	public $pageKeys = null;
	public $text = null;
	public $ts = null;
	public $status = self::STATUS_DISABLED;
	public $menu = self::MENU_NONE;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	
	/* Черновой вариант страницы. Не показывается */
	const STATUS_DISABLED = "STATUS_DISABLED";
	
	/* Чистовой вариант */
	const STATUS_ENABLED = "STATUS_ENABLED";

	/* Отображение страницы в меню. Не отображать */
	const MENU_NONE = "MENU_NONE";

	/* Отображать в меню вверху страницы */
	const MENU_TOP = "MENU_TOP";

	/* Отображать в меню внизу страницы */
	const MENU_BOTTOM = "MENU_BOTTOM";

	
	function Content()
	{		
	}


    function getFields()
    {
        return array
        (
            'id' => self::ENTITY_FIELD_INT,
			'alias' => self::ENTITY_FIELD_STRING,
			'title' => self::ENTITY_FIELD_STRING,
			'pageTitle' => self::ENTITY_FIELD_STRING,
			'pageDesc' => self::ENTITY_FIELD_STRING,
			'pageKeys' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
			'menu' => self::ENTITY_FIELD_STRING,
			'text' => self::ENTITY_FIELD_STRING,
			'ts' => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
	        "ownerOrgId" => self::ENTITY_FIELD_INT,
        );
    }

	public static function getMenuDesc($menu = null)
	{
		$menus = array(
						self::MENU_NONE => "Не отображается",
						self::MENU_TOP => "Меню 1 (выше)",
						self::MENU_BOTTOM => "Меню 2 (ниже)"
					);

		return $menu ? $menus[$menu] : $menus;
	}

}
