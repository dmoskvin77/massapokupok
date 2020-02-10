<?php 
/**
 * Тег для формирования ссылки
 */
function smarty_function_link($params, &$smarty)
{
	$action = $params["do"];
	unset($params["do"]);
	
	$target = $params["show"];
	unset($params["show"]);
	
	$fullUrl = $params["fullUrl"];
	unset($params["fullUrl"]);

	$fullUrlHttps = $params["fullUrlHttps"];
	unset($params["fullUrlHttps"]);
	
	if(!$action && !$target || $action && $target)
		return "Link: one parameter expected";
	
	if($action)
		return Application::normalizePath( Utils::linkAction($action, $params));
	
	foreach($params ? $params : array() as $key => $value)
		$target .= "/$key/$value";
	
	if($fullUrl)
	{
		$url = "http://".str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
		return "$url/$target";
	}

	if($fullUrlHttps)
	{
		$url = "https://".str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
		return "$url/$target";
	}
	
	return 'http://'.$_SERVER['HTTP_HOST'].'/'.$target;
}
?>