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
$mutex = new Mutex("digestsend", $tmp);

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
	// собственно тут будут действия

	/*
	 * 1) Берём запросом 100 участников, которым пора отправлять сообщение, т.е.
	 *    у которых dateDigest + digestFrequency * 24 * 3600  <  time()
	 *
	 * 2) По каждому такому пользователю от даты последнего визита dateLastVisit
	 *    или даты последнего дайджеста (что больше!) в письмо поместить:
	 *    "За время Вашего отсутствия открылось N новых закупок" (по zakupkaStatusLog)
	 *
	 * 3) На такое-то число (момент рассылки) у Вас столько то не прочитанных
	 *    личных сообщений и столько-то уведомлений.
	 *
	 * 4) На ваши комментарии даны ответы в следующих закупках: ... (ссылки на закупки)
	 *
	 */

	// начали
	$header = Enviropment::prepareForMail(MailTextHelper::parse("header.html"));
	$footer = Enviropment::prepareForMail(MailTextHelper::parse("footer.html"));

	$zlm = new ZakupkaStatusLogManager();
	$pem = new PublicEventManager();
	$msm = new MessageDialogueManager();
	$com = new CoNotificationManager();
	$osm = new OwnerSiteManager();

	$um = new UserManager();

	$users = $um->getDigestUsers();
	if (count($users))
	{
		// перебор - это тупо, но, из-за сложной логики по каждому участнику, уместно
		foreach ($users AS $user)
		{
			// готовим сообщение
			$message = "";

			$host = Configurator::get('application:url');
			$fromEmail = SettingsManager::getValue($user->ownerSiteId, $user->ownerOrgId, 'mail_from');
			$fromName = SettingsManager::getValue($user->ownerSiteId, $user->ownerOrgId, 'mail_fromName');
			$signMessage = SettingsManager::getValue($user->ownerSiteId, $user->ownerOrgId, 'mail_sign');

			if ($user->ownerSiteId)
			{
				$osObj = $osm->getById($user->ownerSiteId);
				if ($osObj)
					$host = "http://".$osObj->hostName;
			}

			$freqlink = $host."/userdigestfrequency";

			// п. 2
			$maxDate = max($user->dateLastVisit, $user->dateDigest);
			$zaksFromLog = $zlm->getOpenZakFromDate($user->ownerSiteId, $user->ownerOrgId, $maxDate);
			$newOpenZakCount = count($zaksFromLog);
			if ($newOpenZakCount > 0)
				$message .= 'С момента Вашего предыдущего визита на сайт <a href="'.$host.'">'.$host.'</a> открылось новых закупок: '.$newOpenZakCount.'<br>';

			$ts = time();
			$showDate = Utility::dateFormat($ts, "d M Y");

			// п. 3
			$countNewMessages = intval($msm->countNewMessages($user->id));
			$countNewEvents = intval($pem->countNewMessages($user->id));

			if ($countNewMessages || $countNewEvents)
			{
				$message .= 'На '.$showDate.' у Вас ';
				if ($countNewMessages)
					$message .= $countNewMessages.' не прочитанных личных сообщений';

				if ($countNewMessages && $countNewEvents)
					$message .= ' и ';

				if ($countNewEvents)
					$message .= $countNewEvents.' уведомлений о событиях.<br>';

			}

			// п. 4
			$comList = $com->getByUserId($user->id, $maxDate);
			if (count($comList))
			{
				if ($user->isOrg)
					$message .= 'В следующих закупках есть НОВЫЕ комментарии, возможно, требующие ответа:<br>';
				else
					$message .= 'На ваши комментарии даны ответы в следующих закупках:<br>';

				foreach ($comList AS $oneLogItem)
				{
					$viewLink = $host."/viewcollection/id/".$oneLogItem->headId;
					$message .= '<a href="'.$viewLink.'">'.$oneLogItem->zakName.'</a><br>';
				}

				$message .= '<br>';
			}

			// обновим дату в профиле пользователя
			$user->dateDigest = time();
			$um->save($user);

			// собственно отправка письма
			// если есть что сообщить
			if ($newOpenZakCount > 0 || $countNewMessages || $countNewEvents || count($comList))
			{
				$shortTitle = "Дайджест событий {$showDate} от  {$host}";
				$shortTitle = str_replace('http://', '', $shortTitle);
				$shortTitle = str_replace('https://', '', $shortTitle);

				$vars = array(
					"MESSAGE_TITLE" => $shortTitle,
					"USER_LOGIN" => $user->nickName,
					"FIRST_NAME" => $user->firstName,
					"SECOND_NAME" => $user->secondName,
					"LAST_NAME" => $user->lastName,
					"MAIN_MESSAGE" => $message,
					"MESSAGE_SIGN" => $signMessage,
					"FREQ_LINK" => $freqlink,
					"HOST" => $host
				);

				$body = Enviropment::prepareForMail(MailTextHelper::parse("anytext.html", $vars));

				// ставим письмо в очередь
				allMailManager::addMailMessage($shortTitle, $header.$body.$footer, $user->login, $fromEmail, $fromName);

			}

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
