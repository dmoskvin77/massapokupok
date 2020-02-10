<?php
/**
 * Менеджер для управление журналом потенциальных опасностей
 */
class SecurityLogManager extends BaseEntityManager 
{
	/**
	 * Функция делает подсчет попыток залогиниться
	 * При превышении - пишем в лог 
	 *
	 * @return bool True, если присутствует подбор пароля
	 */
	public static function detectPasswordBrutforce()
	{
		if(!Configurator::get("securitylog:enabled"))
			return false;		

		$count = intval(Session::get("__detectPasswordBrutforce"));	
		$count++;
		if(Configurator::get("securitylog:passwordBrutforceLimit") < $count)
		{
			$exclude = array("password");
			self::write(SecurityLog::TYPE_PASSWORD, null, $exclude);	
			return true;		
		}
		else 
		{
			Session::set("__detectPasswordBrutforce", $count);
			return false;
		}
	}
	
	/**
	 * Функция обнуляет счетчик попыток логина
	 *
	 */
	public static function clearPasswordBrutforce()
	{
		if (!Configurator::get("securitylog:enabled"))
			return true;
		
		Session::clear("__detectPasswordBrutforce");
		return true;
	}
	
	
	/**
	 * Функция записи в журнал
	 *
	 * @param string $type Тип действия
	 * @param string $info Дополнительная информация
	 * @param array $excludeParams Параметры HTTP запроса, которые не нужно записывать в лог
	 * @return bool
	 */
	public static function write($type, $info = null, $excludeParams = null)
	{
		if(!Configurator::get("securitylog:enabled"))
			return false;
		
		$log = new SecurityLog();
		$actor = Context::getActor();
		
		$userId = null;
		$operatorId = null;
		$username = null;
		
		if ($actor != null)
		{
			if($actor instanceof Operator) 
			{
				$operatorId = $actor->id;
				$username = $actor->login;
			}
			if($actor instanceof User) 
			{
				$userId = $actor->id;
				$username = $actor->login;
			}	
		}
		
		// определим в какой части приложения происходило действие
		$uri = $_SERVER['REQUEST_URI'];
		if(strpos($uri, "/adminka") === false)
			$log->section = SecurityLog::SECTION_FRONT;
		else 
			$log->section = SecurityLog::SECTION_ADMINKA;
		
		// данные запроса
		$request = "Request URI : {$uri}"  . PHP_EOL;
		$request .= "Query string :" . $_SERVER['QUERY_STRING'] . PHP_EOL;

		// Убираем параметры, которые мы хотим скрыть
		if($excludeParams != null)
		{
			foreach ($excludeParams as $ex)
				unset($_REQUEST[$ex]);
		}
		
		$request .= self::requestToString($_REQUEST);
				
		$log->userId = $userId;
		$log->operId = $operatorId;
		$log->ip = Request::getIp();
		$log->longIp = ip2long($log->ip);
		$log->request = $request;
		$log->actionType = $type;
		$log->info = $info;
		
		$slm = new self();
		$slm->save($log);
		return true;
	}

	/**
	 * Функция преобразует запрос в строку
	 *
	 * @param array $array
	 * @return string
	 */

	private static function requestToString($array)
	{
		$res = "";
		foreach ($array as $k => $v)
		{
			if(is_array($v))
			{
				self::requestToString($v);
			}
			else 
			{
				$k = htmlspecialchars($k);
				$v = htmlspecialchars($v);
				$res .= "[{$k}] => {$v}" . PHP_EOL;
			}
		}
		return $res;
	}
	
	/**
	 * Функция записывает данные из детектора инъекций (callback)
	 *
	 * @param mixed $data
	 */
	public static function writeIntrusionDetect($data)
	{
		$message = "";
		foreach ($data as $item)
			$message .= $item["info"] . "\n";
			
		self::write(SecurityLog::TYPE_INJECTION, htmlspecialchars( $message ));
	}
	
	/**
	 * Функция возвращает записи журнала
	 *
	 * @param int $page текущая страница просмотра
	 * @param int $perPage показывать записей на странице
	 */
	public function getList($page = 1, $perPage = 50)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `securitylog`";
				
		$lim = new SQLCondition();
		$lim->offset =  ($page - 1) * $perPage;
		$lim->rows = $perPage;
		$lim->orderBy = "ts DESC";
		
		$sql .= $lim->buildSQL();
		
		// данные записей
		$records = $this->getBySQL($sql);
		
		// число записей
		$count =  $this->getOneByAnySQL( "SELECT FOUND_ROWS() as cnt" );
		$count = $count['cnt'];
		
		$res["data"] = $records;
		$res["count"] = $count;
		return $res;
	}
	
	/**
	 * Функция возвращает инфу из лога по потенциальным опасностям (Сами записи и их кол-во)
	 *
	 * @param string $section Раздел, к которому относятся записи (SECTION_ADMINKA, SECTION_FRONT, SECTION_SERVER)
	 * @param string $type Тип сообщений( service, app )
	 * @param date $begin Дата начала периода
	 * @param date $end Дата окончания периода
	 * @param int $page текущая страница отображения
	 * @param int $perPage записей на странице
	 * @return mixed
	 */
	public function getByFilter($section, $type, $begin = null, $end = null, $page = 1, $perPage = 200)
	{
		$begin = $this->formatDateTimeIn($begin);
		$end = $this->formatDateTimeIn($end);
		
		$actionType = "";
		
		//все что сработало на правила из intrusionRules.xml + mod_evasive
		if($type == "service")
			$actionType = " 'TYPE_TOO_MANY_REQUESTS', 'TYPE_INJECTION', 'TYPE_NO_ACCESS' ";
			
		//подбор паролей, попытка доступа оператора к закрытому разделу
		if($type == "app")
			$actionType = " 'TYPE_PASSWORD', 'TYPE_ACCESS_DENIED' ";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `securitylog` 
				WHERE section = '{$section}' 
				AND actionType IN ({$actionType}) 
				AND DATE( ts ) between DATE('{$begin}') AND DATE('{$end}')";
			
		$lim = new SQLCondition();
		$lim->offset =  ($page - 1) * $perPage;
		$lim->rows = $perPage;
		$lim->orderBy = "ts DESC";
		
		$sql .= $lim->buildSQL();
		
		// данные записей
		$records = $this->getBySQL($sql);
		// число записей
		$count =  $this->getOneByAnySQL( "SELECT FOUND_ROWS() as cnt" );
		$count = $count['cnt'];
		
		$res["data"] = $records;
		$res["count"] = $count;
		return $res;
	}
	
	/**
	 * Функция делает запись в лог события DOS атаки
	 *
	 * @return void
	 */
	public static function writeEvasiveEvent($ip, $info)
	{
		if(!Configurator::get("securitylog:enabled"))
			return false;
		
		$log = new SecurityLog();
		$log->ip = $ip;
		$log->longIp = ip2long($ip);
		$log->actionType = SecurityLog::TYPE_TOO_MANY_REQUESTS;
		$log->section = SecurityLog::SECTION_SERVER;
		$log->info = $info;
		
		$slm = new self();
		$slm->save($log);	
	}
	
}
