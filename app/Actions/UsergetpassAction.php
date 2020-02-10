<?php
/**
* Действие для генерации нового пароля
*
*/
class UsergetpassAction extends BaseAction implements IPublicAction
{
	public function execute()
	{
		$email = FilterInput::add(new EmailFilter("email", true, "E-Mail"));

		$um = new UserManager();
		$curUser = $um->getByLogin($this->ownerSiteId, $this->ownerOrgId, $email);
		if (!$curUser)
			Enviropment::redirectBack("На указанный E-Mail отправлен новый пароль");

		$newPass = $um->generatePassword();

		$curUser->password = md5(md5($newPass).sha1($newPass));
		$curUser = $um->save($curUser);

		// отправим код для подтверждения
		$this->sendEmail($curUser, $newPass);

		// поменяем пароль на форуме тоже
		$confSection = "vbforum";
		if ($this->ownerOrgId)
		{
			$confSection = $confSection.$this->ownerSiteId."_".$this->ownerOrgId;
		}
		else
		{
			if ($this->ownerSiteId > 1)
				$confSection = $confSection.$this->ownerSiteId;
		}

		$extDBConnData = Configurator::getSection($confSection);
		$vbConn = null;
		$vbConn = @mysql_connect($extDBConnData['host'].":".$extDBConnData['port'], $extDBConnData['user'], $extDBConnData['password']);
		if ($vbConn)
		{
			mysql_select_db($extDBConnData['database']);
			mysql_query("SET NAMES utf8", $vbConn);

			$sql = "UPDATE ".$extDBConnData['prefix']."users SET password = '{$curUser->password}' WHERE email = '{$curUser->login}'";
			mysql_query($sql, $vbConn);

			mysql_close($vbConn);
		}

		// переход на страницу входа
		Enviropment::redirect("userlogin", "На указанный E-Mail отправлен новый пароль");

	}


	/**
	* Функция отправляет письмо на е-майл покупателя
	* с новым паролем
	*/
	protected function sendEmail($curUser, $newPass)
	{
		$shortTitle = "Новый пароль";

		$host = 'http://'.$this->host;
		$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
		$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
		$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

		if ($curUser->firstName || $curUser->secondName)
			$curUser->firstName = ', '.$curUser->firstName;

		if ($curUser->secondName)
			$curUser->secondName = ' '.$curUser->secondName;

		if ($curUser->lastName)
			$curUser->lastName = ' '.$curUser->lastName;

		$vars = array(
			"MESSAGE_TITLE" => $shortTitle,
			"ORG_LOGIN" => $curUser->login,
			"FIRST_NAME" => $curUser->firstName,
			"SECOND_NAME" => $curUser->secondName,
			"NEW_PASS" => $newPass,
			"LAST_NAME" => $curUser->lastName,
			"MESSAGE_SIGN" => $signMessage,
			"HOST" => $host
		);

		$header = Enviropment::prepareForMail(MailTextHelper::parse("header.html", $vars));
		$body = Enviropment::prepareForMail(MailTextHelper::parse("usernewpass.html", $vars));
		$footer = Enviropment::prepareForMail(MailTextHelper::parse("footer.html", $vars));

		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
		Mail::send($shortTitle, $header.$body.$footer, $curUser->login, $fromEmail, $fromName);
		Mail::send($shortTitle, $header.$body.$footer, 'info@massapokupok.ru', $fromEmail, $fromName);

	}

}
