<?php
/**
* возвращает часть строки, разделяя её по символу "_"
*
* {$user->id|getStringPart:2}
*/
function smarty_modifier_getstringpart($string, $pos)
{
	$string = explode("_", $string);

	if (isset($string[$pos]))
		return $string[$pos];
}
?>