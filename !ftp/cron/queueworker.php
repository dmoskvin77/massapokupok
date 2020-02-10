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
$mutex = new Mutex("queueworker", $tmp);

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
	$tasks = $qsm->getSomeNewTasks(5);
	if (count($tasks))
	{
		foreach ($tasks AS $oneTask)
		{
			// отметка о старте задачи
			$qsm->setStartDate($oneTask->id);

			// начинаем работу над задачей
			$boolRez = false;

			$qsm->startTransaction();
			try
			{
				// перевод закупки в статус "Стоп"
				if ($oneTask->taskName == "stopzakupka")
					$boolRez = $qsm->stopZakupkaWorker($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId, $oneTask->headName, $oneTask->dateCreate);

				// перевод в статус "Проверена"
				if ($oneTask->taskName == "checkzakupka")
					$boolRez = $qsm->checkZakupkaWorker($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId, $oneTask->headName, $oneTask->dateCreate);

				// перевод закупки в статус "Доставлена"
				if ($oneTask->taskName == "dlvrzakupka")
					$boolRez = $qsm->dlvrZakupkaWorker($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId, $oneTask->headName, $oneTask->dateCreate);

				// в ряду поменялась цена
				if ($oneTask->taskName == "changezlineprice")
					$boolRez = $qsm->orgChangePriceInZline($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId, $oneTask->headName, $oneTask->otherData);

				// добавилась встреча
				if ($oneTask->taskName == "addmeeting")
					$boolRez = $qsm->orgAddMeeting($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId, $oneTask->headName, $oneTask->meetId);

				// пересчет суммы набранности закупки
				if ($oneTask->taskName == "calcnarate")
					$boolRez = $qsm->orgCalcNarate($oneTask->headId);

				// рассылка от организатора участникам закупки
				if ($oneTask->taskName == "orgbroadcast")
					$boolRez = $qsm->orgBroadcast($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId, $oneTask->headName, $oneTask->otherData);

				// пересчет всех заказов в связи с групповой установкой наличия
				if ($oneTask->taskName == "rebuildzakupkaorders")
					$boolRez = $qsm->orgRebuildOrders($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId);

				// копирование рядов при клонировании закупки (длительная процедура)
				if ($oneTask->taskName == "copyzlines")
					$boolRez = $qsm->orgCloneZlines($oneTask->headId, $oneTask->otherData);

				// уведомление подписавшихся на выкуп
				if ($oneTask->taskName == "vikupzhstart")
					$boolRez = $qsm->vikupZHStart($oneTask->fromUserId, $oneTask->fromNickName, $oneTask->headId, $oneTask->headName, $oneTask->otherData);

				// вычисление olap данных по сайту-владельцу
                if (strpos($oneTask->taskName, 'calcolapsite_') !== false) {
                    $taskTitleInfo = explode('_', $oneTask->taskName);
                    $ownerSiteId = $taskTitleInfo[1];
                    $boolRez = $qsm->calcOlapSiteOwner($ownerSiteId);
                }

                // вычисление olap данных по оргу-владельцу
                if (strpos($oneTask->taskName, 'calcolaporg_') !== false) {
                    $taskTitleInfo = explode('_', $oneTask->taskName);
                    $ownerOrgId = $taskTitleInfo[1];
                    $boolRez = $qsm->calcOlapOrgOwner($ownerOrgId);
                }

                // вычислить ежесуточные olap данные по сайтам
                if (strpos($oneTask->taskName, 'calccommsite_') !== false) {
                    $taskTitleInfo = explode('_', $oneTask->taskName);
                    $ownerSiteId = $taskTitleInfo[1];
                    $boolRez = $qsm->calcCommSiteOwner($ownerSiteId);
                }

                // вычислить ежесуточные olap данные по оргам
                if (strpos($oneTask->taskName, 'calccommorg_') !== false) {
                    $taskTitleInfo = explode('_', $oneTask->taskName);
                    $ownerOrgId = $taskTitleInfo[1];
                    $boolRez = $qsm->calcCommOrgOwner($ownerOrgId);
                }


				$qsm->commitTransaction();
			}
			catch (Exception $e)
			{
				$qsm->rollbackTransaction();
				Logger::error($e->getMessage());
			}

			// отметка об окончании задачи
			$qsm->setFinishDate($oneTask->id, $boolRez);

		}
	}

}
catch (Exception $e)
{
	echo $e->getMessage()." ".$e->getTraceAsString()."\n";
}

// Освобождаем ресурс
$mutex->release();

?>
