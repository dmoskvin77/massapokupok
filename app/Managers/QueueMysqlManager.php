<?php
/**
 * Менеджер очереди задач
 */

class QueueMysqlManager extends BaseEntityManager
{
	// получить N записей в порядке естественного возрастания id
	public function getSomeNewTasks($limit = 5)
	{
		$sql = new SQLCondition("dateStart IS NULL AND isError = 0");
		$sql->offset = 0;
		$sql->rows = $limit;
		return $this->get($sql);
	}

	// быстрая установка даты старта задачи
	public function setStartDate($id)
	{
		$sql = "UPDATE queueMysql SET dateStart = ".time()." WHERE id = ".$id;
		$this->executeNonQuery($sql);
		return true;
	}

	// быстрая установка даты окончания задачи
	public function setFinishDate($id, $boolRez)
	{
		if ($boolRez)
			$sql = "UPDATE queueMysql SET dateFinish = ".time().", isFinish = 1 WHERE id = ".$id;
		else
			$sql = "UPDATE queueMysql SET dateFinish = ".time().", isError = 1, isFinish = 1 WHERE id = ".$id;

		// сразу удаляем эти записи, т.к. у нас есть уникальность по ним
		// а сообщение об ощибке лучше отправлять на почту (мониторинг)
		// $sql = "DELETE FROM queueMysql WHERE id = ".$id;
		$this->executeNonQuery($sql);

		return true;
	}


	// пересчет суммы набранности закупки
	public function orgCalcNarate($headId)
	{
		$zhm = new ZakupkaHeaderManager();
		return $zhm->orgCalcNarate($headId);
	}

	// орг назначает встречу
	public function orgAddMeeting($fromUserId, $fromNickName, $headId, $headName, $meetId)
	{
		// получим объект "встреча"
		$mtm = new MeetingManager();
		$meetObj = $mtm->getById($meetId);
		if (!$meetObj)
			return false;

		// получить учасников закупки, у кого сумма заказа больше нуля
		// добавить им публичное уведомление о назначенной встрече
		$ohm = new OrderHeadManager();
		$orderList = $ohm->getByHeadId($meetObj->headId, true);
		if (!count($orderList))
			return true;

		$userIds = array();
		foreach ($orderList AS $oneOrder)
			$userIds[] = $oneOrder->userId;

		// тут вызовем функцию, которая определит host по id закупки
		$gotHost = $this->getHostByHearId($meetObj->headId);
		if (!$gotHost)
			return true;

		$host = "http://".$gotHost['host'];
		$orgLink = $host."/vieworg/id/".$fromUserId;
		$viewLink = $host."/viewcollection/id/".$headId;

		$startDate = date("d.m.Y H:i", $meetObj->startTs);
		$finishDate = date("d.m.Y H:i", $meetObj->finishTs);

		$message = "Организатор [url={$orgLink}]{$fromNickName}[/url] по закупке [url={$viewLink}]{$headName}[/url] назначил встречу для раздачи заказов, которая пройдёт c [b]{$startDate}[/b] по [b]{$finishDate}[/b].
		[b]Сообщение огранизатора:[/b]
		&laquo;[i]{$meetObj->message}[/i]&raquo;";

		$pem = new PublicEventManager();
		$pem->buildInsertOnZakupka($meetObj->ownerSiteId, $meetObj->ownerOrgId, $userIds, $fromUserId, $fromNickName, $headId, $headName, $message, $meetObj->id);

		return true;

	}

