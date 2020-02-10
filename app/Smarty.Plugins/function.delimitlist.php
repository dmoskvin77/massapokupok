<?php
/**
 */
function smarty_function_delimitlist($params, &$smarty)
{
	$delimiter = ", ";
	if (isset($params['delim']))
		$delimiter = $params['delim'];
	
	$value = $params['array'];
	if ($value === null)
		return null;
	
	return implode($delimiter, $value);
}
?>