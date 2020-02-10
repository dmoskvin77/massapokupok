<?php
/**
 * Крон для выполнения задач, поставленных в очередь
 * Тяжёлый крон, требующий много ресурсов
 *
 */

$srvBasePath = '/home/host1320388/43pokupki.ru/htdocs/www/';

set_time_limit(0);

// требуется полный путь к файлам для запуска в режиме cli
require_once $srvBasePath.'/app/Config/framework.php';
require_once $srvBasePath.'/app/BaseApplication.php';
require_once $srvBasePath.'/app/Enviropment.php';
require_once $srvBasePath.'/app/Lib/Mutex/Mutex.php';

Logger::init(Configurator::getSection("logger"));

$tmp = Configurator::get("application:tempDir");
$mutex = new Mutex("nightwork", $tmp);

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
	// получаем задачи из очереди по 5 штук
	$qsm = new QueueMysqlManager();

	$qsm->startTransaction();
	try
	{
        // вычислить ежесуточные olap данные по сайтам
        $osm = new OwnerSiteManager();
        $ownerSitesList = $osm->getAll();
        if (count($ownerSitesList)) {
            foreach ($ownerSitesList AS $ownerSite) {
                $qsm->savePlaceTask("calcolapsite_" . $ownerSite->id, null, null, null);
            }
        }

        // вычислить ежесуточные olap данные по оргам
        $oom = new OwnerOrgManager();
        $ownerOrgList = $oom->getAll();
        if (count($ownerOrgList)) {
            foreach ($ownerOrgList AS $ownerOrg) {
                $qsm->savePlaceTask("calcolaporg_" . $ownerOrg->id, null, null, null);
            }
        }

        // ставим в очередь задачи, которые надо выполнить ночью

        $qsm->commitTransaction();
	}
	catch (Exception $e)
	{
		$qsm->rollbackTransaction();
		Logger::error($e->getMessage());
	}

}
catch (Exception $e)
{
	echo $e->getMessage()." ".$e->getTraceAsString()."\n";
}

// Освобождаем ресурс
$mutex->release();

?>
