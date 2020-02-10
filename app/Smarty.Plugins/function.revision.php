<?php
/*
* Ревизия
*
* {revision}
*/
function smarty_function_revision($params, &$smarty)
{
	$isDebug = Configurator::get("application:debug");
	if($isDebug)
	{
		$revision = BaseApplication::getRevision();
		return " (Версия #{$revision})";
	}	
}
?>