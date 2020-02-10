<?php
/**
 * Класс для кэширования сущностей с помощью Memcache
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  Cache
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: Entity.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */

class Memcached implements IDataCache
{
	/**
	 * Экземпляр memcached server connection
	 * 
	 * @var resource
	 */
	private $cache = null;
	
	/**
	 * Режим кеша. Включен\выключен
	 * 
	 * @var boolean
	 */
	public  $isEnabled = false;	
	
	/**
	 * Время жизни закешированных данных
	 * 
	 * @var integer
	 */
	private $lifetime = 60;
	
	/**
	 * ссылка на экземпляр кеша
	 * 
	 * @var Memcached
	 */
	private static $instance = null;
	
	/**
	 * Возвращает ссылку на экземпляр кеша
	 * 
	 * @return Memcached
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{			
			self::$instance = new Memcached();
		}
		return self::$instance;		
	}
	
	/**
	 * Конструктор
	 * 
	 * @return void
	 */
	private function __construct()
	{	
		$options = Configurator::getSection("memcache");
		$this->isEnabled = (bool)$options["enabled"];
		$server = $options["server"];
		$port = $options["port"];
		$persistent = (bool)$options["persistent"];
		$weight = $options["weight"];
		$timeout = $options["timeout"];
		$retry_interval = $options["retry_interval"];
		$this->lifetime = $options["lifetime"];
		
		if ($this->isEnabled)
		{
			try
			{
				$this->cache = new Memcache();
				$res = $this->cache->addServer($server, $port, $persistent, $weight, $timeout, $retry_interval);
			}
			catch (Exception $e)
			{
				throw new Exception("Can't connect to memcache server : " . $e->getMessage());
			}			
		}
	}
	
	/**
	 * Устанавливает значение в кэш
	 * 
	 * @param string $key Ключ
	 * @param mixed $data Данные
	 * @param int $lifetime Время жизни данных в секундах
	 * 
	 * @return boolean
	 */
	public function set($key, $data, $lifetime = null)
	{
		if (!$this->isEnabled)
			return true;
			
		if ($lifetime == null)
			$lifetime = $this->lifetime;
		
		try 
		{
			return $this->cache->set($key, $data, $lifetime);
		}
		catch (Exception $e)
		{
			throw new Exception("Can't set data to cache [{$key}]" . $e->getMessage());
		}		
	}
	
	/**
	 * Удаление данных из кэша
	 * 
	 * @param string $key Ключ
	 * 
	 * @return boolean
	 */
	public function remove($key)
	{
		if (!$this->isEnabled)
			return true;
		
		try 
		{
			return $this->cache->delete($key);
		}
		catch (Exception $e)
		{
			throw new Exception("Can't delete data from cache [{$key}]" . $e->getMessage());
		}
	}
	
	/**
	 * Получение данных из кэша
	 * 
	 * @param string $key Ключ
	 * 
	 * @return mixed
	 */
	public function get($key)
	{
		if (!$this->isEnabled)
			return null;
			
		try 
		{
			$res = $this->cache->get($key);
			if (false === $res)
				return null;
			else 
				return $res;
		}
		catch (Exception $e)
		{
			throw new Exception("Can't get data from cache [{$key}]" . $e->getMessage());
		}
	}
	
	/**
	 * Метод помечает все записи в кэше как "недействительные"
	 * Применяется, например, при удалении всех сущностей из к-либо таблицы.
	 * Т.к. таблицы могут быть связаны, а данные из кэша не удалятся
	 * 
	 * @return bool
	 */
	public function flush()
	{
		if (!$this->isEnabled)
			return true;
			
		try
		{
			return $this->cache->flush();
		}
		catch (Exception $e)
		{
			throw new Exception("Memcache flush unsuccessful : " . $e->getMessage());
		}
	}
}
?>