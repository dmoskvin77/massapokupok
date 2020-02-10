<?php
/**
* Действие БО для отклонения рассылки
*/
class RemovebroadcastAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("managebroadcast", "Не задан ID рассылки");

		$bcm = new BroadcastManager();
		$bcObj = $bcm->getById($id);
		if (!$bcObj)
			Adminka::redirect("managebroadcast", "Рассылка не найдена");

		if ($this->ownerSiteId != $bcObj->ownerSiteId || $this->ownerOrgId != $bcObj->ownerOrgId)
			Adminka::redirect("managebroadcast", "Нет прав на выполнение выбранного действия");

		if ($bcObj->status != Broadcast::STATUS_NEW)
			Adminka::redirect("managebroadcast", "Статус объявления не позволяет его одобрить");

		// можно забирать кроном и отправлять
		$bcObj->status = Broadcast::STATUS_DECLINED;
		$bcObj = $bcm->save($bcObj);

		$um = new UserManager();
		$userObj = $um->getById($bcObj->orgId);
		if ($userObj)
			$this->sendEmail($userObj);

		Adminka::redirect("managebroadcast", "Рассылка отклонена");

	}


	// уведомляем автора
	protected function sendEmail($userObj)
	{
		$shortTitle = "Ваша рассылка отклонено";

		$host = 'http://'.$this->host;
		$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
		$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
		$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

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
			"MESSAGE_SIGN" => $signMessage,
			"HOST" => $host
		);

		$header = Adminka::prepareForMail(MailTextHelper::parse("header.html", $vars));
		$body = Adminka::prepareForMail(MailTextHelper::parse("broadcastdeclined.html", $vars));
		$footer = Adminka::prepareForMail(MailTextHelper::parse("footer.html", $vars));

		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
		Mail::send($shortTitle, $header.$body.$footer, $userObj->login, $fromEmail, $fromName);

	}

}
