<?php 
function smarty_function_currentuser($params, &$smarty)
{
	$user = Context::getActor();
	if ($user)
	{
		return $user->__toString();
	}
}
?>