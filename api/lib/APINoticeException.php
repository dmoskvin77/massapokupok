<?php
/**
 * Специальное исключение. Применяется для пересылки
 * уведомлений. 
 * 
 * @version $Id: ProfileManager.php 29 2008-01-24 13:04:43Z afi $
 * @author Andrey Filippov
 */
class APINoticeException extends Exception 
{
	function __construct($message)
	{
		$this->code = 0;
		$this->message = $message;		
	}
}
?>