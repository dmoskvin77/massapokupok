<?php
/**
 * Обработчик файла конфигурации в формате JSON
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

class JSONConfigurator implements IConfiguratorParser
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
		$content = file_get_contents($configFile);
		$this->config = json_decode($content, true);
	}	
	
	/**
	 * @param string $param
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
	 * 
	 */
	public function getSection()
	{
		
	}

	/**
	 * @param string $paramName
	 */
	public function getArray($paramName)
	{
		$res = $this->get($paramName);
		if ($res == null)
			return array();
		else 
			return $res;
	}


	/**
	 * 
	 */
	public function getOptions ()
	{
		return $this->config;
	}

	
}
?>