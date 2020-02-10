<?php
/**
 * Сущность запись журнала потенциальных опасностей
 *
 */
class SecurityLog extends Entity
{
	public $entityTable = 'securityLog';
	public $primaryKey = 'id';
	public $section = null;
	public $userId = null;
	public $orgId = null;
	public $operId = null;
	public $actionType = null;
	public $request = null;
	public $info = null;
	public $ip = null;
	public $longIp = null;
	public $dateCreate = null;
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;

	/** Попытка подбора пароля */
	const TYPE_PASSWORD = "TYPE_PASSWORD";

	/** попытки доступа персонала бэкофиса к функционалу, запрещенному ролью */
	const TYPE_ACCESS_DENIED = "TYPE_ACCESS_DENIED";

	// нет доступа - несанкционированный доступ в фронт офисе
	const TYPE_NO_ACCESS = "TYPE_NO_ACCESS";
	
	/** слишком большое количество запросов одного пользователя в единицу времени */
	const TYPE_TOO_MANY_REQUESTS = "TYPE_TOO_MANY_REQUESTS";

	/** попытки sql-инъекций, php-инъекций, обхода каталогов, других способов, направленных на 
		получение несанкционированного доступа к данным системы или изменение логики работы системы 
	*/
	const TYPE_INJECTION = "TYPE_INJECTION";
	
	/** Действие направлено на магазин */
	const SECTION_FRONT = "SECTION_FRONT";
	
	/** Действие направлено на бэкофис */
	const SECTION_ADMINKA = "SECTION_ADMINKA";
	
	/** по логам mod_evasive невозможно определить на какой раздел  была DOS атака*/
	const SECTION_SERVER = "SECTION_SERVER";
	
	/**
	 * Возвращает описание типа по его ID
	 * Или список типов с описаниями
	 *
	 * @param string $status
	 * @return array or string
	 */
    public static function getTypesDesc($type = null)
    {
    	$typeList = array(
			self::TYPE_PASSWORD => "Подбора пароля",
			self::TYPE_ACCESS_DENIED => "Доступ персонала к функционалу, запрещенному правами",
			self::TYPE_NO_ACCESS => "Попытка несанкционированного доступа во фронт части",
			self::TYPE_INJECTION => "Инъекция вредоносного кода",
			self::TYPE_TOO_MANY_REQUESTS => "Слишком большое количество запросов одного пользователя"			
		);

		return $type ? $typeList[$type] : $typeList;
    }	
    
    
    
    /**
     * Тип в текстовом виде
     *
     * @return string
     */
    public function getTypeString()
    {
    	return self::getTypesDesc($this->actionType);
    }
    
 	/**
	 * Возвращает описание раздела приложения по его ID
	 * Или список разделов с описаниями
	 *
	 * @param string $section
	 * @return array or string
	 */
    public static function getSectionDesc($section = null)
    {
    	$typeList = array(
			self::SECTION_ADMINKA => "Админка",
			self::SECTION_FRONT => "Фронт",
			self::SECTION_SERVER => "Сервер",
				
		);

		return $section ? $typeList[$section] : $typeList;
    }	
    
   /**
     * Раздел в текстовом виде
     *
     * @return string
     */
    public function getSectionString()
    {
    	return self::getSectionDesc($this->section);
    }
	
	
	function SecurityLog()
	{		
	}
	
	function getFields()
	{
		return array
		(
			'id' => self::ENTITY_FIELD_INT,
			'section' => self::ENTITY_FIELD_STRING,
			'userId' => self::ENTITY_FIELD_INT,
			'orgId' => self::ENTITY_FIELD_INT,
			'operId' => self::ENTITY_FIELD_INT,
			'actionType' => self::ENTITY_FIELD_STRING,
			'request' => self::ENTITY_FIELD_STRING,
			'info' => self::ENTITY_FIELD_STRING,
			'ip' => self::ENTITY_FIELD_STRING,
			'longIp' => self::ENTITY_FIELD_INT,
			'dateCreate' => self::ENTITY_FIELD_INT,
	        "ownerSiteId" => self::ENTITY_FIELD_INT,
			"ownerOrgId" => self::ENTITY_FIELD_INT,
		);
	}	
}
