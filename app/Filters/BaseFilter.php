<?php
/**
 * Базовый класс для всех фильтров
 */

abstract class BaseFilter implements IFIlter  
{
	/* Сообщение от фильтра */
	protected $message = null;
	
	/* Значение  */
	protected $value = null;
	
	/**
	 * Устанавливает значение фильтра
	 * Используется для возврата методом FilterInput::add
	 * 
	 *
	 * @param mixed $val
	 * @see FilterInput::add
	 */
	protected function setValue($val)
	{
		$this->value = $val;
	}
	
	/**
	 * Возвращает значение
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Возвращает статус проверки фильтра
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return $this->message == null;
	}
	
	/**
	 * Возвращает сообщение от фильтра
	 *
	 * @return unknown
	 */
	public function getMessage()
	{
		return $this->message;
	}	
}
?>