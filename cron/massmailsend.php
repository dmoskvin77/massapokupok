<?php
/**
 * Крон для отправки сообщений на e-mail пользователей
 *
 */

$srvBasePath = '/var/www/massapokupok.ru';

set_time_limit(0);

// требуется полный путь к файлам для запуска в режиме cli
require_once $srvBasePath.'/app/Config/framework.php';
require_once $srvBasePath.'/app/BaseApplication.php';
require_once $srvBasePath.'/app/Lib/Mutex/Mutex.php';

Logger::init(Configurator::getSection("logger"));

$tmp = Configurator::get("application:tempDir");
$mutex = new Mutex("massmailsend", $tmp);

// скрипт уже выполняется
if ($mutex->isAcquired())
{
	echo "Выполение заблокировано другим процессом\n";
	exit();
}

// если не выполняется, лочим для других потоков
$mutex->lock();

try
{
	switch (AllMailManager::send())
	{
		case AllMailManager::STATUS_SENT:
			echo "Почта отправлена\n";
			break;
		case AllMailManager::STATUS_NOTHING:
			echo "Нет писем для отправки\n";
			break;
	}

	// массовая рассылка от орга
	$cnt = BroadcastManager::send();
	echo "cnt: {$cnt}\n";


}
catch (Exception $e)
{
	echo $e->getMessage()." ".$e->getTraceAsString()."\n";
}

// Освобождаем ресурс
$mutex->release();

?>
