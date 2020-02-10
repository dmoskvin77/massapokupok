<?php
/**
 * Менеджер
 */
class AllMailManager extends BaseEntityManager
{
	function __construct()
	{
		$this->setCommonConnection(Application::getConnection("master"));
	}

	/** сообщения отправлены */
	const STATUS_SENT = 'STATUS_SENT';
	/** нет писем для отправки */
	const STATUS_NOTHING = 'STATUS_NOTHING';

	/**
	 * Функция добавляет сообщение в очередь отправки
	 *
	 */
	public static function addMailMessage($subj, $body, $toMail, $fromEmail, $fromName)
	{
		$mm = new self();
		$massmail = new allMail();

		$massmail->status = allMail::STATUS_NEW;

		$massmail->subj = $subj;
		$massmail->body = $body;
		$massmail->toMail = $toMail;

		$massmail->mailFrom = $fromEmail;
		$massmail->mailFromName = $fromName;

		$mm->save($massmail);

		return $massmail->id;
	}


	/*
	 * Функция осуществляет отправку сообщений на е-майл пользователей
	 *
	 */
	public static function send()
	{
		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";

		$sql = "SELECT
					am.id, am.toMail, am.subj, am.body, am.mailFrom, am.mailFromName
				FROM
					allMail AS am
				WHERE
					am.status = '".allMail::STATUS_NEW."'
				LIMIT
					".Configurator::get("mail:uall");

		$mm = new self();
		$list = $mm->getByAnySQL($sql);

		$totalSent = 0;
		$usleep = Configurator::get("mail:usleep");
		$usend = Configurator::get("mail:usend");

		if (count($list) > 0)
		{
			foreach($list as $item)
			{
				$item = (object)$item;

				$fromEmail = $item->mailFrom;
				$fromName = $item->mailFromName;

				$body = Utility::prepareStringForMail($item->body);

				$res = Mail::send($item->subj, $body, $item->toMail, $fromEmail, $fromName);
				if ($res)
					$mm->remove($item->id);

				$totalSent++;

				if ($totalSent % $usend == 0)
					usleep($usleep * 1000);

			}
		}

		return $totalSent > 0 ? self::STATUS_SENT : self::STATUS_NOTHING;
	}


	// удаляет отложенные сообщения
	public function removePending($ccId = null, $zcId = null)
	{
		$ccId = intval($ccId);
		$zcId = intval($zcId);

		if ($ccId > 0)
		{
			$query = "DELETE FROM allMail WHERE ccId = {$ccId}";
			$this->executeNonQuery($query);

			$query = "DELETE FROM allvkNotice WHERE ccId = {$ccId}";
			$this->executeNonQuery($query);
		}

		if ($zcId > 0)
		{
			$query = "DELETE FROM allMail WHERE zcId = {$zcId}";
			$this->executeNonQuery($query);

			$query = "DELETE FROM allvkNotice WHERE zcId = {$zcId}";
			$this->executeNonQuery($query);
		}

	}

	// задержанные разрешает отправить
	public function allowPending($ccId = null, $zcId = null)
	{
		$ccId = intval($ccId);
		$zcId = intval($zcId);

		if ($ccId > 0)
		{
			$query = "UPDATE allMail SET status = ".allMail::STATUS_NEW." WHERE ccId = {$ccId}";
			$this->executeNonQuery($query);

			$query = "UPDATE allvkNotice SET status = ".allMail::STATUS_NEW." WHERE ccId = {$ccId}";
			$this->executeNonQuery($query);
		}

		if ($zcId > 0)
		{
			$query = "UPDATE allMail SET status = ".allMail::STATUS_NEW." WHERE zcId = {$zcId}";
			$this->executeNonQuery($query);

			$query = "UPDATE allvkNotice SET status = ".allMail::STATUS_NEW." WHERE zcId = {$zcId}";
			$this->executeNonQuery($query);
		}

	}

}
