<?php
/**
* возвращает длину строки
*
* {$alb->name|length}
*/
function smarty_modifier_length($string)
{
	if(!$string)
		return 0;

	return mb_strlen($string, 'utf-8');
}
?>