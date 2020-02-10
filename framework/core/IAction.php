<?php 
/**
 * Интерфейс для всех действий (Actions)
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

interface IAction
{
	/**
	 * Метод выполняется до вызова execute()
	 * 
	 * @return void
	 */
	public function preExecute();
	
	/**
	 * Метод выполняется после вызова preExecute()
	 * Здесь выполняется основной код действия.
	 *  
	 * @return void
	 */
	public function execute();
	
	/**
	 * Метод выполняется после вызова execute()
	 * 
	 * @return void
	 */
	public function postExecute();
	
	/**
	 * Прямое выполнение действия
	 * 
	 * @param IAction $action Экземпляр действия
	 * 
	 * @return void
	 */
	public static function directExecute(IAction $action);
}
?>