	// орг меняет цену на товар ряда
	public function orgChangePriceInZline($fromUserId, $fromNickName, $headId, $headName, $otherData)
	{
		if (!$otherData)
			return true;

		$additionalDataArray = unserialize($otherData);
		if (!count($additionalDataArray))
			return true;

		if (!isset($additionalDataArray['zlId']) || !isset($additionalDataArray['oldPrice']) || !isset($additionalDataArray['newPrice']) || !isset($additionalDataArray['prodName']))
			return true;

		$zlId = $additionalDataArray['zlId'];
		$oldPrice = $additionalDataArray['oldPrice'];
		$newPrice = $additionalDataArray['newPrice'];
		$prodName = $additionalDataArray['prodName'];

		$olm = new OrderListManager();
		$orderList = $olm->getByZlId($zlId);
		if (!count($orderList))
			return true;

		if (is_object($zlId))
			$zlId = $zlId->id;

		// поменяем в заказах цену одним безопасным запросом
		$olm->orgChangeZlPrice($zlId, $newPrice);

		// получим список заказов, из них выделим людей, кому сделать рассылку
		// публичного уведомления об изменении цены
		// сделаем rebuild корзин всех этих людей
		$ohm = new OrderHeadManager();
		$rebuildedUserIds = array();
		foreach($orderList AS $oneOrder)
		{
			if (!in_array($oneOrder->userId, $rebuildedUserIds))
			{
				$ohm->rebuildOrder($oneOrder->userId, $headId);
				$rebuildedUserIds[] = $oneOrder->userId;
			}
		}

		// проверим, т.к. внутри самой функции отправки сообщений проверки нет
		if (!count($rebuildedUserIds))
			return true;

		// подготовить и отправить публичное сообщение
		$gotData = $this->getHostByHearId($headId);
		if (!$gotData)
			return true;

		$host = "http://".$gotData['host'];
		$orgLink = $host."/vieworg/id/".$fromUserId;
		$viewLink = $host."/viewcollection/id/".$headId;

		$message = "Организатор [url={$orgLink}]{$fromNickName}[/url] в закупке [url={$viewLink}]{$headName}[/url] поменял цену на товар {$prodName} с {$oldPrice} на {$newPrice}.";

		$pem = new PublicEventManager();
		$pem->buildInsertOnZakupka($gotData['ownerSiteId'], $gotData['ownerOrgId'], $rebuildedUserIds, $fromUserId, $fromNickName, $headId, $headName, $message);

		return true;
	}


	// воркер "Стоп" закупки
	// надо расставить публичные уведомления
	// сделать рассылку на мыло
	public function stopZakupkaWorker($fromUserId, $fromNickName, $headId, $headName, $dateCreate)
	{
		$shortTitle = "Закупка переведена в СТОП";
		$mailTpl = "stopzakupka.html";
		$message = "СТОП. Заказы больше не принимаются. Ожидайте подтверждения наличия Ваших заказов, готовтесь к оплате.";

		// собственно сама раскладка сообщений
		return $this->zakupkaWorker($fromUserId, $fromNickName, $headId, $headName, $dateCreate, $shortTitle, $mailTpl, $message);
	}

	// закупка "Проверена"
	public function checkZakupkaWorker($fromUserId, $fromNickName, $headId, $headName, $dateCreate)
	{
		$shortTitle = "Закупка переведена в статус ПРОВЕРЕНА";
		$mailTpl = "checkzakupka.html";
		$message = "статус ПРОВЕРЕНА. Информация о наличии у поставщика заказанных Вами товаров записана организатором в Вашу корзину. Пожалуйста оплатите Ваши заказы как можно быстрее, т.к. от этого будет зависеть скорость отправки всей закупки от поставщика организатору.";

		// собственно сама раскладка сообщений
		return $this->zakupkaWorker($fromUserId, $fromNickName, $headId, $headName, $dateCreate, $shortTitle, $mailTpl, $message);
	}

	// закупка доставлена
	public function dlvrZakupkaWorker($fromUserId, $fromNickName, $headId, $headName, $dateCreate)
	{
		$shortTitle = "Закупка ДОСТАВЛЕНА!";
		$mailTpl = "deliveredzakupka.html";
		$message = "статус ДОСТАВЛЕНА. Ожидайте сообщений о встречах с организатором, на которых Вам будут переданы Ваши заказы. Не забывайте оплатить сумму, выставленную за доставку.";

		// собственно сама раскладка сообщений
		return $this->zakupkaWorker($fromUserId, $fromNickName, $headId, $headName, $dateCreate, $shortTitle, $mailTpl, $message);
	}

