<?php
/**
 * Класс для обработки фильтров
 *
 */
class FilterInput
{
	/* список сообщений от фильтров */
	private static $messages = null;
	
	/**
	 * Добавляет фильтр на проверку
	 * 
	 * @param IFIlterInput $filter Объект фильтр
	 */
	public static function add(IFIlter $filter)
	{
		$res = $filter->isValid();
		if(!$res)
			self::$messages[] = $filter->getMessage();
			
		return $filter->getValue();
	}
	
	/**
	 * Возвращает сообщения от фильтров
	 * @return string
	 */
	public static function getMessages()
	{
		return self::$messages;
	}
	
	/**
	 * Статус проверки всех фильтров
	 * Если список сообщений пуст - то true
	 * 
	 * @return bool
	 */
	public static function isValid()
	{
		return count(self::$messages) == 0;
	}
	
	/**
	 * Добавляет сообщение в список
	 * (Фильтр становится невалидным)
	 * 
	 * @param strint $message Сообщение
	 */
	public static function addMessage($message)
	{
		self::$messages[] = $message;
	}
}
?>