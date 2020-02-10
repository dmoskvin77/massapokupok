<?php
/**
 * Менеджер
 */
class BroadcastManager extends BaseEntityManager
{
	/*
	 * Функция отдает список по массиву id
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($inpIds)
	{
		if(!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("id IN ($ids)", null, "id"));

		return Utility::sort($inpIds, $res);
	}

	// получить все категории
	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

	// получить новые объявления (статус NEW)
	public function getByStatus($ownerSiteId, $ownerOrgId, $status = null)
	{
		if (!$status)
			$status = Broadcast::STATUS_NEW;

		$sql = new SQLCondition("status = '{$status}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		$sql->orderBy = "dateCreate DESC";
		$rez = $this->get($sql);
		return $rez;
	}


	// массовая рассылка того что одобрил администратор
	public static function send()
	{
		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";

		$sql1 = "SELECT * FROM broadcast WHERE status = 'STATUS_APPROVED' LIMIT 1";
		$bcm = new BroadcastManager();
		$bcArray = $bcm->getOneByAnySQL($sql1);
		if (!$bcArray)
			return false;

		$sql3 = "UPDATE broadcast SET status = 'STATUS_SENT' WHERE id = ".$bcArray['id'];
		$bcm->executeNonQuery($sql3);

		// получить мыло и ФИО всех пользователей
		$sql2 = "SELECT login, nickName, firstName, lastName, secondName FROM user WHERE entityStatus = 1 AND ownerSiteId = {$bcArray['ownerSiteId']} AND ownerOrgId = {$bcArray['ownerOrgId']}";
		$um = new UserManager();
		$list = $um->getByAnySQL($sql2);

		$totalSent = 0;
		$usleep = Configurator::get("mail:usleep");
		$usend = Configurator::get("mail:usend");

		// ставим рассылать сообщения на почту
		$osm = new OwnerSiteManager();
		$ownerSiteObj = $osm->getById($bcArray['ownerSiteId']);
		if (!$ownerSiteObj)
			return false;

		$host = "http://".$ownerSiteObj->hostName;
		$fromEmail = SettingsManager::getValue($bcArray['ownerSiteId'], $bcArray['ownerOrgId'], 'mail_from');
		$fromName = SettingsManager::getValue($bcArray['ownerSiteId'], $bcArray['ownerOrgId'], 'mail_fromName');
		$signMessage = SettingsManager::getValue($bcArray['ownerSiteId'], $bcArray['ownerOrgId'], 'mail_sign');

		// отправляет типа как "голимый спам" пока что
		// TODO: надо это поправить чтобы было обращение по ФИО

		if (count($list) > 0)
		{
			foreach ($list as $item)
			{
				$body = Utility::prepareStringForMail($bcArray['message']);
				$res = Mail::send($fromName." новости", $body, $item['login'], $fromEmail, $fromName);

				$totalSent++;
				if ($totalSent % $usend == 0)
					usleep($usleep * 1000);

			}
		}

		return $totalSent > 0 ? self::STATUS_SENT : self::STATUS_NOTHING;
	}

}
