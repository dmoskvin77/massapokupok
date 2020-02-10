<?php
require_once APPLICATION_DIR . "/Lib/PartnerAPI/PartnerAPIFactory.php";

/**
 * Обработка запросов, выполнение методов, 
 * генерация ответов на запросы
 * 
 * @version $Id: ProfileManager.php 29 2008-01-24 13:04:43Z afi $
 * @author Andrey Filippov
 */
class APIHandler
{	
	/** режим работы приложения */
	const MODE_NORMAL = "MODE_NORMAL";
	const MODE_TEST = "MODE_TEST";

	public static $mode = self::MODE_NORMAL;
	
	/** режим отладки */
	public static $isDebug = true;
	
	/** ссылка на текущего партнера */
	public static $partner = null;
	
	/**идентификатор текущего партнера */
	public static $partnerId = null;	
	
	/** действующий юзер (object User)*/
	public static $actor = null;
	
	/**
	 * Коды ошибок 
	 */
	
	// эти коды не использовать в методах, т.к. они предназначены для 
	// контроля правильности запростов
	const API_INTERNAL_ERROR = "API_INTERNAL_ERROR";
	const API_REQUEST_SYNTAX_ERROR = "API_REQUEST_SYNTAX_ERROR";
	const API_USER_AUTHORIZATION_FAILED_ERROR = "API_USER_AUTHORIZATION_FAILED_ERROR";
	const API_UNKNOWN_METHOD_ERROR = "API_UNKNOWN_METHOD";
	const API_MISSED_REQUIRED_PARAMETERS_ERROR = "API_MISSED_REQUIRED_PARAMETERS_ERROR";
	const API_UNKNOWN_PARTNER_ERROR = "API_UNKNOWN_PARTNER_ERROR";
	const API_PARTNER_DISABLED_ERROR = "API_PARTNER_DISABLED_ERROR";
	const API_INCORRECT_SIGNATURE_ERROR = "API_INCORRECT_SIGNATURE_ERROR";
	
	/** параметры, необходимые для работы с API  */
	public static $APIParams = array("partnerId", "method", "sig", "mode");
	
	/** экземпляр объекта */
	private static $instance = null;
	
	/**
	 * типа Singleton, поэтому конструктор приватный
	 */
	private function __construct()
	{		
	}
	
	public function __clone()
	{
		throw new Exception("Clone method not supported");
	}
	
	/**
	 * Возвращает экземпляр класса APIHandler
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
			self::$instance = new APIHandler();
		
		return self::$instance;		
	}
	
	/**
	 * Устанавливаем и проверяем обязательные параметры обработчика
	 * Обязательные входные параметры
	 * $partnerId - ID партнера
	 * method - имя метода
	 * sig - подпись передаваемых данных (может быть NULL)
	 * 
	 * Необязательные (имеют значения по умолчанию)
	 * mode - 0: рабочий режим(по умолчанию), 1: тестовый режим
	 * format - xml(по умолчанию) или json
	 */	
	public function init($partnerId, $method, $mode = APIHandler::MODE_NORMAL)
	{
		if($partnerId == null || $method == null)
			throw new APIException(APIHandler::API_REQUEST_SYNTAX_ERROR, "Request syntax error");
			
		self::$partnerId = $partnerId;
		// установка режимов
		self::$mode = $mode;

		//надо найти партнера	
		$partnerAPI = PartnerAPIFactory::getInstance(Configurator::getSection("partner"));
		self::$partner = $partnerAPI->getParnter($partnerId);
		
		if (self::$partner == null)
			throw new APIException(self::API_UNKNOWN_PARTNER_ERROR, "Unknown partner");
		if(self::$partner->status == "STATUS_DISABLED")
			throw new APIException(self::API_PARTNER_DISABLED_ERROR, "Partner is disabled");
	}
	
