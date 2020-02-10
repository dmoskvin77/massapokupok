<?php 
/**
 * Самый главный файл ФО
 *
 */

require_once "app/Config/framework.php";
require_once "app/BaseApplication.php";
require_once "app/Enviropment.php";

try 
{
	// init logger
	Logger::init(Configurator::getSection("logger"));

	// start application context	
	Context::start(Configurator::get("application:name"));
	
	// для всех функций MB выставляем кодировку
	$enc = Configurator::get("application:encoding");
	mb_internal_encoding($enc);	

	// start application instance
	$app = Enviropment::getInstance();
	
	// get UI generator instance
	$gen = UIGenerator::getInstance();
	
	// по умолчанию показываем главную страницу CMS
	$gen->defaultControl = "page";
	
	// execute action or render control
	$action = UIGenerator::getAction();
	$control = UIGenerator::getControl();

	// execute action or render control
	$gen->exec($action, $control);

	// send headers
	if (Configurator::get("application:nocache"))
	{
		Request::sendNoCacheHeaders();
	}

	// отправляем заголовки, чтобы контент был в нужной кодировке
	Request::sendHeaderContentType(Configurator::get("application:encoding"));
	
	// print HTMLs
	$gen->display();

	// закрыть все соединения с базой
	Enviropment::closeConnection();
}
catch (Exception $e)
{
	if (!headers_sent())
		Request::sendHeaderContentType();

	echo "Exception : ". $e->getMessage();
	echo "<br/>";	
	
	if (Configurator::get("application:debug"))
	{
		print_r($e->getTrace());
	}
	
	Logger::error($e);
}
?>
