<?php
/**
 * Обработчик INI файла конфигурации
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

class IniConfigurator implements IConfiguratorParser
{
	
	/**
	 * Содержимое конфигурационного файла в 
	 * виде массива 
	 * 
	 * @var array
	 */
	private $config = null;
	
	/**
	 * Конструктор
	 * 
	 * @param string $configFile Путь к файлу конфигурации
	 * 
	 * @return void
	 */
	public function __construct($configFile)
	{
		if (!file_exists($configFile))
			throw new Exception("Config file '{$configFile}' does not exists.");

		$this->config = parse_ini_file($configFile, true);
	}
	
	/**
	 * Возвращает массив с настройками
	 * 
	 * @return array
	 */
	public function getOptions()
	{
		return $this->config;
	}
	
	/**
	 * Возвращает значение параметра, определенного в файле конфигурации
	 * 
	 * @param string $param Имя параметра в формате section:option 
	 * 
	 * @example $dbPassword = Configurator::get("first_connection:password");
	 * 
	 * @return mixed
	 */
	public function get($param)
	{
		$tmp = explode(":", $param);

		$val = @$this->config[$tmp[0]][$tmp[1]];
		if (isset($val))
			return $val;
		else 
			throw new Exception("Undefined config option : {$tmp[0]}:{$tmp[1]}");		
	}

	/**
	 * Возвращает настройки для определенной секции
	 * в виде массива
	 * 
	 * @param string $sectionName Имя секции
	 * 
	 * @return array
	 */
	public function getSection($sectionName)
	{
		$val = @$this->config[$sectionName];
		
		if ($val !== null)
			return $val;
		else
			throw new Exception("Undefined config section : {$sectionName}");		
	}

	/**
	 * Возвращает значение одного параметра в виде массива.
	 * Параметр должен содержать элементы, разделенные символом $delimiter
	 * 
	 * @param string $paramName Имя параметра
	 * 
	 * @example Запись в INI файле: paramName = "val1,val2,val3"
	 * 
	 * @return array
	 */
	public function getArray($paramName)
	{
		$value = $this->get($paramName);
		if ($value == null)
			return array();

		$delimiter = ",";
		$out = explode($delimiter, $value);
		return $out;
	}
}
?>