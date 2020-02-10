<?php
/**
 * Интерфейс, который должны имплементировать все контролы
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

interface IControl 
{
	/**
	 * Метод выполняется перед отрисовкой контрола
	 * 
	 * @return void
	 */
	public function preRender();	
	
	/**
	 * Метод выполняется после отрисовкой контрола
	 * 
	 * @return void
	 */
	public function postRender();
	
	/**
	 * Добавляет другой контрол в текущий4
	 * 
	 * @param Control $control Экземпляр контрола
	 * 
	 * @return void
	 */
	public function addControl(Control $control);
	
	/**
	 * Возвращает данные, установленные в контроле
	 * 
	 * @return mixed
	 */
	public function getData();
	
	/**
	 * Добавляет данные в контрол
	 * 
	 * @param string $name Ключ, по которому данные будут доступны в шаблоне
	 * @param mixed $object Данные, передаваемые в шаблон
	 * 
	 * @return void
	 */
	public function addData($name, $object);
}
?>