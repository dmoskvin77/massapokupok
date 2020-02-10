<?php
/**
 * Сущность "объявление"
 *
 */
class BoardAd extends Entity
{
	public $entityTable = 'boardAd';
	public $primaryKey = 'id';
	public $userId = null;
	public $sourceType = null;
	public $sourceId = null;
	public $typeId = null;
	public $catId = null;
	public $name = null;
	public $price = 0;
	public $description = null;
	public $status = null;
	public $picFile1 = null;
	public $picSrv1 = null;
	public $picVer1 = 1;
	public $dateCreate = null;
	public $dateUpdate = null;
	public $datePublish = null;
	public $option1 = null;
	public $option2 = null;
	public $option3 = null;
	public $option4 = null;
	public $option5 = null;
	public $option6 = null;
	public $option7 = null;
	public $option8 = null;
	public $option9 = null;
	public $viewCnt = 0;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	// статусы
	const STATUS_NEW = "STATUS_NEW";
	const STATUS_ACTIVE = "STATUS_ACTIVE";
	const STATUS_EXPIRED = "STATUS_EXPIRED";
	const STATUS_DECLINED = "STATUS_DECLINED";
	const STATUS_DELETE = "STATUS_DELETE";

	// модератор заблокировал
	const STATUS_BLOCKED = "STATUS_BLOCKED";

	// пользователь сам скрыл
	const STATUS_HIDE = "STATUS_HIDE";


	function BoardAd()
	{
	}

	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'userId' => self::ENTITY_FIELD_INT,
			'sourceType' => self::ENTITY_FIELD_STRING,
			'sourceId' => self::ENTITY_FIELD_INT,
			'typeId' => self::ENTITY_FIELD_INT,
			'catId' => self::ENTITY_FIELD_INT,
			'name' => self::ENTITY_FIELD_STRING,
			'price' => self::ENTITY_FIELD_STRING,
			'description' => self::ENTITY_FIELD_STRING,
			'status' => self::ENTITY_FIELD_STRING,
			"picFile1" => self::ENTITY_FIELD_STRING,
			"picSrv1" => self::ENTITY_FIELD_STRING,
			"picVer1" => self::ENTITY_FIELD_INT,
			'dateCreate' => self::ENTITY_FIELD_INT,
			'dateUpdate' => self::ENTITY_FIELD_INT,
			'datePublish' => self::ENTITY_FIELD_INT,
			'option1' => self::ENTITY_FIELD_STRING,
			'option2' => self::ENTITY_FIELD_STRING,
			'option3' => self::ENTITY_FIELD_STRING,
			'option4' => self::ENTITY_FIELD_STRING,
			'option5' => self::ENTITY_FIELD_STRING,
			'option6' => self::ENTITY_FIELD_STRING,
			'option7' => self::ENTITY_FIELD_STRING,
			'option8' => self::ENTITY_FIELD_STRING,
			'option9' => self::ENTITY_FIELD_STRING,
			'viewCnt' => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}

	// получить описание статуса
	public static function getStatusDesc($status = null)
	{
		$statuses = array(
			self::STATUS_NEW => "Новое",
			self::STATUS_ACTIVE => "Активно",
			self::STATUS_EXPIRED => "Истек срок",
			self::STATUS_DECLINED => "Отклонено",
			self::STATUS_DELETE => "Удалено",
			self::STATUS_HIDE => "Скрыто",
		);

		return $status ? $statuses[$status] : $statuses;
	}
}
