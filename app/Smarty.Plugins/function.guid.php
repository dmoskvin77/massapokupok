<?php
/*
* гуид
*
* {guid}
*/
function smarty_function_guid($params, &$smarty)
{
	return Utils::getGUID();
}
?>