	// собственно сама раскладка сообщений
	public function zakupkaWorker($fromUserId, $fromNickName, $headId, $headName, $dateCreate, $shortTitle, $mailTpl, $message)
	{
		// берём список всех участников закупки
		$ohm = new OrderHeadManager();
		$userIds = $ohm->getUserIds($headId);
		if (!count($userIds))
			return true;

		$um = new UserManager();
		$users = $um->getByIds($userIds);

		// ставим рассылать сообщения на почту
		$gotHost = $this->getHostByHearId($headId);
		if (!$gotHost)
			return true;

		$host = "http://".$gotHost['host'];
		$signMessage = SettingsManager::getValue($gotHost['ownerSiteId'], $gotHost['ownerOrgId'], 'mail_sign');
		$fromEmail = SettingsManager::getValue($gotHost['ownerSiteId'], $gotHost['ownerOrgId'], 'mail_from');
		$fromName = SettingsManager::getValue($gotHost['ownerSiteId'], $gotHost['ownerOrgId'], 'mail_fromName');

		$header = Enviropment::prepareForMail(MailTextHelper::parse("header.html"));
		$footer = Enviropment::prepareForMail(MailTextHelper::parse("footer.html"));

		$viewLink = $host."/viewcollection/id/".$headId;
		$orgLink = $host."/vieworg/id/".$fromUserId;

		foreach ($users AS $oneUser)
		{
			if ($oneUser->firstName || $oneUser->secondName)
				$oneUser->firstName = ', '.$oneUser->firstName;

			if ($oneUser->secondName)
				$oneUser->secondName = ' '.$oneUser->secondName;

			if ($oneUser->lastName)
				$oneUser->lastName = ' '.$oneUser->lastName;

			$vars = array(
				"MESSAGE_TITLE" => $shortTitle,
				"ZAK_TITLE" => $headName,
				"USER_LOGIN" => $oneUser->nickName,
				"FIRST_NAME" => $oneUser->firstName,
				"SECOND_NAME" => $oneUser->secondName,
				"LAST_NAME" => $oneUser->lastName,
				"VIEW_LINK" => $viewLink,
				"MESSAGE_SIGN" => $signMessage,
				"HOST" => $host
			);

			$body = Enviropment::prepareForMail(MailTextHelper::parse($mailTpl, $vars));

			// ставим письмо в очередь
			allMailManager::addMailMessage($shortTitle, $header.$body.$footer, $oneUser->login, $fromEmail, $fromName);

		}

		$zhm = new ZakupkaHeaderManager();
		$zhObj = $zhm->getById($headId);
		if (!$zhObj)
			return true;

		// теперь надо создать публичные уведомления в личку
		$message = "Организатор [url={$orgLink}]{$fromNickName}[/url] перевел закупку [url={$viewLink}]{$headName}[/url] в ".$message;
		$pem = new PublicEventManager();
		$pem->buildInsertOnZakupka($zhObj->ownerSiteId, $zhObj->ownerOrgId, $userIds, $fromUserId, $fromNickName, $headId, $headName, $message);

		return true;
	}

	// пересчет всех заказов в закупке, например в связи со сменой наличия товаров
	public function orgRebuildOrders($fromUserId, $fromNickName, $headId)
	{
		$ohm = new OrderHeadManager();
		$orders = $ohm->getByHeadId($headId);
		if (count($orders))
		{
			foreach ($orders AS $oneOrder)
				$ohm->rebuildOrder($oneOrder->ownerSiteId, $oneOrder->ownerOrgId, $oneOrder->userId, $headId);

		}

		return true;
	}

	// рассылка организатором из закупки
	public function orgBroadcast($fromUserId, $fromNickName, $headId, $headName, $otherData)
	{
		$message = @base64_decode($otherData);
		if (!$message)
			return false;

		// сначала надо получить список заинтересованных
		$ohm = new OrderHeadManager();
		$userIds = $ohm->getUserIds($headId);
		if (!count($userIds))
			return true;

		$gotHost = $this->getHostByHearId($headId);
		if (!$gotHost)
			return true;

		$host = "http://".$gotHost['host'];
		$viewLink = $host."/viewcollection/id/".$headId;
		$orgLink = $host."/vieworg/id/".$fromUserId;

		// теперь надо создать публичные уведомления в личку
		$message = "Организатор [url={$orgLink}]{$fromNickName}[/url] по закупке [url={$viewLink}]{$headName}[/url] сообщает:
".$message;

		$zhm = new ZakupkaHeaderManager();
		$zhObj = $zhm->getById($headId);
		if (!$zhObj)
			return true;

		$pem = new PublicEventManager();
		$pem->buildInsertOnZakupka($zhObj->ownerSiteId, $zhObj->ownerOrgId, $userIds, $fromUserId, $fromNickName, $headId, $headName, $message);

		return true;
	}


