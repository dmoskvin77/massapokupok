<?php 
/**
 * Dictionary class
 * 
 * @version $Id: Dico.php 25 2008-01-21 11:17:47Z afi $
 * @author Andrey Filippov
 *  
 */
class Dico 
{
	private static $dico = null;
	
	public static function init($language = "en_EN", $encoding = "windows-1251")
	{
		$file = APPLICATION_I18N_DIR . "/" . $language . "/" . $encoding . ".php";
		if(!file_exists($file))
			throw new Exception("Can't find i18n file : $file");
		require_once $file;
	}
	
	/**
	 * Возвращает термин для заданного идентификатора термина
	 * @param $termID Идентификатор термина
	 * @desc return Term
	 * @return string
	 */

	public static function term($termID = '', $debug = false)
	{
		if (self::$dico == null)
			throw new Exception("Please, initialize Dictionary");
		if (isset( self::$dico[$termID]))
		{
			if( $debug != true ) 
				return self::$dico[$termID];
			else
				return $termID.self::$dico[$termID];
		}
		else
		{
			//return "Term [$termID] was not found";
			// return original phrase if term not found
			return $termID;
		}
	}

}
?>