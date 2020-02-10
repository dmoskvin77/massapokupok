<?php
/**
 * Показывает только определенное количество слов в тексте
 *
 * {wordlimit text='asdasd a' limit=50}
 * 
 */
function smarty_function_wordlimit($params, &$smarty)
{
	$limit = $params["limit"];
	$text = $params["text"];
	$tmp = explode(" ", $text);
	$res = preg_split("/[\s]+/", $text);

	$res = array_chunk($res, $limit);
	$res = $res[0];
	return join(" ", $res);
}
?>