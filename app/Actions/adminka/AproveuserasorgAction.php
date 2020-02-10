<?php
/**
* Действие БО для перевода участника в орги
*/
class AproveuserasorgAction extends AdminkaAction
{
	public function execute()
	{
		$userId = Request::getInt("id");
		if (!$userId)
			Adminka::redirect("manageusers", "Не задан ID пользователя");
	
		$um = new UserManager();
		$user = $um->getById($userId);
		if (!$user)
			Adminka::redirect("manageusers", "Пользователь не найден");

		if ($this->ownerSiteId != $user->ownerSiteId || $this->ownerOrgId != $user->ownerOrgId)
			Adminka::redirect("manageusers", "Нет прав на выполнение данного действия");

		$user->isOrg = 1;
		$org = $um->save($user);
		$this->sendEmail($org);

		Adminka::redirect("manageusers", "Участник стал организатором");

	}

	/**
	 * Функция отправляет письмо на е-майл нового орга
	 * об успешной модерации
	 *
	 * @param Org $newOrg орг
	 */
	protected function sendEmail($newOrg)
	{
		$shortTitle = "Заявка организатора одобрена";

		$host = 'http://'.$this->host;
		$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
		$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
		$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

		if ($newOrg->firstName || $newOrg->secondName)
			$newOrg->firstName = ', '.$newOrg->firstName;

		if ($newOrg->secondName)
			$newOrg->secondName = ' '.$newOrg->secondName;

		if ($newOrg->lastName)
			$newOrg->lastName = ' '.$newOrg->lastName;

		$vars = array(
			"MESSAGE_TITLE" => $shortTitle,
			"LOGIN" => $newOrg->login,
			"FIRST_NAME" => $newOrg->firstName,
			"SECOND_NAME" => $newOrg->secondName,
			"LAST_NAME" => $newOrg->lastName,
			"MESSAGE_SIGN" => $signMessage,
			"HOST" => $host
		);

		$header = Utility::prepareStringForMail(MailTextHelper::parse("header.html", $vars));
		$body = Utility::prepareStringForMail(MailTextHelper::parse("orgapproved.html", $vars));
		$footer = Utility::prepareStringForMail(MailTextHelper::parse("footer.html", $vars));

		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
		Mail::send($shortTitle, $header.$body.$footer, $newOrg->login, $fromEmail, $fromName);

	}

}
