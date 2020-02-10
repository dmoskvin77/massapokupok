<?php 
/**
 * Класс реализует паттерн Factory 
 * Применяется для создания адаптеров БД
 * 
 * PHP version 5
 * 
 * @category Framework
 * @package  DataBase
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: Entity.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
 */

class DBFactory
{
	/**
	 * Создает экземпляр IDBAdapter
	 * 
	 * @param string $type Тип адаптера
	 * 
	 * @return IDBAdapter
	 */
	public static function factory($type)
	{
		$class = $type . "Adapter";
		return new $class();	
	}
}
?>