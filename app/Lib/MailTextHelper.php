<?php
/**
 * Класс для подготовки писем 
 * Обработка шаблонов
 */
class MailTextHelper
{
	/**
	 * Возвращает содержимое шаблона
	 */
	public static function getTemplate($name)
	{
		$path = Configurator::get("MailTextHelper:path");
		return file_get_contents($path.$name);
	}
	
	/**
	 * Вставляет значения переменных в шаблон
	 * 
	 * @param sting Имя шаблона
	 * @param hash array("vat_name" => "var_value")
	 */
	public static function parse($name, $values = array())
	{
		$content = self::getTemplate($name);
		if (count($values))
		{
			$keys = array();
			$keystmp = array_keys($values);
			foreach ($keystmp as $key)
				$keys[] = "{" . $key . "}";

			$vals = array_values($values);
			return str_replace($keys, $vals, $content);
		}
		else
			return $content;

	}
}
?>