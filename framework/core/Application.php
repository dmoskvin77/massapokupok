<?php
/**
 * Базовый класс приложения
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  Core
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: Entity.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */

abstract class Application 
{
	
	
	/**
	 * Коллекция соединений к БД
	 * 
	 * @var array
	 */
	private static $connections = array();
	
	/**
	 * Список уже загруженных скриптов
	 * 
	 * @var array
	 */
	private static $scripts = array();
	
	/**
	 * Возвращает соединение по его имени
	 * 
	 * @param string $name Имя соединения (см. секцию database в конфигурации)
	 * 
	 * @return resource Соединение к БД
	 * */
	public static function getConnection($name)
	{
		if (!@key_exists($name, self::$connections))
		{
			$adapter = DBFactory::factory(Configurator::get($name . ":driver"));
			$adapter->setConfig(Configurator::getSection($name));
			self::$connections[$name] = $adapter;
		}
		
		return self::$connections[$name];
	}
	
	/**
	 * Disconnect from MySQL adapters
	 * 
	 * @return void
	 */
	public static function closeConnection()
	{
		foreach (self::$connections as $name => $adapter)
		{
			$adapter->close();
		}
	}
	
	/**
	 * Нормализует путь к файлам.
	 * Добавляет к пути первый слеш и каталог,
	 * определенный в конфигураторе "application:basePath"
	 * 
	 * @param string $path Путь к файлу
	 * 
	 * @return string Нормализованный путь к файлу
	 */
	public static function normalizePath($path)
	{
		$basePath = Configurator::get("application:basePath");		
		$basePath = "/" . trim($basePath, "\/");			
		$path = "/" . trim($path, "\/");
		return "/" . trim($basePath . $path, "\/");
	}
	

	
	/**
	 * Метод осуществляет контроль загрузки JS и CSS на страницу для
	 * того, чтобы в страницу не подключались несколько одинаковых файлов.
	 * Если файл уже загружен, то возвращает false
	 * 
	 * @param $fileName путь к файлу
	 * 
	 * @return bool
	 */
	public static function loadScript($fileName)
	{
		$hash = md5($fileName);

		if ( !in_array($hash, self::$scripts) )
		{
			self::$scripts[] = $hash;
			return true;
		}
		else
		{
			return false;
		}		
	}
}
?>