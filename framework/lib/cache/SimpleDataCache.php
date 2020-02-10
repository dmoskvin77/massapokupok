<?php
/**
 * Класс предоставляет функционал для кеширования 
 * данных в файловой системе
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

class SimpleDataCache implements IDataCache
{
	/**
	 * Путь к каталогу, где хранятся файлы кеша
	 * 
	 * @var string
	 */
	private $path;	
	
	/**
	 * Не используется
	 * 
	 * @var null
	 */
	private $cache = null;
	
	/**
	 * ссылка на экземпляр кеша
	 * 
	 * @var SimpleDataCache
	 */
	private static $instance = null;	
	
	/**
	 * Режим отладки
	 * 
	 * @var boolean
	 */
	public $isDebug = true;
	
	/**
	 * Конструктор
	 * 
	 * @return void
	 */
	private function __construct()
	{
		$this->path = APPLICATION_DIR . "/var/cache/";
	}	
	
	/**
	 * Возвращает экземпляр кеша
	 * 
	 * @return SimpleDataCache
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{			
			self::$instance = new SimpleDataCache();
		}
		return self::$instance;		
	}
	
	/**
	 * Получает значение по ключу
	 * 
	 * @param string $key Ключ
	 * 
	 * @return mixed
	 */
	public function get($key)
	{		
		$md5 = md5($key);
		if ($this->isDebug)
			$md5 = $key;
		$files = glob($this->path . "{$md5}_*");
		if (!$files)
			return null;
				
		$f = reset($files);
		if (!$f)
			return null;
		
		$name = explode("_", $f);
		
		$ts = time();
		$cacheTs = $name[1];
		if ($ts > $cacheTs)
		{
			unlink($f);
			return null;
		}
		
		// лочим файл для других потоков
		//$fp = fopen($f, "r");
		//flock($fp, LOCK_EX);		
		$res = file_get_contents($f);
		//flock($fp, LOCK_UN);
		//fclose($fp);		
		
		return unserialize($res);
	}
	
	/**
	 * Пишет файл в каталог кэша.
	 * Файл содержит сериализованные данные
	 * Имя файла формируется по правилу: md5(key)_timestamp
	 * 
	 * @param string $key Ключ
	 * @param mixed $data Данные
	 * @param int $timeout Время жизни кеша в секундах
	 * 
	 * @return void
	 */
	public function set($key, $data, $timeout = 60)
	{		
		$md5 = md5($key);
		if ($this->isDebug)
			$md5 = $key;
		
		$this->remove($md5);
		
		$ts = time();
		if ($timeout)
			$ts = $ts + intval($timeout);
		$file = $this->path . $md5 . "_" . $ts;
		$fp = fopen($file, "wb");
		flock($fp, LOCK_EX);
		fwrite($fp, serialize($data));
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
	/**
	 * Удаляет из кеша данные по ключу
	 * 
	 * @param string $key Ключ
	 * 
	 * @return bool
	 */
	public function remove($key)
	{
		$md5 = md5($key);
		if ($this->isDebug)
			$md5 = $key;
		
		$res = false;
		
		// найти все файлы с таким ключом и удалить
		$files = glob($this->path . "{$md5}_*");
		foreach ($files as $match)
		{
			$res = @unlink($match);
		}
		
		return $res;
	}
	
	/**
	 * Удаляет все данные из кеша
	 * 
	 * @return void
	 */
	public function flush()
	{
		// найти все файлы и удалить
		$files = glob($this->path . "*.*");
		foreach ($files as $match)
		{
			@unlink($match);
		}			
	}
	
	/**
	 * Возвращает путь к каталогу для кеширования
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}
}
?>