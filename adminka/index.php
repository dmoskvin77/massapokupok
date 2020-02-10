<?php 
/**
 * Главный файл админки
 * 
 */

require_once "../app/Config/framework.php";
require_once "../app/BaseApplication.php";
require_once "../app/Adminka.php";

try 
{
	// init logger
	Logger::init(Configurator::getSection("logger"));
	
	// start application context	
	Context::start("back" . Configurator::get("application:name"));
	
	// для всех функций MB выставляем кодировку
	$enc = Configurator::get("application:encoding");
	mb_internal_encoding($enc);	

	// start application instance
	$app = Adminka::getInstance();
	
	// get UI generator instance
	$gen = UIGenerator::getInstance();

	// execute action or render control
	$action = UIGenerator::getAction();
	$control = UIGenerator::getControl();
	
	if ($action == null && $control == null)
		$control = "adminkaindex";
	
	// execute action or render control
	$gen->exec($action, $control);

	// send headers
	if (Configurator::get("application:nocache"))
	{
		Request::sendNoCacheHeaders();
	}

	// print HTMLs
	$gen->display();
	
	// закрыть все соединения с базой
	Adminka::closeConnection();
}
catch (Exception $e)
{
	echo "Exception : ". $e->getMessage();
	echo "<br/>";	
	
	if (Configurator::get("application:debug"))
	{
		print_r($e->getTrace());
	}
	
	Logger::error($e);
}
?>