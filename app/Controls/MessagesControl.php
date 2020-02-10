<?php
/**
 * Контрол для просмотра личных сообщений
 *
 */

class MessagesControl extends AuthorizedUserControl
{
	public $pageTitle = "Сообщения и уведомления";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$ts = time();
		$this->addData("ts", $ts);

		$mode = Request::getVar("mode");
		$this->addData("mode", $mode);

		// поднимаем диалоги
		$mdm = new MessageDialogueManager();
		$dialogues = $mdm->getDialogues($actor->id);
		$this->addData("dialogues", $dialogues);

		// передали id собеседника
		$userId = Request::getInt('userid');
		if ($userId)
		{
			$dlgId = Request::getInt('dlgid');
			if (!$dlgId)
				Enviropment::redirectBack("Не указан ID диалога");

			$dlgObj = $mdm->getById($dlgId);
			if (!$dlgObj)
				Enviropment::redirectBack("Не выбран диалог");

			if ($this->ownerSiteId != $dlgObj->ownerSiteId || $this->ownerOrgId != $dlgObj->ownerOrgId)
				Enviropment::redirectBack("Нет прав на выполнение данного действия");

			$this->addData("dlgObj", $dlgObj);

			// учавствует ли актор в диалоге
			if ($dlgObj->user1 != $actor->id && $dlgObj->user2 != $actor->id)
				Enviropment::redirectBack("Нет прав на просмотр диалога");

			// всё проверили, получаем сообщения
			$mfm = new MessageFromManager();
			$mtm = new MessageToManager();

			// begin ОПРЕДЕЛИТЬ $mindate, от которой будем показывать сообщения
			if ($actor->id > $userId)
				$lastReadId = $dlgObj->lastReadId12;
			else
				$lastReadId = $dlgObj->lastReadId21;

			$showMessFromFromDate = $mfm->getMessFromFromDate($this->ownerSiteId, $this->ownerOrgId, $lastReadId);

			if ($actor->id > $userId)
				$lastReadId = $dlgObj->lastReadId21;
			else
				$lastReadId = $dlgObj->lastReadId12;

			$showMessToFromDate = $mtm->getMessToFromDate($this->ownerSiteId, $this->ownerOrgId, $lastReadId);

			$mindate = min($showMessFromFromDate, $showMessToFromDate);

			// переписка за 3 дня, если есть
			if ($mindate > 0)
				$mindate = $mindate - 3600 * 24 * 3;

			// страховка
			if ($mindate < 0)
				$mindate = 0;

			$this->addData("mindate", $mindate);

			// end ОПРЕДЕЛИЛИ $mindate, от которой будем показывать сообщения

			$messFrom = $mfm->getMessages($actor->id, $userId, $mindate);
			$messTo = $mtm->getMessages($userId, $actor->id, $mindate);

			// объединить эти два массива с сортировкой по ключу dateCreate
			$messAll = array();

			// наибольший прочитанный id
			$lmidFrom = 0;
			if (count($messFrom)) {
				if (count($messFrom)) {
					foreach ($messFrom AS $mfFrom) {
						$messAll[$mfFrom->dateCreate] = $mfFrom;
						if (($mfFrom->userId == $actor->id || $mfFrom->userToId == $actor->id) && $mfFrom->id > $lmidFrom)
							$lmidFrom = $mfFrom->id;
					}
				}
			}

			$lmidTo = 0;
			if (count($messTo)) {
				foreach ($messTo AS $mfTo) {
					$messAll[$mfTo->dateCreate] = $mfTo;
					if (($mfTo->userId == $actor->id || $mfTo->userToId == $actor->id) && $mfTo->id > $lmidTo)
						$lmidTo = $mfTo->id;
				}
			}

			ksort($messAll);
			$this->addData("messAll", $messAll);

			// ники собеседников
			$um = new UserManager();
			$user = $um->getById($userId);
			$this->addData("user", $user);
			$this->addData("actor", $actor);

			// диалог и отметки о прочтении в него запишем
			// актор увидел все сообщения, значит каунт новых ставим в ноль
			$mdm->markDlgRead($actor->id, $userId);
			// отмечаем и сами сообщения тоже
			if ($lmidFrom > 0)
				$mdm->setLastReadId($actor->id, $lmidFrom, $userId);

			$this->addData("lmidFrom", $lmidFrom);
			$this->addData("lmidTo", $lmidTo);
			$this->addData("lmid", $lmidTo);

		} else {

			// покажем все публичные уведомления
			$pem = new PublicEventManager();
			// не прочитанные, либо созданные 7 дней назад и ранее
			$publicEvents = $pem->getByUserId($actor->id, 3600 * 24 * 7);
			// $countAllMessages = $pem->countAllMessages($actor->id);

			if (count($publicEvents)) {
				// соберем id всех не прочитанных ранее уведомлений
				// чтобы отметить их как прочитанные
				$collectEventsIds = array();
				foreach ($publicEvents AS $oneEvent) {
					if (!$oneEvent->dateRead)
						$collectEventsIds[] = $oneEvent->id;
				}

				// отметим что всё прочитано
				if (count($collectEventsIds))
					$pem->setAsRead($collectEventsIds);

				// передадим в шаблон
				$this->addData("publicEvents", $publicEvents);

			}

			// это страница пейджинга
			$page = Request::getInt('page');
			if (!count($publicEvents) && !$page)
				$page = 1;


		}

	}

}
