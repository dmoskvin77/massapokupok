<?php
/**
 * Тег, формирующий ЧПУ
 */

function smarty_function_rwlink($params, &$smarty)
{
	$target = $params['show'];
	unset($params['show']);

	$action = $params['do'];
	unset($params['do']);
	
	if ($target && $action)
		return "Link -> Only one parameter allowed";

	// action не обрабатываем	
	if ($action)
		return Utils::linkAction($action, $params);
	
	// а вот target - да
	foreach($params ? $params : array() as $key => $value)
		$target .= "/$key/$value";
		
	return "/page/$target";
}
?>