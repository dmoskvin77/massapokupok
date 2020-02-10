<?php
/**
 * Точка входа в API
 * 
 * 
 * Стандартное взаимодействие приложения с Classnet API производится 
 * путем создания HTTP-запроса (POST или GET) к http://local-sovpok.ru/api/api.php.
 * 
 * @version $Id: api.php 29 2008-01-24 13:04:43Z afi $
 * @author Andrey Filippov
 */

require_once '../app/Config/framework.php';
require_once 'lib/IAPIMethod.php';
require_once 'lib/APIMethod.php';
require_once 'lib/APIException.php';
require_once 'lib/APINoticeException.php';
require_once 'lib/APIHandler.php';

/**
 * Обязательные входные параметры
 * appId - ID приложения
 * method - имя метода
 * sig - подпись передаваемых данных (может быть NULL)
 * 
 * Необязательные (имеют значения по умолчанию)
 * mode - 0: рабочий режим(по умолчанию), 1: тестовый режим
 * 
 */

// Идентификатор партнера - зашит в терминальном приложении. Обязательный параметр.
$partnerId = Request::getVar("partnerId");

// имя вызываемого метода. Обязательный параметр.
$method = Request::getVar("method");

// значение подписи параметров. Обязательный параметр.
$sig = Request::getVar("sig");

// режим работы приложения: 1 - тестовый режим, 0 - обычный режим. Необязательный параметр.
$mode = Request::getInt("mode", 0);

try 
{
	// для всех функций MB выставляем кодировку
	$enc = Configurator::get("application:encoding");
	mb_internal_encoding($enc);
	
	// init logger
	Logger::init(Configurator::getSection("logger"));

	// start application context	
	Context::start("api" . Configurator::get("application:name"));
	
	// режимы
	if(1 === $mode)
		$mode = APIHandler::MODE_TEST;
	if(0 === $mode)
		$mode = APIHandler::MODE_NORMAL;
	
	// обработчик запросов к API
	$handler = APIHandler::getInstance();
	$handler->init($partnerId, $method, $mode);
	$handler->checkSignature($sig);
	
	// запускаем метод
	$res = $handler->executeMethod($method);

	// отправляем ответ
	APIHandler::sendResponse($res);
	exit();
}
catch (APINoticeException $ne)
{
	APIHandler::sendNotice($ne->getMessage());
	exit();
}
catch (APIException $apiex) // Обработка исключения уровня API
{
	APIHandler::sendError($apiex->getCode(), $apiex->getMessage());
	exit();
}
catch (Exception $e) // обработка исключения уровня системы
{
	APIHandler::writeLog($e);
	APIHandler::sendError(APIHandler::API_INTERNAL_ERROR, $e->getMessage());	
	exit();
}
?>