	// безопасная постановка задачи в очередь INSERT IGNORE
	public function savePlaceTask($taskName, $fromUserId, $fromNickName, $headId, $headName = null, $meetId = null, $otherData = null, $dateCreate = null)
	{
		if (!$dateCreate)
			$dateCreate = time();

		if (!$fromUserId)
			$fromUserId = "NULL";

		if (!$fromNickName)
			$fromNickName = "NULL";
		else
			$fromNickName = "'{$fromNickName}'";

		if (!$headName)
			$headName = "NULL";
		else
			$headName = "'{$headName}'";

		if (!$meetId)
			$meetId = "NULL";

		if (!$otherData)
			$otherData = "NULL";
		else
			$otherData = "'{$otherData}'";

		if (!$headId)
			$headId = "NULL";
		else
			$headId = intval($headId);

		$sql = "INSERT IGNORE INTO queueMysql (taskName, fromUserId, fromNickName, headId, headName, meetId, otherData, dateCreate) VALUES ('{$taskName}', {$fromUserId}, {$fromNickName}, {$headId}, {$headName}, {$meetId}, {$otherData}, {$dateCreate})";
		$this->executeNonQuery($sql);

		return true;
	}


	// копирование строк закупки при клонировании
	public function orgCloneZlines($headid, $newHeadId)
	{
		$newHeadId = intval($newHeadId);
		if (!$newHeadId)
			return false;

		$zlm = new ZakupkaLineManager();
		$zakLines = $zlm->getByHeadId($headid);

		if (count($zakLines))
		{
			foreach ($zakLines AS $zakLineObj)
			{
				$newZakLineObj = new ZakupkaLine();

				$newZakLineObj->headId = $newHeadId;

				if ($zakLineObj->productId)
					$newZakLineObj->productId = $zakLineObj->productId;

				if ($zakLineObj->prodLink)
					$newZakLineObj->prodLink = $zakLineObj->prodLink;

				if ($zakLineObj->orgId)
					$newZakLineObj->orgId = $zakLineObj->orgId;

				if ($zakLineObj->wholePrice)
					$newZakLineObj->wholePrice = $zakLineObj->wholePrice;

				if ($zakLineObj->oldWholePrice)
					$newZakLineObj->oldWholePrice = $zakLineObj->oldWholePrice;

				if ($zakLineObj->finalPrice)
					$newZakLineObj->finalPrice = $zakLineObj->finalPrice;

				$newZakLineObj->dateCreate = time();
				$newZakLineObj->dateUpdate = time();

				if ($zakLineObj->minValue)
					$newZakLineObj->minValue = $zakLineObj->minValue;

				if ($zakLineObj->minName)
					$newZakLineObj->minName = $zakLineObj->minName;

				if ($zakLineObj->isGrow)
					$newZakLineObj->isGrow = $zakLineObj->isGrow;

				if ($zakLineObj->shouldClose)
					$newZakLineObj->shouldClose = $zakLineObj->shouldClose;

				if ($zakLineObj->ownerSiteId)
					$newZakLineObj->ownerSiteId = $zakLineObj->ownerSiteId;

				if ($zakLineObj->ownerOrgId)
					$newZakLineObj->ownerOrgId = $zakLineObj->ownerOrgId;

				$newZakLineObj->rowNumbers = 1;

				if ($zakLineObj->sizes)
					$newZakLineObj->sizes = $zakLineObj->sizes;

				if ($zakLineObj->sizesComplete)
					$newZakLineObj->sizesComplete = $zakLineObj->sizesComplete;

				$newZakLineObj->status = ZakupkaLine::STATUS_ACTIVE;
				if ($zakLineObj->status == ZakupkaLine::STATUS_HIDDEN)
					$newZakLineObj->status = ZakupkaLine::STATUS_HIDDEN;

				$zlm->save($newZakLineObj);
			}

		}

		return true;
	}


