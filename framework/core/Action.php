<?php 
/**
 * Абстрактный класс. Предок всех действий (actions)
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

abstract class Action implements IAction
{
	/**
	 * Выполняется перед выполнением действия
	 * 
	 * @return void
	 * */
	public function preExecute()
	{
	}
	
	/**
	 * Выполнение действия
	 * @return void
	 * */
	//abstract function execute();
	
	/**
	 * Выполняется после выполнения действия
	 * 
	 * @return void
	 * */
	public function postExecute()
	{
	}
	
	/**
	 * Выполняет действие непосредственно
	 * 
	 * @param IAction $action Экземпляр действия
	 * 
	 * @return void
	 * */
	public static function directExecute(IAction $action)
	{
		$action->preExecute();
		$action->execute();
		$action->postExecute();
	}
}
?>