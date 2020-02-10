<?php
/**
* Действие для регистрации нового пользователя
*
*/
class UserregisterAction extends BaseAction implements IPublicAction
{
	public function execute()
	{
		$email = FilterInput::add(new EmailFilter("email", true, "E-Mail"));
		$nickName = FilterInput::add(new StringFilter("nickName", true, "Ник на сайте"));
		$password = FilterInput::add(new StringFilter("password", true, "Пароль"));
		$reppass = FilterInput::add(new StringFilter("reppass", false, "Повтор пароля"));
		$lastName = FilterInput::add(new StringFilter("lastName", false, "Фамилия"));
		$firstName = FilterInput::add(new StringFilter("firstName", false, "Имя"));
		$secondName = FilterInput::add(new StringFilter("secondName", false, "Отчество"));

		if (SettingsManager::getValue($this->ownerSiteId, $this->ownerOrgId, 'needphone') == 'on')
			$phone1 = FilterInput::add(new StringFilter("phone1", true, "Телефон"));
		else
			$phone1 = FilterInput::add(new StringFilter("phone1", false, "Телефон"));

		$phone2 = FilterInput::add(new StringFilter("phone2", false, "Ещё один телефон"));
		$url = FilterInput::add(new StringFilter("url", false, "URL сайта"));
		$name = FilterInput::add(new StringFilter("name", false, "Организация"));
		$checkrules = FilterInput::add(new StringFilter("checkrules", false, "Подтвержение правил"));
		$reqorg = FilterInput::add(new StringFilter("reqorg", false, "Организатор"));
		$reqopt = FilterInput::add(new StringFilter("reqopt", false, "Поставщик"));
		// человек идёт из соцсети
		$network = FilterInput::add(new StringFilter("network", false, "Соц. сеть"));
		$uid = FilterInput::add(new StringFilter("uid", false, "Идентификатор соц. сети"));

		if (mb_strlen($password) < 4 || mb_strlen($password) > 20)
			FilterInput::addMessage("Длина пароля от 4 до 20 символов");

		if ($reppass && $password != $reppass)
			FilterInput::addMessage("Пароль и повтор не совпадают");

		if ($checkrules != 'on')
			FilterInput::addMessage("Регистрация возможна только если принимаются правила сайта");

		$um = new UserManager();
		if (!FilterInput::isValid())
		{
			FormRestore::add("user-register");
			Enviropment::redirect("userregister", FilterInput::getMessages());
		}

		// соц сетевик
		$gotSocial = null;
		if ($network && $uid)
		{
			$sm = new SocialManager();
			$gotSocial = $sm->getByUid($this->ownerSiteId, $this->ownerOrgId, $uid, $network);
			if ($gotSocial)
			{
				$firstName = $gotSocial->first_name;
				$lastName = $gotSocial->last_name;
			}
		}

		// посмотрим нет ли уже пользователя с выбранной комбинацией логина / пароля
		$pass = md5(md5($password).sha1($password));
		$user = $um->getOne(new SQLCondition("login = '{$email}' AND password = '{$pass}' AND ownerSiteId = {$this->ownerSiteId} AND ownerOrgId = {$this->ownerOrgId}"));
		if ($user && $gotSocial)
		{
			if (!$gotSocial->userId)
			{
				// поставим в social id пользователя
				$gotSocial->userId = $user->id;
				$gotSocial = $sm->save($gotSocial);
			}
		}

		if ($user)
		{
			// авторизуем пользователя
			if ($user->isNotActive() && !$gotSocial)
			{
				Enviropment::redirect("userlogin", "Ваша регистрация не подтверждена, обратитесь к администратору");
			}

			if (($user->isBlocked() || $user->isDeleted()))
			{
				Enviropment::redirect("userlogin", "Ваш аккаунт заблокирован, обратитесь к администратору");
			}

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
			Enviropment::vBulletinLogin($this->ownerSiteId, $this->ownerOrgId, $this->host, $email, $password);

			// $redirMessage = "Для авторизации на форуме используйте Ваш e-mail и пароль";
			$redirMessage = "";

			$goto = Request::getVar("goto");
			if (!$goto)
				Enviropment::redirect("userarea", $redirMessage);
			else
				Enviropment::redirect(base64_decode($goto), $redirMessage);

		}

		// выявлен новый кейс, когда пользователь регался традиционно,
		// но не подтвердил аккаунт и теперь хочет войти с тем же мылом
		// только через соц. сеть
		// закрываем этот кейс следующим кодом
		if (!$gotSocial || !$user)
		{
			if ($um->getByLogin($this->ownerSiteId, $this->ownerOrgId, $email))
				Enviropment::redirect("userregister", "Пользователь с указанным E-Mail уже зарегистрирован");

			if ($um->getByNickName($this->ownerSiteId, $this->ownerOrgId, $nickName))
				Enviropment::redirect("userregister", "Указанный ник для сайта уже кем-то занят");
		}

		if ($phone1 && $phone1 != '' && $um->getByPhone1($this->ownerSiteId, $this->ownerOrgId, $phone1))
			Enviropment::redirect("userregister", "Указанный номер телефона уже кем-то занят");

		if (!$user)
		{
			// формируем массив данных
			$regData = compact("email", "nickName", "password", "lastName", "firstName", "secondName", "phone1", "phone2", "url", "name", "reqorg", "reqopt", "city");

			// начало транзакции
			$um->startTransaction();
			try
			{
				$getNewUser = $um->registerNewUser($this->ownerSiteId, $this->ownerOrgId, $regData);
			}
			catch (Exception $e)
			{
				$um->rollbackTransaction();
				Logger::error($e->getMessage());
				Enviropment::redirect("userregister", "Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке");
			}
			$um->commitTransaction();

			// проверим добавлен ли новый орг
			if (!$getNewUser)
			{
				Enviropment::redirect("userregister", "Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке");
			}
			else
			{
				// отправим код для подтверждения
				$this->sendEmail($getNewUser);

				// переход на страницу входа
				Enviropment::redirect("userlogin", "На указанный E-Mail отправлена ссылка для подтверждения регистрации");
			}
		}
		else
		{
			// случилось что-то не понятное
			Enviropment::redirect("userlogin", "Непредвиденная ошибка, обратитесь к администратору");
		}

	}


	/**
	* Функция отправляет письмо на е-майл нового покупателя
	* для подтвердении регистрации
	*
	* @param User $newUser орг
	*/
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
