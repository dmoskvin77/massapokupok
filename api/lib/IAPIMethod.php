<?php
/**
 * Интерфейс для методов API
 * @version $Id: ProfileManager.php 29 2008-01-24 13:04:43Z afi $
 * @author Andrey Filippov
 */

interface IAPIMethod
{
	/**
	 * Регистрирует обязательный параметр
	 * @param string $name Имя параметра в HTTP запросе
	 * @return void
	 */
	public function registerRequiredParameter($name);
	
	/**
	 * Регистрирует необязательный параметр
	 * @param string $name Имя параметра в HTTP запросе
	 * @return void
	 */	
	public function registerOptionalParameter($name);
	
	/**
	 * В этом методе производятся все действия по выполнению метода
	 * Он должен вернуть строку XML или JSON
	 * 
	 * @return string
	 */	
	public function execute();
	
	/**
	 * Подготовка метода к выполнению
	 * Проверка необходимых параметров в HTTP запросе
	 * 
	 * @return void
	 */	
	public function prepare();
}
?>