<?php 
/**
 */
function smarty_function_ischecked($params, &$smarty)
{
	$val = $params["value"];
	if ($val === true)
		return ' checked="checked"';
	else 
		return "";
}
?>