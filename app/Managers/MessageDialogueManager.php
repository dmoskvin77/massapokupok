<?php
/**
* Диалоги между пользователями
*/
class MessageDialogueManager extends BaseEntityManager
{
	public function addMessage($ownerSiteId, $ownerOrgId, $actorId, $userId, $message)
	{
		// вставка диалога, если диалог есть, то update
		$dlgUsrIds = array($userId, $actorId);
		sort($dlgUsrIds);

		$this->addDlg($ownerSiteId, $ownerOrgId, $dlgUsrIds[0], $dlgUsrIds[1], $actorId);

		$dateCreate = time();

		// из messageFrom получить все сообщения для данной пары автор-получатель,
		// с временем больше или равным текущему
		$mfm = new MessageFromManager();
		$gotLatestMsgs = $mfm->getLatestMessages($actorId, $userId, $dateCreate);
		if (count($gotLatestMsgs))
		{
			foreach ($gotLatestMsgs AS $gtltmgs)
			{
				// если записи будут, то сменить время на наибольшее найденное, плюс есдиничка
				if ($gtltmgs->dateCreate >= $dateCreate)
					$dateCreate = $gtltmgs->dateCreate + 1;
			}
		}

		// добавить сами сообщения
		$mfm->addMessage($ownerSiteId, $ownerOrgId, $actorId, $userId, $dateCreate, $message);

		$mtm = new MessageToManager();
		$mtm->addMessage($ownerSiteId, $ownerOrgId, $actorId, $userId, $dateCreate, $message);

		return true;
	}


	// добавить диалог, если не существует
	public function addDlg($ownerSiteId, $ownerOrgId, $user1, $user2, $actorId)
	{
		$hasNew12 = 0;
		$hasNew21 = 0;
		if ($user1 == $actorId)
			$hasNew21 = 1;
		else
			$hasNew12 = 1;

		// найдем логины для user1 и user2
		$um = new UserManager();
		$userObject1 = $um->getById($user1);
		$userObject2 = $um->getById($user2);

		$userNick1 = $userObject1->nickName;
		$userNick2 = $userObject2->nickName;

		// установим текущее время
		$curTs = time();

		if ($hasNew12)
			$sql = "INSERT INTO `messageDialogue` (user1, userNick1, user2, userNick2, hasNew12, dateUpdate, ownerSiteId, ownerOrgId) VALUES ({$user1}, '{$userNick1}', {$user2}, '{$userNick2}', 1, {$curTs}, {$ownerSiteId}, {$ownerOrgId}) ON DUPLICATE KEY UPDATE hasNew12 = hasNew12 + 1, dateUpdate = ".$curTs;
		else
			$sql = "INSERT INTO `messageDialogue` (user1, userNick1, user2, userNick2, hasNew21, dateUpdate, ownerSiteId, ownerOrgId) VALUES ({$user1}, '{$userNick1}', {$user2}, '{$userNick2}', 1, {$curTs}, {$ownerSiteId}, {$ownerOrgId}) ON DUPLICATE KEY UPDATE hasNew21 = hasNew21 + 1, dateUpdate = ".$curTs;

		$this->executeNonQuery($sql);

		return true;
	}


	// получить диалоги по отношению к текущему пользователю
	public function getDialogues($userId)
	{
		$sql = new SQLCondition("user1 = {$userId} OR user2 = {$userId}");
		$sql->orderBy = "dateUpdate DESC";
		return $this->get($sql);

	}


	// получить кол-во новых сообщений, адресованных человеку
	public function countNewMessages($userId)
	{
		$newMessCount = 0;

		$sql21 = "SELECT SUM(hasNew21) AS cnt FROM messageDialogue WHERE user2 = {$userId}";
		$res21 = $this->getOneByAnySQL($sql21);
		if ($res21)
			$newMessCount = $newMessCount + intval($res21['cnt']);

		$sql12 = "SELECT SUM(hasNew12) AS cnt FROM messageDialogue WHERE user1 = {$userId}";
		$res12 = $this->getOneByAnySQL($sql12);
		if ($res12)
			$newMessCount = $newMessCount + intval($res12['cnt']);

		return $newMessCount;

	}


	// диалог и отметки о прочтении в него запишем
	// актор увидел все сообщения, значит каунт новых ставим в ноль
	public function markDlgRead($actorId, $userId)
	{
		$sql21 = "UPDATE messageDialogue SET hasNew21 = 0 WHERE user2 = {$actorId} AND user1 = {$userId}";
		$this->executeNonQuery($sql21);

		$sql12 = "UPDATE messageDialogue SET hasNew12 = 0 WHERE user1 = {$actorId} AND user2 = {$userId}";
		$this->executeNonQuery($sql12);

		return true;
	}


	// обновляет lastReadId
	public function setLastReadId($actorId, $lmid, $userId)
	{
		if (!$lmid)
			return false;

		if ($actorId > $userId)
		{
			$sql1 = "UPDATE messageDialogue SET lastReadId12 = {$lmid} WHERE user1 = {$userId} AND user2 = {$actorId}";
			$this->executeNonQuery($sql1);
		}
		else
		{
			$sql2 = "UPDATE messageDialogue SET lastReadId21 = {$lmid} WHERE user1 = {$actorId} AND user2 = {$userId}";
			$this->executeNonQuery($sql2);
		}

		return true;
	}

}
