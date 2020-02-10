<?php
/**
 * Выполняет контроль загрузки JS и CSS файлов
 * с тем, чтобы одинаковые файлы не подгружались несколько раз
 *
 * @example 
 * {loadscript file='js/example.js' type='js'}
 * {loadscript file='js/example.css' type='css'}
 */
function smarty_function_loadscript($params, &$smarty)
{
	$res = BaseApplication::loadScript($params['file']);
	if ($res === false)
		return "";

	$type = $params['type'];
	$fileName = $params['file'];
	$fileName = Application::normalizePath($fileName);
	$media = $params['media'] ? "media=\"" . $params['media'] . "\" " : "";
	$revision = BaseApplication::getRevision();
	
	if($type == "js")
		return "<script language=\"javascript\" type=\"text/javascript\" src=\"{$fileName}?{$revision}\"></script>";
	if($type == "css")
		return "<link type=\"text/css\" href=\"{$fileName}?{$revision}\" rel=\"stylesheet\" {$media}/>";
	if($type == "swf")
		return "{$fileName}?{$revision}";
}
?>