	/**
	 * проверка данных и подписи
	 */
	public function checkSignature($sig)
	{
		// Получить все переменные из запроса, исключить из них параметры API,
		// вычислить подпись и сравнить ее
		$res = array();
		if(Request::isGet())
			$res = array_keys($_GET);
		if(Request::isPost())
			$res = array_keys($_POST);

		// подписываем все параметры исключая только sig, а не все основные self::$APIParams
		$diff = array_diff($res, array("sig"));
		
		$tmp = array();
		$signature = "";
		foreach ($diff as $param)
		{
			// т.к. данные фильтруются на сервере, принимаем необработанную строку
			// для вычисления подписи
			$tmp[] = "{$param}=" . Request::getVar($param, null, true);
		}
		
		// сортируем список по возрастанию
		sort($tmp);
		$signature = join("", $tmp);
		
		// генерируем подпись
		$str = self::$partner->partnerId . $signature ;
		$signature = md5($str);

		if($sig !== $signature)
			throw new APIException(self::API_INCORRECT_SIGNATURE_ERROR, "Incorrect signature {$sig} != {$signature}");
	}
	
	/**
	 * выполнение метода
	 * 
	 * Ищет файл, содержащий класс метода по принципу Name + Method.php
	 * Например: класс GetprofilesMethod должен находиться в файле method/GetprofilesMethod.php
	 * 
	 * @return string XML или JSON
	 */
	public function executeMethod($methodName)
	{
		// именование методов: TestMethod.php
		$method = ucfirst(strtolower($methodName)) . "Method";
		$file = "method/{$method}.php";
		
		if (!file_exists($file))
			throw new APIException(self::API_UNKNOWN_METHOD_ERROR, "Unknown method: {$methodName}");
		
		// загрузка класса с методом
		require_once $file;	
		$obj = new $method;
		
		// попробуем установить юзера из контекста
		self::setActor(Context::getActor());		
		
		// проверим, авторизован ли пользователь
		if($obj->requiredAuth && self::$actor == null)
			throw new APIException(self::API_USER_AUTHORIZATION_FAILED_ERROR, "Authorization required");
		
		// подготовим метод к выполнению
		$obj->prepare();
		
		// выполнение метода
		return $obj->execute();
	}
	
	/**
	 * Установка текущего пользователя 
	 */
	public static function setActor($actor)
	{
		self::$actor = $actor;
		Context::setActor($actor);
	}
	
	/**
	 * 
	 * Отсылает заголовки
	 * выводит результат выполнения метода
	 * 
	 * @param mixed $result
	 */
	public static function sendResponse($result)
	{
		$res = array();
		$res["data"] = $result;
		$res["isSuccess"] = true;
		$res["info"] = null;
		
		Request::sendHeaderContentType("utf-8", "text/html");
		$json = json_encode($res);
		echo $json;
	}
	
	/**
	 * Отправка уведомлений
	 * 
	 * @param string $message
	 */
	public static function sendNotice($message)
	{
		$error = array();
		$error["data"] = null;
		$error["isSuccess"] = false;
		$error["info"] = $message;
		
		Request::sendHeaderContentType("utf-8", "text/html");
		echo json_encode($error);
	}
	
	/**
	 * Отправка объекта, описывающего ошибку
	 */
	public static function sendError($code, $message)
	{
		$error = array();
		$error["data"] = null;
		$error["isSuccess"] = false;
		$error["info"] = $message;
		$error["errorCode"] = $code;
		
		Request::sendHeaderContentType("utf-8", "text/html");
		echo json_encode($error);
	}
	
	/**
	 * Запись ошибки в лог-файлы с префиксом
	 * 'api_ИдентификаторПартнера_'
	 */
	public static function writeLog($object)
	{
		$pid = "PartnerId: " . self::$partnerId . " ";
		$obj = Logger::parseEvent($object);
		Logger::$filePrefix = "api_" . self::$partnerId . "_";
		Logger::error($pid . $obj);
	}
}
?>