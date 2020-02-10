<?php
/**
 * Подстановка компонентов в шаблон
 */
function smarty_function_uigenerator($params, &$smarty)
{
	if (array_key_exists('do', $params) && $params['do'] == null)
		return "uigenerator tag: to specify action name.";
		
	if (array_key_exists('show', $params) && $params['show'] == null)
		return "uigenerator tag: to specify target name.";
		
	if (!isset($params['do']) && !isset($params['show']))
		return "uigenerator tag: to specify target or action name.";
		
	$force = false;
	if (isset($params['force']) && "true" == $params['force'])
		$force = true;
	
	extract($params);
	$ui = UIGenerator::getInstance();
	$ui->forceViewComponent = $force;	
	$ui->exec($action, $target);
	return $ui->display();
}
?>