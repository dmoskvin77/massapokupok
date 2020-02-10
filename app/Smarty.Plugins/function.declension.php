<?php
/**
* возвращает число и слово в нужном падеже, в зависимости от числа 
* пример вызова: {declension count=$this.count form1=ответ form2=ответа form5=ответов}
* 
* @version $Id: function.declension.php 9 2010-01-19 11:00:28Z afi $
* @param count - количество
* @param form1 - именительный падеж: [1] 'друг'
* @param form2 - родительный падеж: [2] 'друга'
* @param form5 - родительный падеж, множественное число: [5] 'друзей'
* @param merge - возвращать ли число вместе со словом (true или не задано) или только слово (false)
*/
function smarty_function_declension($params, &$smarty)
{
	extract($params);
	
	return Utility::declension($count, $form1, $form2, $form5, isset($merge) ? $merge : true);
}
?>