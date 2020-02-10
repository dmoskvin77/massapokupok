<?php
/**
 * Интерфейс для всех классов, предоставляющих функции кеширования данных
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

interface IDataCache
{
	/**
	 * Получение данных из кэша
	 * 
	 * @param string $key Ключ
	 * 
	 * @return mixed
	 */
	public function get($key);
	
	/**
	 * Устанавливает значение в кэш
	 * 
	 * @param string $key Ключ
	 * @param mixed $data Данные
	 * @param int $lifetime Время жизни данных в секундах
	 * 
	 * @return boolean
	 */
	public function set($key, $data, $lifetime = null);
	
	/**
	 * Удаление данных из кэша
	 * 
	 * @param string $key Ключ
	 * 
	 * @return boolean
	 */	
	public function remove($key);
	
	/**
	 * Метод помечает все записи в кэше как "недействительные"
	 * Применяется, например, при удалении всех сущностей из к-либо таблицы.
	 * Т.к. таблицы могут быть связаны, а данные из кэша не удалятся
	 * 
	 * @return bool
	 */	
	public function flush();
	
}
?>