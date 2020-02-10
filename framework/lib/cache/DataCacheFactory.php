<?php
/**
 * Класс реализует паттерн Factory 
 * Применяется для создания кэшей
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

class DataCacheFactory
{
	
	/**
	 * Создает кэш сущностей
	 *
	 * @param string $type Тип кэша сущностей
	 */
	function factory($className)
	{
		return new $className;
	}
}

?>