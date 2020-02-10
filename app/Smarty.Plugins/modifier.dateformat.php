<?php
/**
 * Выводит дату в определенном формате
 */

function smarty_modifier_dateformat($string, $format = "d-m-Y")
{
	return Utility::dateFormat($string, $format);
}
?>