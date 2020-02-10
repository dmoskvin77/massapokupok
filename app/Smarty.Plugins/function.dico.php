<?php 
/**
 * Using:
 * {dico term="dico test"}
 * {dico term="dico test" lang="ru_RU"}
 * {dico term="dico test" lang="ru_RU" enc="windows-1251"}
 * 
 */
function smarty_function_dico($params, &$smarty)
{
	$term = $params["term"];
	$lang = $params["lang"];
	$encoding = $params["enc"];
	return Dico::term($term);
}
?>