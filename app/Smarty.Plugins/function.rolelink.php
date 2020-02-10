<?php
/**
 * Рисует ссылку в зависомости от роли
 * 
 * @example {rolelink action=deletephoto id=2 text="Удалить" [ownid=12] [css="del"] }
 */
function smarty_function_rolelink($params, &$smarty)
{
	$target = $params['show'];
	unset($params['show']);

	$action = $params['do'];
	unset($params['do']);

	$text = $params['text'] == null ? "Текст ссылки" : $params['text'];
	unset($params['text']);
	$css = $params['css'];
	unset($params['css']);
	$ownid = $params['ownid'];
	unset($params['ownid']);

	if ($target && $action)
		return "Link -> Only one parameter allowed";
	
	$actor = Context::getActor();
	
	// нельзя определить действие, если актора нет
	if(null == $actor)
		return null;
	
	// если проверяем на владельца
	if ($ownid != null)
	{
		if( $actor->id == $ownid ) //если владелец
			$res = true;
		else 
			$res = BaseApplication::checkRole($action, $actor->role);
	}
	else 
	{
		$res = MyApplication::checkRole($action, $actor->role);
	}
		
	// проверяем , показывать ли ссылку
	if($res)
	{
		$href = "";
		if ($target)
			$href =  Application::normalizePath( Utils::linkTarget($target, $params) );
		if ($action)
			$href =  Application::normalizePath( Utils::linkAction($action, $params) );
		
		return "<a class='{$css}' href='{$href}'>{$text}</a>";
	}
}
?>