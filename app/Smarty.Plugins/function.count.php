<?php
/**
 * возвращает количество элементов в массиве
 * {count array=$EditpostControl.post.attaches} 
 */
function smarty_function_count($params, &$smarty)
{	
	$array = $params['array'];
	if($array)
		return count($array);
	return 0;
}
?>