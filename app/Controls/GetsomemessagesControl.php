<?php
/**
 * Контрол поднимает несколько сообщений
 *
 */

class GetsomemessagesControl extends AuthorizedUserControl
{
	public $pageTitle = "Сообщения";

	public function preRender()
	{
		parent::preRender();
		$this->layout = "blank.html";
	}

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$ts = time();
		$this->addData("ts", $ts);

		// режим тестирования
		$testmode = Request::getInt('testmode');
		$this->addData("testmode", $testmode);

		// диалог
		$mdm = new MessageDialogueManager();

		// передали id собеседника
		$userId = Request::getInt('userid');
		// дата откуда уже показаны были сообщения
		$mindate = Request::getInt('mindate');
		// id диалога
		$dlgId = Request::getInt('dlgid');
		// итерация
		$iteration = Request::getInt('iteration');

		$shoulShow = true;

		if (!$userId || !$mindate || !$dlgId || !$iteration)
			$shoulShow = false;

		$dlgObj = $mdm->getById($dlgId);
		if (!$dlgObj)
			$shoulShow = false;
		else
		{
			if ($this->ownerSiteId != $dlgObj->ownerSiteId || $this->ownerOrgId != $dlgObj->ownerOrgId)
				$shoulShow = false;

			// учавствует ли актор в диалоге
			if ($dlgObj->user1 != $actor->id && $dlgObj->user2 != $actor->id)
				$shoulShow = false;
			else
				$this->addData("dlgObj", $dlgObj);
		}

		// если всё ок, то делаем выборку
		if ($shoulShow)
		{
			// всё проверили, получаем сообщения
			$mfm = new MessageFromManager();
			$mtm = new MessageToManager();

			// begin ОПРЕДЕЛИТЬ $mindate, от которой будем показывать сообщения
			// переписка на месяц назад, если есть
			$maxdate = $mindate;
			$this->addData("mindate", $mindate);

			// первая итерация "на месяц назад", вторая на год, третья - всё остальное
			if ($iteration == 1)
				$mindate = $mindate - 3600 * 24 * 30;
			else if ($iteration == 2)
				$mindate = $mindate - 3600 * 24 * 365;
			else
				$mindate = 0;

			// страховка
			if ($maxdate < 0)
				$maxdate = 0;

			if ($mindate < 0)
				$mindate = 0;

			$this->addData("iteration", $iteration);

			// end ОПРЕДЕЛИЛИ $mindate, от которой будем показывать сообщения

			$messFrom = $mfm->getMessages($actor->id, $userId, $mindate, $maxdate);
			$messTo = $mtm->getMessages($userId, $actor->id, $mindate, $maxdate);

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

			$this->addData("lmidFrom", $lmidFrom);
			$this->addData("lmidTo", $lmidTo);
			$this->addData("lmid", $lmidTo);

		}

	}

}
