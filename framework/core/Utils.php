<?php 
/**
 * Класс содержит вспомогательные методы
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

class Utils 
{
	
	/**
	 * Генерирует ссылку на страницу
	 * 
	 * @param string $target Наименование контрола
	 * @param array $params Список параметров hashtable ('id' => 1)
	 * @param string $handler Имя файла скрипта-обработчика, по умолчанию index.php
	 * @param string $mode Режим формирования ссылки: 
	 * 				 url - обычный вид ссылки, rewrite - ЧПУ вид
	 * 
	 * @return string
	 * */
	public static function linkTarget($target = null, $params = null, $handler = null, $mode = "url")
	{
		if (null == $handler)
			$handler = "index.php";
			
		switch ($mode) 
		{
			case "url":
					$par = "";
					if ($params)
					{
						$par = "&amp;";
						foreach ($params as $k => $v)
						{
							$para[] = "$k=$v";
						}
						$par .= implode("&amp;", $para);
					}
					return "/{$handler}?show=" . $target . $par;
				break;
				
			case "rewrite":
				return "/show/{$target}/";
				break;
					
			default:
				throw new Exception(__METHOD__ . ": Undefined mode '{$mode}'");
		}
	}
	
	/**
	 * Генерирует ссылку на действие
	 * 
	 * @param string $action Наименование действия
	 * @param array $params Список параметров hashtable ('id' => 1)
	 * @param string $handler Имя файла скрипта-обработчика, по умолчанию index.php
	 * @param string $mode Режим формирования ссылки: 
	 * 				 url - обычный вид ссылки, rewrite - ЧПУ вид
	 * 
	 * @return string
	 * */
	public static function linkAction($action = null, $params = null, $handler = null, $mode = "url")
	{
		if ("url" == $mode)
		{
			if (null == $handler)
				$handler = "index.php";
				
			$par = "";
			if ($params)
			{
				$par = "&amp;";
				foreach ($params as $k => $v)
				{
					$para[] = "$k=$v";
				}
				$par .= implode("&amp;", $para);
			}
			return "/{$handler}?do=" . $action . $par;
		}
		if ("rewrite" == $mode)
			return "/do/{$action}/";
	}
	
	/**
	 * Генерирует GUID
	 * 
	 * @return string
	 * */
	public static function getGUID()
	{
		return md5(uniqid(rand(0,1000))."-".time());
	}
	
	/**
	 * Правильный подсчет количества символов в строке
	 * с учетом кодировки
	 * 
	 * @param string $string Строка
	 * @param string $encoding кодировка
	 * 
	 * @return string
	 */
	public static function strlen($string, $encoding = "utf-8")
	{
		return mb_strlen($string, $encoding);
	}
	
	/**
	 * Конвертирует строку в указанную кодировку
	 * 
	 * @param string $string Строка
	 * @param string $encoding кодировка
	 * 
	 * @return string
	 */
	public static function convertEncoding($string, $encoding = "utf-8")
	{
		return mb_convert_encoding($string, $encoding);
	}

	// получить имя текущего контрола
	public static function getCurrenControlName()
	{
		$curPage = null;
		if (isset($_SERVER["REQUEST_URI"]))
		{
			$curPage = strtolower($_SERVER["REQUEST_URI"]);
			$cpArray = explode('/', $curPage);
			if (count($cpArray) > 1 && strpos($curPage, '=') === false)
				$curPage = $cpArray[1];
			else
			{
				// возможно у нах вид запроса GET
				$cpArray1 = explode('?', $curPage);
				if (isset($cpArray1[1]))
				{
					$cpArray2 = explode('=', $cpArray1[1]);
					if (isset($cpArray2[1]))
					{
						$cpArray2[1] .= '&_';
						$cpArray3 = explode('&', $cpArray2[1]);
						if (isset($cpArray3[0]))
							$curPage = $cpArray3[0];
					}
				}
			}
		}

		return $curPage;
	}

}
?>