	// уведомление подписавшихся на выкуп
	public function vikupZHStart($fromUserId, $fromNickName, $headId, $headName, $otherData)
	{
		$vikupId = intval($otherData);
		if (!$vikupId)
			return false;

		// в паблик и на мыло

		// сначала надо получить список заинтересованных
		$vsm = new VikupSubscribersManager();
		$userIds = $vsm->getUserIds($headId);
		if (!count($userIds))
			return true;

		$gotHost = $this->getHostByHearId($headId);
		if (!$gotHost)
			return true;

		$host = "http://".$gotHost['host'];
		$viewLink = $host."/viewcollection/id/".$headId;
		$orgLink = $host."/vieworg/id/".$fromUserId;

		// теперь надо создать публичные уведомления в личку
		$message = "Организатор [url={$orgLink}]{$fromNickName}[/url] открыл новую регулярную закупку, на уведомления о которой Вы подписаны: [url={$viewLink}]{$headName}[/url]";

		$zhm = new ZakupkaHeaderManager();
		$zhObj = $zhm->getById($headId);
		if (!$zhObj)
			return true;

		$pem = new PublicEventManager();
		$pem->buildInsertOnZakupka($zhObj->ownerSiteId, $zhObj->ownerOrgId, $userIds, $fromUserId, $fromNickName, $headId, $headName, $message);

		// теперь на мыло
		$um = new UserManager();
		$users = $um->getByIds($userIds);

		// ставим рассылать сообщения на почту
		$gotHost = $this->getHostByHearId($headId);
		if (!$gotHost)
			return true;

		$host = "http://".$gotHost['host'];
		$signMessage = SettingsManager::getValue($gotHost['ownerSiteId'], $gotHost['ownerOrgId'], 'mail_sign');
		$fromEmail = SettingsManager::getValue($gotHost['ownerSiteId'], $gotHost['ownerOrgId'], 'mail_from');
		$fromName = SettingsManager::getValue($gotHost['ownerSiteId'], $gotHost['ownerOrgId'], 'mail_fromName');

		$header = Enviropment::prepareForMail(MailTextHelper::parse("header.html"));
		$footer = Enviropment::prepareForMail(MailTextHelper::parse("footer.html"));

		$viewLink = $host."/viewcollection/id/".$headId;
		$orgLink = $host."/vieworg/id/".$fromUserId;

		$shortTitle = "Уведомление о регулярной закупке";
		$mailTpl = "zakupkavikupstart.html";

		foreach ($users AS $oneUser)
		{
			if ($oneUser->firstName || $oneUser->secondName)
				$oneUser->firstName = ', '.$oneUser->firstName;

			if ($oneUser->secondName)
				$oneUser->secondName = ' '.$oneUser->secondName;

			if ($oneUser->lastName)
				$oneUser->lastName = ' '.$oneUser->lastName;

			$vars = array(
				"MESSAGE_TITLE" => $shortTitle,
				"ZAK_TITLE" => $headName,
				"USER_LOGIN" => $oneUser->nickName,
				"FIRST_NAME" => $oneUser->firstName,
				"SECOND_NAME" => $oneUser->secondName,
				"LAST_NAME" => $oneUser->lastName,
				"VIEW_LINK" => $viewLink,
				"ORG_LINK" => $orgLink,
				"ORG_NICK" => $fromNickName,
				"MESSAGE_SIGN" => $signMessage,
				"HOST" => $host
			);

			$body = Enviropment::prepareForMail(MailTextHelper::parse($mailTpl, $vars));

			// ставим письмо в очередь
			allMailManager::addMailMessage($shortTitle, $header.$body.$footer, $oneUser->login, $fromEmail, $fromName);

		}

		return true;
	}


