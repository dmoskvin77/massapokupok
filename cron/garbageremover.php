<?php
/**
 * Крон для удаления мусора (отработанных данных) из БД
 *
 */

$srvBasePath = '/var/www/massapokupok.ru';

set_time_limit(0);

// требуется полный путь к файлам для запуска в режиме cli
require_once $srvBasePath.'/app/Config/framework.php';
require_once $srvBasePath.'/app/BaseApplication.php';
require_once $srvBasePath.'/app/Enviropment.php';
require_once $srvBasePath.'/app/Lib/Mutex/Mutex.php';

Logger::init(Configurator::getSection("logger"));

$tmp = Configurator::get("application:tempDir");
$mutex = new Mutex("garbageremover", $tmp);

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
	// собственно тут будут запросы - чистильщики базы
	$ts = time();

	// работать будем от имени очереди
	$qsm = new QueueMysqlManager();

	// накидаем голых запросов прямо сюда

	// 5 минут
	$tooOld = time() - 60 * 5;
	$sql1 = "DELETE FROM queueMysql WHERE isFinish = 1 AND dateCreate < {$tooOld}";
	$qsm->executeNonQuery($sql1);

	// 90 дней
	$tooOld = time() - 3600 * 24 * 90;
	$sql2 = "DELETE FROM user WHERE dateLastVisit = 0 AND dateCreate < {$tooOld}";
	$qsm->executeNonQuery($sql2);

	// 10 минут
	$tooOld = time() - 60 * 10;
	$sql3 = "DELETE FROM tocken WHERE tsCreated < {$tooOld}";
	$qsm->executeNonQuery($sql3);

	// 1 день
	$tooOld = time() - 3600 * 24;
	$sql4 = "DELETE FROM csvfile WHERE tsCreated < {$tooOld}";
	$qsm->executeNonQuery($sql4);

	// publicEvent - тоже можно подчищать хвосты за пол года те что были прочитаны

	/* ПЕРЕЗАГРУЗКА:
	TRUNCATE TABLE `orderHead`;
	TRUNCATE TABLE `orderList`;
	TRUNCATE TABLE `pay`;
	TRUNCATE TABLE `product`;
	TRUNCATE TABLE `publicEvent`;
	TRUNCATE TABLE `securityLog`;
	TRUNCATE TABLE `zakupkaHeader`;
	TRUNCATE TABLE `zakupkaLine`;
	*/



}
catch (Exception $e)
{
	echo $e->getMessage()." ".$e->getTraceAsString()."\n";
}

// Освобождаем ресурс
$mutex->release();

?>
