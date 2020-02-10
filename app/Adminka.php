<?php
/**
 * Прложение для администрирования
 * 
 */
class Adminka extends BaseApplication
{
	private static $instance = null;
	
	private function __construct()
	{}
	
	/**
	 * Функция возвращает экземпляр класса
	 *
	 * @return Application object
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{			
			$class = __CLASS__;
			self::$instance = new $class;
			self::$instance->init();
		}
		return self::$instance;
	}
	
	/**
	 * Нельзя создавать копии
	 */	
	public function __clone()
	{
		throw new Exception("Can't clone singleton object ". __CLASS__);
	}	
	
	/**
	 * В этом методе инициализируются 
	 * все необходимые объекты
	 *
	 * @return void
	 */
	private function init()
	{
		self::setReferef();
		
		require_once '../app/Lib/IntrusionDetector/IntrusionDetector.php';
		new IntrusionDetector(Configurator::getSection("IntrusionDetector"));
		
		self::loadSecurityList();
		
		// всегда обновляем из базы текущего юзера		
		$operator = Context::getActor();
		
		if ($operator != null)
		{
			$om = new OperatorManager();
			$operator = $om->getById($operator->id);
			if ($operator != null)
			{
				// установим текущую роль
				self::$role = self::getCurrentRole();
				
				// и текущего оператора
				Context::setActor($operator);
			}
			else 
			{
				Context::logOff();
			}
		}		
		
	}
	
	/**
	 * Функция загружает список контролов и действий, контролируемых
	 * Security
	 *
	 * @return array
	 */
	public static function loadSecurityList()
	{		
		if (self::$securityList == null)
		{
			$file = APPLICATION_DIR . "/Config/security.php";
			self::$securityList = parse_ini_file($file, true);
		}
		return self::$securityList;
	}

	/**
	 * Функция читает из файла комментарии к шаблонам сервиса и отдает их
	 *
	 * @return array
	 */
	public static function loadTemplateInfoList()
	{
		if (self::$tplList == null)
		{
			$file = APPLICATION_DIR . "/Config/templates.php";
			self::$tplList = parse_ini_file($file, true);
		}
		return self::$tplList;
	}

	/**
	 * Cписок контролов и действий, контролируемых
	 * Security
	 *
	 * @var array
	 */
	private static $securityList = null;

	/**
	 * Список комментариев к шаблонам smarty сервиса
	 *
	 * @var array
	 */
	private static $tplList = null;
	
	/**
	 * Функция возвращает массив основных настроек для текущей лотереи
	 *
	 * @var array
	 */
	private static $basicSettings = null;	
	
	/**
	 * Функция возвращает массив разрешенных действий для роли текущего пользователя
	 *
	 * @var array
	 */
	private static $permissions = null;

	/**
	 * Функция возвращает текущую роль пользователя
	 *
	 * @var Role
	 */
	private static $role = null;
	