	// отдает хост по id закупки
	public function getHostByHearId($headId)
	{
		$zhm = new ZakupkaHeaderManager();
		$zhObj = $zhm->getById($headId);
		if ($zhObj)
		{
			$ownerSiteObj = null;
			$ownerOrgObj = null;
			if ($zhObj->ownerSiteId)
			{
				$osm = new OwnerSiteManager();
				$ownerSiteObj = $osm->getById($zhObj->ownerSiteId);
			}

			if ($zhObj->ownerOrgId)
			{
				$oom = new OwnerOrgManager();
				$ownerOrgObj = $oom->getById($zhObj->ownerOrgId);
			}

			// собираем хост
			$hostName = "";
			if ($ownerOrgObj)
				$hostName = $hostName.$ownerOrgObj->alias.".";

			if ($ownerSiteObj)
				$hostName = $hostName.$ownerSiteObj->hostName;

			return array("host" => $hostName, "ownerSiteId" => $zhObj->ownerSiteId, "ownerOrgId" => $zhObj->ownerOrgId);

		}

		return false;

	}


	public function calcOlapSiteOwner($ownerSiteId)
    {
        // за прошедьший день (за весь) надо вычислить данные по сайту и сохранить в olap, а так же инкрументировать в аккумулятивные поля
        $countUsers = 0; $countActiveUsers = 0; $countZak = 0; $countZakOpen = 0; $countZakFinished = 0;
        $amountZak = 0; $amountSiteCommisionAdded = 0; $amountSiteCommisionPayed = 0;

        $modification = "-1 day";
        if (!$modification) {
            $useDate = date("Y-m-d");
        }
        else {
            $currentTs = strtotime(date("Y-m-d"));
            $modifiedTs = strtotime($modification, $currentTs);
            $useDate = date("Y-m-d", $modifiedTs);
        }

        $tsStart = strtotime($useDate." 00:00:00");
        $tsFinish = strtotime($useDate." 23:59:59");

        $um = new UserManager();
        $countUsersArray = $um->getUsersTotal($ownerSiteId, 0, $tsStart, $tsFinish);
        if (count($countUsersArray)) {
            $countUsers = $countUsersArray['activated'] + $countUsersArray['nonactivated'];
            $countActiveUsers = $countUsersArray['activated'];
        }

        $zhm = new ZakupkaHeaderManager();
        $countZakArray = $zhm->getZakTotal($ownerSiteId, 0, $tsStart, $tsFinish);
        if (count($countZakArray)) {
            // кол-во закупок новых за день всего
            $countZak = $countZakArray['created'];
            // кол-во открывшихся закупок за день
            $countZakOpen = $countZakArray['opened'];
			// кол-во завершённых закупок
			$countZakFinished =         $countZakArray['finished'];
			$amountZak =                $countZakArray['amountZak'];
            $amountSiteCommisionAdded = $countZakArray['amountSiteCommisionAdded'];
			$amountSiteCommisionPayed = $countZakArray['amountSiteCommisionPayed'];
        }

        // по таблице `orderHead`:
        // то что заработает орг
        // выставленные суммы по заказам
        // (без учёта доставки)
        $ohm = new OrderHeadManager();
        $amountOrgCommisionAdded = $ohm->getOrgAddedCommisionAmount($ownerSiteId, 0, $tsStart, $tsFinish);

        // по таблице `orderHead`:
        // то что заплачено оргу в части его заработка (орг сбора)
        // фактически оплаченные суммы по заказам
        // (без учёта доставки, т.е. надо убрать доставку!)
        $amountOrgCommisionPayed = $ohm->getOrgPayedCommisionAmount($ownerSiteId, 0, $tsStart, $tsFinish);

        // за каждый заказ (pay per click) надо посчитать сумму начисленную по таблице orderList
        $mcm = new MainCommisionManager();
        $amountMainCommisionAdded = $mcm->getClickedOrdersAddedAmountTotal($ownerSiteId, 0, $tsStart, $tsFinish);

        // сколько оплачено за заказы сайту статистика
        $amountMainCommisionPayed = $mcm->getClickedOrdersPayedAmountTotal($ownerSiteId, 0, $tsStart, $tsFinish);

        // сохранить полученные данные в таблицу olapOwnerSite
        $osm = new OlapOwnerSiteManager();
        $osObj = new OlapOwnerSite();

        $osObj->dataDate = date("Y-m-d");
        $osObj->ownerSiteId = $ownerSiteId;

        $osObj->countUsers = $countUsers;
        $osObj->countActiveUsers = $countActiveUsers;
        $osObj->countZak = $countZak;
        $osObj->countZakOpen = $countZakOpen;
        $osObj->countZakFinished = $countZakFinished;

        $osObj->amountZak = $amountZak;
        $osObj->amountOrgCommisionAdded = $amountOrgCommisionAdded;
        $osObj->amountOrgCommisionPayed = $amountOrgCommisionPayed;
        $osObj->amountSiteCommisionAdded = $amountSiteCommisionAdded;
        $osObj->amountSiteCommisionPayed = $amountSiteCommisionPayed;
        $osObj->amountMainCommisionAdded = $amountMainCommisionAdded;
        $osObj->amountMainCommisionPayed = $amountMainCommisionPayed;

        $osm->save($osObj);

        return true;
    }


