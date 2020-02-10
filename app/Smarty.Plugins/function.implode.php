<?php
/*
* возвращает элементы массива в виде строки
*
* {implode array=$PhotocommentControl.photo.tags glue=', ' attr='tag'}
* glue - разделитель
* array - массив
* attr - необязательный параметр. если задан, то собираются только эти атрибуты массива:
* используется например для $array=array(array('id'=>1,'tag'=>'tag1'),array('id'=>2,'tag'=>'tag2'))
*
*/
function smarty_function_implode($params, &$smarty)
{
	extract($params);
	
	if($array)
	{
		if($attr)
		{
			$arr = array();
			foreach($array as $a)
			{
				$arr[] = $a[$attr];
			}
			$array = $arr;
		}
		return implode($glue, $array);
	}
	
	return '';
}
?>