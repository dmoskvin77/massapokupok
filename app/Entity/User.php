<?php
/**
* Сущность пользователь
*
*/
class User extends BaseEntity
{
	public $entityTable = 'user';
	public $dateCreate = null;
	public $dateLastVisit = null;
	public $login = null;
	public $password = null;
	public $confirmCode = null;
	public $nickName = null;
	public $firstName = null;
	public $lastName = null;
	public $secondName = null;
	public $phone1 = null;
	public $phone2 = null;
	public $name = null;
	public $avatar = null;
	public $isOrg = null;
	public $orgPersent = 0;
	public $isOpt = null;
	public $isBot = null;
	public $requestOrg = null;
	public $requestOpt = null;
	public $entityStatus = null;
	public $status = null;
	public $rating = null;
	public $pozitiveFeeds = null;
	public $negativeFeeds = null;
	// доверенный участник, может постить каменты без модерации
	public $isAproved = 0;
	// крайняя дата рассылки дайджеста
	public $dateDigest = 0;
	// частота рассылки дайджеста, дней
	public $digestFrequency = 7;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	// надо ещё поля:
	// кол-во закупок, в которых учавствовал
	// кол-во оплаченных
	// кол-во неоплаченных закупок
	// оплаченные и неоплаченные за последний год

	function getFields()
	{
       return array
       (
			"id" => self::ENTITY_FIELD_INT,
			"dateCreate" => self::ENTITY_FIELD_INT,
			"dateLastVisit" => self::ENTITY_FIELD_INT,
			"login" => self::ENTITY_FIELD_STRING,
			"password" => self::ENTITY_FIELD_STRING,
			"confirmCode" => self::ENTITY_FIELD_STRING,
			"nickName" => self::ENTITY_FIELD_STRING,
			"firstName" => self::ENTITY_FIELD_STRING,
			"lastName" => self::ENTITY_FIELD_STRING,
			"secondName" => self::ENTITY_FIELD_STRING,
			"phone1" => self::ENTITY_FIELD_STRING,
			"phone2" => self::ENTITY_FIELD_STRING,
			"name" => self::ENTITY_FIELD_STRING,
			"avatar" => self::ENTITY_FIELD_STRING,
			"isOrg" => self::ENTITY_FIELD_INT,
			"orgPersent" => self::ENTITY_FIELD_STRING,
			"isOpt" => self::ENTITY_FIELD_INT,
			"isBot" => self::ENTITY_FIELD_INT,
			"requestOrg" => self::ENTITY_FIELD_INT,
			"requestOpt" => self::ENTITY_FIELD_INT,
			"entityStatus" => self::ENTITY_FIELD_INT,
			"status" => self::ENTITY_FIELD_STRING,
			"rating" => self::ENTITY_FIELD_INT,
			"pozitiveFeeds" => self::ENTITY_FIELD_INT,
			"negativeFeeds" => self::ENTITY_FIELD_INT,
			"isAproved" => self::ENTITY_FIELD_INT,
			"dateDigest" => self::ENTITY_FIELD_INT,
			"digestFrequency" => self::ENTITY_FIELD_INT,
			"ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
       );
    }
}