    public function calcOlapOrgOwner($ownerOrgId)
    {
        // за прошедьший день (за весь) надо вычислить данные по сайту и сохранить в olap, а так же инкрументировать в аккумулятивные поля
        $countUsers = 0; $countActiveUsers = 0; $countZak = 0; $countZakOpen = 0; $countZakFinished = 0;
        $amountZak = 0; $amountSiteCommisionAdded = 0; $amountSiteCommisionPayed = 0;

        $modification = "-1 day";
        if (!$modification) {
            $useDate = date("Y-m-d");
        }
        else {
            $currentTs = strtotime(date("Y-m-d"));
            $modifiedTs = strtotime($modification, $currentTs);
            $useDate = date("Y-m-d", $modifiedTs);
        }

        $tsStart = strtotime($useDate." 00:00:00");
        $tsFinish = strtotime($useDate." 23:59:59");

        $um = new UserManager();
        $countUsersArray = $um->getUsersTotal(0, $ownerOrgId, $tsStart, $tsFinish);
        if (count($countUsersArray)) {
            $countUsers = $countUsersArray['activated'] + $countUsersArray['nonactivated'];
            $countActiveUsers = $countUsersArray['activated'];
        }

        $zhm = new ZakupkaHeaderManager();
        $countZakArray = $zhm->getZakTotal(0, $ownerOrgId, $tsStart, $tsFinish);
        if (count($countZakArray)) {
            // кол-во закупок новых за день всего
            $countZak = $countZakArray['created'];
            // кол-во открывшихся закупок за день
            $countZakOpen = $countZakArray['opened'];
            // кол-во завершённых закупок
            $countZakFinished =         $countZakArray['finished'];
            $amountZak =                $countZakArray['amountZak'];
            $amountSiteCommisionAdded = $countZakArray['amountSiteCommisionAdded'];
            $amountSiteCommisionPayed = $countZakArray['amountSiteCommisionPayed'];
        }

        // по таблице `orderHead`:
        // то что заработает орг
        // выставленные суммы по заказам
        // (без учёта доставки)
        $ohm = new OrderHeadManager();
        $amountOrgCommisionAdded = $ohm->getOrgAddedCommisionAmount(0, $ownerOrgId, $tsStart, $tsFinish);

        // по таблице `orderHead`:
        // то что заплачено оргу в части его заработка (орг сбора)
        // фактически оплаченные суммы по заказам
        // (без учёта доставки, т.е. надо убрать доставку!)
        $amountOrgCommisionPayed = $ohm->getOrgPayedCommisionAmount(0, $ownerOrgId, $tsStart, $tsFinish);

        // за каждый заказ (pay per click) надо посчитать сумму начисленную по таблице orderList
        $mcm = new MainCommisionManager();
        $amountMainCommisionAdded = $mcm->getClickedOrdersAddedAmountTotal(0, $ownerOrgId, $tsStart, $tsFinish);

        // сколько оплачено за заказы сайту статистика
        $amountMainCommisionPayed = $mcm->getClickedOrdersPayedAmountTotal(0, $ownerOrgId, $tsStart, $tsFinish);

        // сохранить полученные данные в таблицу olapOwnerSite
        $oom = new OlapOwnerOrgManager();
        $ooObj = new OlapOwnerOrg();

        $ooObj->dataDate = date("Y-m-d");
        $ooObj->ownerOrgId = $ownerOrgId;

        $ooObj->countUsers = $countUsers;
        $ooObj->countActiveUsers = $countActiveUsers;
        $ooObj->countZak = $countZak;
        $ooObj->countZakOpen = $countZakOpen;
        $ooObj->countZakFinished = $countZakFinished;

        $ooObj->amountZak = $amountZak;
        $ooObj->amountOrgCommisionAdded = $amountOrgCommisionAdded;
        $ooObj->amountOrgCommisionPayed = $amountOrgCommisionPayed;
        $ooObj->amountSiteCommisionAdded = $amountSiteCommisionAdded;
        $ooObj->amountSiteCommisionPayed = $amountSiteCommisionPayed;
        $ooObj->amountMainCommisionAdded = $amountMainCommisionAdded;
        $ooObj->amountMainCommisionPayed = $amountMainCommisionPayed;

        $oom->save($ooObj);

        return true;
    }


