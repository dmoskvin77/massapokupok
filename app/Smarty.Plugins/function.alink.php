<?php 
/**
 * Тег для формирования ссылки в разделе администрирования
 */
function smarty_function_alink($params, &$smarty)
{
	//extract($params);
	$target = $params['show'];
	unset($params['show']);

	$action = $params['do'];
	unset($params['do']);
	
	if ($target && $action)
		return "Link -> Only one parameter allowed";
	
	if ($target)
		return Application::normalizePath(Utils::linkTarget($target, $params, "adminka/index.php"));
	if ($action)
		return Application::normalizePath( Utils::linkAction($action, $params, "adminka/index.php"));
	
	return "No link parameters!";
}
?>