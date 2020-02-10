<?php 
/**
 * Слой бизнес-логики приложения
 * 
 */
class Enviropment extends BaseApplication
{
	private static $instance = null;
	
	private function __construct()
	{}
	
	/**
	 * Функция возвращает экземпляр класса
	 * @return Shop object
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
	 * Флаг того, что GUID корзины определён ...
	 *
	 * @var unknown_type
	 */
	private static $isBasketCookieAlreadySet = false;
	
	/**
	 * В этом методе инициализируются 
	 * все необходимые объекты
	 *
	 * @return void
	 */
	private function init()
	{
		// проверим есть ли текстовый файлик о том, что на сайте идёт обновление
		if (file_exists('mounting.txt'))
		{
			$URL = "mounting.html";
			header ("Location: {$URL}");
			
		}
		else
		{
			// костыль для IE7,8
			// т.к. он некорректно определяет HTTP_REFERER
			self::setReferef();
	
			require_once 'app/Lib/IntrusionDetector/IntrusionDetector.php';
			$detector = new IntrusionDetector(Configurator::getSection("IntrusionDetector"));
			
			// выставляем куку для проверки, включены ли куки в браузере
			self::setCheckUpCookie();
	
			$user = Context::getActor();
			
			// всегда обновляем из базы текущего юзера
			if ($user != null)
			{
				$successLogin = false;

				$um = new UserManager();
				$newUser = $um->getById($user->id);
				if ($newUser != null)
				{
					Context::setActor($newUser);
					$successLogin = true;
				}

				if ($successLogin == false)
					Context::logOff();

			}
			
			// если все же юзер есть в контексте, то обновим время его последней активности
			self::setLastActivity();

		}

	}
	
	/**
	 * Метод для принудительной переинициализации приложения
	 *
	 * @return void
	 */
	public function reInit()
	{
		$this->init();
	}
	
	/**
	 * Функция упрощает редирект на страницы магазина
	 * 
	 * @param string $url Адрес , на который происходит редирект
	 *               Если не задан - редирект на предыдущую страницу
	 * @param mixed $message Сообщение
	 * @return void
	 */
	public static function redirect($url = null, $message = null)
	{
		if($message != null)
			Context::setError($message);
			
		if($url != null)
			Request::redirect(Utility::toSlashUrl(Application::normalizePath(Utils::linkTarget($url))));

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

		Request::redirect($backURL);
	}

	/**
	 * Функция проверяет, включены ли куки у клиента
	 * 
	 * Делается это так: при каждой инициализации приложения
	 * выставляем куку с именем "checkup" = 1 на время сессии
	 * 
	 * в этом методе проверяется, если этой куки нет, то они выключены
	 * 
	 * @return bool
	 */
	public static function isCookieEnabled()
	{
		$respond = self::getCookie('checkup');
		return $respond !== null;
	}

	/**
	 * Функция устанавливает тестовую куку на год
	 */
	public static function setCheckUpCookie()
	{
		if (!headers_sent())
			setcookie("checkup", 1, time() + 60*60*24*356, "/");
	}
	
	/**
	 * Функция устанавливает последнее время активности пользователя
	 * для последующего котроля активности до таймаута
	 */
	public static function setLastActivity()
	{
		Session::set("__time", time());	
	}

	/**
	 * Функция возвращает последнее время активности пользователя
	 *
	 * @return string
	 */	
	public static function getLastActivity()
	{
		return Session::get("__time");	
	}	
	
	/**
	 * Функция отдает произвольную куку
	 *  
	 * @param string $coockieName название куки
	 * @return занчение куки или null
	 */
	public static function getCookie($cookieName)
	{
		return isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : null;
	}
		
	/**
	 * Функция установливает произвольную куку
	 * 
	 * @param string $cookieName название куки
	 * @param string $cookieValue значение куки
	 * 
	 */
	public static function setCookie($cookieName, $cookieValue)
	{
		if (!headers_sent())
		{
			if ($cookieName == "basketGUID")
				setcookie($cookieName, $cookieValue, 0, "/");
			else
				setcookie($cookieName, $cookieValue, time()+60*60*24*30, "/");
		}
	}	
	
	/**
	 * Функция очищает куку
	 * 
	 * @param string $cookieName название куки
	 */
	public static function unsetCookie($cookieName)
	{
		if (!headers_sent())
			setcookie($cookieName, null, time() - 3600, "/");
	}

	public static function unsetAllCookies()
	{
		if (!headers_sent())
		{
			foreach ($_COOKIE as $ind => $val)
				self::unsetCookie($ind);
		}

	}

	/**
	 * Функция устанавливает\возвращает идентификатор корзины в куки
	 *
	 * @return string
	 */
	public static function getBasketGUID()
	{
		$getBaskedGUID = self::getCookie("basketGUID");
		if ($getBaskedGUID == null)
		{
			$getBaskedGUID = Utils::getGUID();
			if(!self::$isBasketCookieAlreadySet)
			{
				if (!headers_sent())
					self::setCookie("basketGUID", $getBaskedGUID);

				self::$isBasketCookieAlreadySet = true;
			}
		}
		return $getBaskedGUID;
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


	// авторизация на форуме vbulleting
	public static function vBulletinLogin($ownerSiteId, $ownerOrgId, $host, $user, $pass)
	{
		if ($ownerSiteId == 7) {
			$host = "http://roman:patriot@{$host}";
		}
		else {
			$host = "http://{$host}";
		}

		$forumUrl = "{$host}/forum/login.php";
		if ($ownerOrgId)
			$forumUrl = "{$host}/forum{$ownerSiteId}_{$ownerOrgId}/login.php";
		else
		{
			if ($ownerSiteId > 1)
				$forumUrl = "{$host}/forum{$ownerSiteId}/login.php";
		}

		$data = "req_username={$user}&req_password={$pass}&save_pass=1";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $forumUrl);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_TIMEOUT, '10');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($ch);

		// echo $content; exit;

		preg_match_all('|Set-Cookie: (.*);|U', $content, $results);
		if (isset($results[1]) && is_array($results[1]) && count($results[1]))
		{
			foreach($results[1] AS $oneCookieKey => $oneCookieVal)
			{
				$pareArray = explode('=', $oneCookieVal);
				if (count($pareArray) == 2)
				{
					$cookieKey = $pareArray[0];
					$cookieVal = $pareArray[1];
					Enviropment::setCookie($cookieKey, $cookieVal);
				}
			}
		}

		curl_close($ch);
	}

}