    public function calcCommSiteOwner($ownerSiteId)
    {
        $modification = "-7 day";
        if (!$modification) {
            $useDate = date("Y-m-d");
        }
        else {
            $currentTs = strtotime(date("Y-m-d"));
            $modifiedTs = strtotime($modification, $currentTs);
            $useDate = date("Y-m-d", $modifiedTs);
        }

        $tsStart = strtotime($useDate." 00:00:00");
        $tsFinish = strtotime($useDate." 23:59:59");

        // за каждый заказ (pay per click) надо посчитать количество по таблице orderList
        $olm = new OrderListManager();
        $amountMainCommisionAdded = $olm->getClickedOrdersTotal($ownerSiteId, 0, $tsStart, $tsFinish);

        // выставить счёт в сущность mainCommision
        if ($amountMainCommisionAdded > 0) {
            $payPerOrderPrice = Configurator::get("application:payPerOrderPrice");
            $payPerOrderNeedAmount = $amountMainCommisionAdded * $payPerOrderPrice;
            $mcm = new MainCommisionManager();
            $mcObj = new MainCommision();
            $mcObj->baseAmount = $amountMainCommisionAdded;
            $mcObj->needAmount = $payPerOrderNeedAmount;
            $mcObj->dateCreate = time();
            $mcObj->type = MainCommision::TYPE_CLICK;
            $mcObj->status = MainCommision::STATUS_NEW;
            $mcObj->ownerSiteId = $ownerSiteId;
            $mcObj->ownerOrgId = 0;
            $mcm->save($mcObj);
        }
    }


    public function calcCommOrgOwner($ownerOrgId)
    {
        $modification = "-7 day";
        if (!$modification) {
            $useDate = date("Y-m-d");
        }
        else {
            $currentTs = strtotime(date("Y-m-d"));
            $modifiedTs = strtotime($modification, $currentTs);
            $useDate = date("Y-m-d", $modifiedTs);
        }

        $tsStart = strtotime($useDate." 00:00:00");
        $tsFinish = strtotime($useDate." 23:59:59");

        // за каждый заказ (pay per click) надо посчитать количество по таблице orderList
        $olm = new OrderListManager();
        $amountMainCommisionAdded = $olm->getClickedOrdersTotal(0, $ownerOrgId, $tsStart, $tsFinish);

        // выставить счёт в сущность mainCommision
        if ($amountMainCommisionAdded > 0) {
            $payPerOrderPrice = Configurator::get("application:payPerOrderPrice");
            $payPerOrderNeedAmount = $amountMainCommisionAdded * $payPerOrderPrice;
            $mcm = new MainCommisionManager();
            $mcObj = new MainCommision();
            $mcObj->baseAmount = $amountMainCommisionAdded;
            $mcObj->needAmount = $payPerOrderNeedAmount;
            $mcObj->dateCreate = time();
            $mcObj->type = MainCommision::TYPE_CLICK;
            $mcObj->status = MainCommision::STATUS_NEW;
            $mcObj->ownerSiteId = 0;
            $mcObj->ownerOrgId = $ownerOrgId;
            $mcm->save($mcObj);
        }
    }

}
