<?php

$srvBasePath = '..';

set_time_limit(0);

// требуется полный путь к файлам для запуска в режиме cli
require_once $srvBasePath.'/app/Config/framework.php';
require_once $srvBasePath.'/app/BaseApplication.php';
require_once $srvBasePath.'/app/Enviropment.php';
require_once $srvBasePath.'/framework/core/Utils.php';

Logger::init(Configurator::getSection("logger"));

$tmp = Configurator::get("application:tempDir");

try
{
	$iteration = Request::getInt('iteration');

	$um = new UserManager();
	$sql = "SELECT * FROM user WHERE id > {$iteration} AND entityStatus = 1 LIMIT 1";
	$userObj = (object) $um->getOneByAnySQL($sql);
	if (!$userObj)
	{
		echo "no user"; exit;
	}
	else
		echo "userId: ".$userObj->id."<br/>";

	// данные все готовим, в том числе и nickName
	$login = $userObj->login;
	$nickName = $userObj->nickName;
	$password = $userObj->password;
	$salt = $userObj->id."".substr(Utils::getGUID(), 0, rand(15, 20));

	$ts = time();

	$confSection = "vbforum";
	if ($userObj->ownerOrgId)
	{
		$confSection = $confSection.$userObj->ownerSiteId."_".$userObj->ownerOrgId;
	}
	else
	{
		if ($userObj->ownerSiteId > 1)
			$confSection = $confSection.$userObj->ownerSiteId;
	}

	echo "confSection: {$confSection}<br/>";

	// перед редиректом засунем инфу в базу форума
	$extDBConnData = Configurator::getSection($confSection);
	$vbConn = null;
	$vbConn = @mysql_connect($extDBConnData['host'].":".$extDBConnData['port'], $extDBConnData['user'], $extDBConnData['password']);
	if ($vbConn)
	{
		mysql_select_db($extDBConnData['database']);
		mysql_query("SET NAMES utf8", $vbConn);

		$sql = "INSERT IGNORE INTO ".$extDBConnData['prefix']."users (group_id, username, password, salt, email, timezone, dst, registered) VALUES (3, '{$nickName}', '{$password}', '{$salt}', '{$login}', 2, 1, {$ts})";
		mysql_query($sql, $vbConn);

		$sql = "UPDATE ".$extDBConnData['prefix']."users SET password = '{$curUser->password}' WHERE email = '{$curUser->login}'";
		mysql_query($sql, $vbConn);

		mysql_close($vbConn);
	}

	$iteration = $userObj->id;

	sleep(2);
	// редирект на тот же скрипт
	echo '<script language="javascript" type="text/javascript">window.location.href = "/install/synhronizeforum5.php?iteration='.$iteration.'";</script>';

}
catch (Exception $e)
{
	echo $e->getMessage()." ".$e->getTraceAsString()."\n";
}
