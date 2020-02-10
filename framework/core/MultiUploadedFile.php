<?php
/**
 * Класс для обработки нескольких одновременно загружаемых 
 * файлов.
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

class MultiUploadedFile
{
	/**
	 * Возвращает коллекцию объектов UploadedFile
	 * 
	 * @param string $fieldName Имя поля загрузки файлов
	 * 
	 * @return array of UploadedFile
	 */
	public static function getFiles($fieldName)
	{
		$files = null;
		if (isset($_FILES[$fieldName]))
			$files = $_FILES[$fieldName];
		else 
			return array();
			//throw new Exception("Undefined field name '{$fieldName}'");		
		
		$prepare = array();
		$out = array();
				
		for ($i = 0; $i < count($files["name"]); $i++)
		{
			if ($files["size"][$i] == 0)
				continue;
				
			$tmp["name"] = $files["name"][$i];
			$tmp["type"] = $files["type"][$i];
			$tmp["tmp_name"] = $files["tmp_name"][$i];
			$tmp["error"] = $files["error"][$i];
			$tmp["size"] = $files["size"][$i];
									
			$out[] = new UploadedFile($tmp , true);
		}

		return $out;
				
	}	
}
?>