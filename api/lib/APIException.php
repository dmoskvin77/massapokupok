<?php
/**
 * Специальное исключение, генерируемое методами API
 * 
 * @version $Id: ProfileManager.php 29 2008-01-24 13:04:43Z afi $
 * @author Andrey Filippov
 */
class APIException extends Exception 
{
	function __construct($code, $message)
	{
		$this->code = $code;
		$this->message = $message;		
	}
}
?>