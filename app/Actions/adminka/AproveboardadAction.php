<?php
/**
* Действие БО для одобрения объявления
*/
class AproveboardadAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("manageboard", "Не задан ID объявления");

		$bam = new BoardAdManager();
		$boardObject = $bam->getById($id);
		if (!$boardObject)
			Adminka::redirect("manageboard", "Объявление не найдено");

		if ($this->ownerSiteId != $boardObject->ownerSiteId || $this->ownerOrgId != $boardObject->ownerOrgId)
			Adminka::redirect("manageboard", "Нет прав на выполнение выбранного действия");

		if ($boardObject->status != BoardAd::STATUS_NEW)
			Adminka::redirect("manageboard", "Статус объявления не позволяет его одобрить");

		$boardObject->status = BoardAd::STATUS_ACTIVE;
		$boardObject->datePublish = time();
		$boardObject = $bam->save($boardObject);

		// пересчитаем в категории и в типе кол-во объявлений, обновим данные
		$bam->rebuildCounters($boardObject->typeId, $boardObject->catId);

		// отправим сообщение на е-майл "Объявление опубликовано"
		$um = new UserManager();
		$userObj = $um->getById($boardObject->userId);
		$this->sendEmail($userObj, $boardObject);

		Adminka::redirect("manageboard", "Объявление опубликовано");

	}


	// уведомляем автора
	protected function sendEmail($userObj, $boardObject)
	{
		$shortTitle = "Ваше объявление одобрено";

		$host = 'http://'.$this->host;
		$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
		$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
		$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

		$boardLink = $host."/board/id/".$boardObject->id;

		if ($userObj->firstName || $userObj->secondName)
			$userObj->firstName = ', '.$userObj->firstName;

		if ($userObj->secondName)
			$userObj->secondName = ' '.$userObj->secondName;

		if ($userObj->lastName)
			$userObj->lastName = ' '.$userObj->lastName;

		$vars = array(
			"MESSAGE_TITLE" => $shortTitle,
			"USER_LOGIN" => $userObj->login,
			"FIRST_NAME" => $userObj->firstName,
			"SECOND_NAME" => $userObj->secondName,
			"LAST_NAME" => $userObj->lastName,
			"BOARD_LINK" => $boardLink,
			"MESSAGE_SIGN" => $signMessage,
			"HOST" => $host
		);

		$header = Adminka::prepareForMail(MailTextHelper::parse("header.html", $vars));
		$body = Adminka::prepareForMail(MailTextHelper::parse("boardapproved.html", $vars));
		$footer = Adminka::prepareForMail(MailTextHelper::parse("footer.html", $vars));

		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
		Mail::send($shortTitle, $header.$body.$footer, $userObj->login, $fromEmail, $fromName);

	}

}
