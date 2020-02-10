<?php
/**
 */
function smarty_modifier_checked($value)
{
	if($value)
	{
		return ' checked="checked"';
	}
}
?>