	/**
	 * Функция делает проверку допустимости выполнения к-либо действия
	 * текущим пользователем
	 *
	 * @param string $resourceName	Имя контрола или действия. Если не указан,
	 *								то проверяется текущее действие или контрол
	 * 
	 * @return bool
	 * 
	 */
	public static function checkPermissions($resourceName = null)
	{
		$op = Context::getActor();
		if($op == null)
			return true;

		// разрешения для текущей роли
		$perms = self::getCurrentPermissions();

		// это у нас попался администратор - ему можно все
		if($perms === "*")
			return true;
	
		// вычисляем текущий контрол или действие		
		$res = null;
		if ($resourceName == null)
		{
			$action = UIGenerator::getAction();
			$control = UIGenerator::getControl();			
			if ($action == null)
				$res = $control  . "control";
			else 
				$res = $action  . "action";
		}
		else
		{
			$res = $resourceName;
		}

		// найдем имя разрешения для этого действия или контрола
		$permName =  self::searchPermissions($res);
	
		// если текущее действие не задано в конфиге безопасности
		// то считаем, что оно разрешено
		if (count( $permName ) == 0)
			return true;
			
		// пересечение нужных разрешений и разрешений актора
		$intersect = array_intersect($permName, $perms);
			
		// если есть в списке разрешения?
		if(count($intersect) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Функция для поиска имени разрешения по имени контрола или действия
	 *
	 * @param string $name 
	 * @return array
	 */
	private static function searchPermissions($name)
	{
		$list = self::loadSecurityList();
		$res = array();
		foreach ($list as $k => $v)
		{
			if( strstr($v["inherit"], $name) == true)
			{
				$exPermission = explode(",", $v["inherit"]);

				foreach ($exPermission as $exItem)
				{
					if ($exItem == $name)
						$res[] = $k;
				}
			}
		}
		return $res;
	}
	
	/**
	 * Функция возвращает список всех разрешений с описанием.
	 *
	 * @return array
	 */
	public static function getAllPermissions()
	{
		$list = self::loadSecurityList();
		$res = array();
		foreach ($list as $k => $v)
		{
			$res[$v["group"]][] = array('key' => $k, 'value' => $v["description"]);
		}
		
		return $res;
	}


	public static function getAllTemplateInfo()
	{
		$list = self::loadTemplateInfoList();
		$res = array();
		foreach ($list as $k => $v)
			$res[trim(strtolower($k))] = array('key' => $k, 'name' => $v["name"], 'info' => $v["info"]);

		return $res;
	}

	/**
	 * Функция возвращает список разрешений для текущей роли
	 * или знак "*", если роль Администратор
	 * 
	 * @return array
	 */
	public static function getCurrentPermissions()
	{
		$role = self::getCurrentRole();
				
		// разрешения, определенные в роли (они хранятся в виде строки имен контролов и действий, 
		// разделенных запятыми)
		if(self::$permissions == null)
		{
			$perms = strtolower( trim($role->permissions));
			$perms = str_replace(" ", "", $perms);
			if($perms === "*")
				self::$permissions = $perms;
			else
				self::$permissions = explode(",", $perms);
		}	

		return self::$permissions;
	}

	/**
	 * Функция возвращает текущую роль оператора
	 *
	 * @return Role
	 */
	public static function getCurrentRole()
	{
		if(self::$role == null)
		{
			$op = Context::getActor();
			if($op == null)
				return null;
			
			$rm = new RoleManager();
			self::$role = $rm->getById($op->roleId);
			
			if(self::$role == null)
				throw new Exception("Undefined role #{$op->roleId}");
		}
		
		return self::$role;
	}
	
	/**
	 * Функция проверяет, имеет ли пользователь в своей роли
	 * разрешение с таким именем
	 *
	 * @param string $permName название настройки (роли)
	 * @return bool
	 */
	public static function actorHasPermission($permName)
	{
		$perms = self::getCurrentPermissions();

		if($perms === "*")
			return true;
			
		if (in_array($permName, $perms) !== false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Функция отправляет на страницу Access Denied
	 *
	 */
	public static function showAccessDenied()
	{
		// добавить в лог
		Adminka::redirect("mainpage", "Действие запрещено!");		
	}
	
	/**
	 * Функция упрощает редирект на страницы админки
	 * 
	 * @param string $url Адрес , на который происходит редирект
	 *               Если не задан - редирект на предыдущую страницу
	 * 
	 * @param mixed $message Сообщение
	 * 
	 * @return void
	 */
	public static function redirect($url = null, $message = null)
	{
		if($message != null)
			Context::setError($message);
			
		if($url != null)
		{
			Request::redirect("http://".str_replace('www.', '', strtolower($_SERVER['HTTP_HOST'])).Application::normalizePath(Utils::linkTarget($url, null, "adminka/index.php")));
		}

		self::redirectBack($message);
	}
	
	/**
	 * Функция делает редирект на предыдущую страницу
	 *
	 * @param string $message Сообщение
	 */
	public static function redirectBack($message = null)
	{
		if($message)
			Context::setError($message);

		$backURL = "";		
		// костыль для IE7,8 , т.к. некорректно определяется HTTP_REFERER
		if(self::isMSIE())
			$backURL = self::getReferef();
		else
			$backURL = Request::prevUri();
			
		// TODO: надо бы проверить, находиться ли ссылка в пределах админки 

		Request::redirect($backURL);
	}
	

	/**
	 * Функция отдает произвольную куку по указанному имени
	 *
	 * @param $coockieName имя куки
	 * @return занчение куки или null
	 */
	public static function getCookie($coockieName)
	{
		if (isset($_COOKIE[$coockieName]))
			return $_COOKIE[$coockieName];
		else
			return null;
	}

	/**
	 * Функция делает установку произвольной куки по указанному имени
	 *
	 * @param $coockieName имя куки
	 * @param $coockieValue значение куки
	 */
	public static function setCookie($coockieName, $coockieValue)
	{
		setcookie($coockieName, $coockieValue, time()+60*60*24*30, "", "", false, false);
	}	
	
	/**
	 * Функция отправляет содержимое в браузер в виде файла  для загрузки
	 *
	 * @param string $fileName Имя файла
	 * @param string $content Содержимое файла
	 */
	public static function sendFileToBrowser($fileName, $content)
	{
		Request::sendNoCacheHeaders();
		
		// Этот заголовок посылается, чтобы IE6 показывал корректное имя файла, а не имя скрипта
		// выявлено опытным путем
		header("Pragma: public");		
		header("Content-type: application/download");
		header("Content-Disposition: attachment; filename={$fileName}");
		echo $content;
		exit();
	}

	
	public static function prepareForMail($inputString)
	{
		if (!$inputString)
			return null;

		$outputString = str_replace("&quot;", '"', $inputString);
		$outputString = str_replace("&lt;", '<', $outputString);
		$outputString = str_replace("&gt;", '>', $outputString);
		$outputString = str_replace("\r\n", "<br />", $outputString);
		$outputString = str_replace("\n", "<br />", $outputString);

		return $outputString;
	}

}
