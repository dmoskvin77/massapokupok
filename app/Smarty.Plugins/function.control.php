<?php
/**
 * вставляет темплат в контрол.
 * для тех случаев, когда в контроле уже имеются данные и их нужно отобразить каким-то темплатом.
 * например, на многих страницах нужно отобразить спикок юзеров. 
 * этот список можно отобразить с помощью темплата например таким образом:
 * {control name=Userlist users=$ClassmatesControl.classmates} 
 */
function smarty_function_control($params, &$smarty)
{	
	extract($params);
	
	// разбиваю в колонки по 5 (или что там придёт в $chunk)
	if($data && $chunk)
	{
		$data = array_chunk($data, $chunk, true);
	}
	
	foreach($params as $key => $value)
	{
		$smarty->assign($key, ${$key});
	}

	$name = $ignorePrefix ? "$name.html" : "{$name}Control.html";
	
	$templatesDir = Configurator::get("framework:directory.templates");
	$smarty->display($templatesDir . "/$name");
}
?>