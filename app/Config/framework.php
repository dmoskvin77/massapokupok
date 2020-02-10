<?php
#
# Подключение фреймворка
#
error_reporting(E_ALL);

// Эти константы нужно определить обязательно
define("FRAMEWORK_ROOT_DIR", realpath(dirname(__FILE__).'/../../framework'));
define("APPLICATION_DIR", realpath(dirname(__FILE__) . '/..'));
define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);

// подключим либу, которая позволяет преобразовывать нотисы (E_NOTICE),
// предупреждения (E_WARNING) и т. д. в исключения PHP.
require_once APPLICATION_DIR . '/Lib/Exceptionizer/Exceptionizer.php';
$php_exceptionizer = new PHP_Exceptionizer(E_ALL);

// Проверка установки значений необходимых констант
require_once FRAMEWORK_ROOT_DIR . '/bootstrap.php';

// подключение конфигуратора
require_once FRAMEWORK_ROOT_DIR . "/core/IConfiguratorParser.php";
require_once FRAMEWORK_ROOT_DIR . "/core/Configurator.php";
require_once FRAMEWORK_ROOT_DIR . "/lib/configurator/IniConfigurator.php";

// Подключаем нужную конфигурацию приложения
Configurator::init(new IniConfigurator(APPLICATION_DIR . "/Config/develop.php"));

// подключим библиотеку, которая позволяет преобразовывать E_NOTICE,
// E_WARNING и т. д. в исключения PHP.
// Это необязательно, но помогает при разработке
if (Configurator::get("application:debug"))
{
	require_once APPLICATION_DIR . '/Lib/Exceptionizer/Exceptionizer.php';
	$php_exceptionizer = new PHP_Exceptionizer(E_ALL);
}

// переопределяем автозагрузку файлов, если необходимо
function __autoload($class)
{
	try
	{
		Configurator::autoload($class);
	}
	catch (Exception $e)
	{
		// если включен режим отладки - показываем сообщение в браузере
		if (Configurator::get("application:debug"))
		{
			echo $e->getMessage();
		}
		else
		{
			// иначе, 404 ошибку
			// Request::send404();
			echo $e->getMessage();
		}
		exit();
	}
}
?>