<?php
/**
 * Авторизация через соцсеть
 * Если не сохранен ник и е-майл, то запросить их через форму регистрации,
 * а если они уже есть, то перекинуть в личный кабинет
*/
class SocialuauthControl extends IndexControl
{
	public $pageTitle = "Вход через социальную сеть";

	public function render()
	{
		if ($this->actor)
			Enviropment::redirect("userarea", "Вы уже авторизованы на сайте.");

		// $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
		$s = @file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=massapokupok.ru');

		if (!$s)
			Enviropment::redirect("userregister", "Не удалось войти через социальную сеть, пожалуйста зерегистрируйтесь.");

		$socUser = @json_decode($s, true);

		if (!$socUser || !is_array($socUser) || !isset($socUser['network']) || !isset($socUser['uid']))
			Enviropment::redirect("userregister", "Не удалось войти через социальную сеть, пожалуйста зерегистрируйтесь.");


		$um = new UserManager();

		$sm = new SocialManager();

		// поиск записи в БД
		$gotSocial = $sm->getByUid($this->ownerSiteId, $this->ownerOrgId, $socUser['uid'], $socUser['network']);

		if (!$gotSocial)
		{
			$gotSocial = new Social();
			$gotSocial->network = $socUser['network'];
			$gotSocial->first_name = $socUser['first_name'];
			$gotSocial->last_name = $socUser['last_name'];
			$gotSocial->profile = $socUser['profile'];
			$gotSocial->uid = intval($socUser['uid']);
			$gotSocial->identity = $socUser['identity'];
			$gotSocial->ownerSiteId = $this->ownerSiteId;
			$gotSocial->ownerOrgId = $this->ownerOrgId;
			$gotSocial = $sm->save($gotSocial);
		}
		else
		{
			// апдейтим данные
			$gotSocial->first_name = $socUser['first_name'];
			$gotSocial->last_name = $socUser['last_name'];
			$gotSocial->profile = $socUser['profile'];
			$gotSocial->identity = $socUser['identity'];
			$sm->save($gotSocial);

			// если пользователь уже приходил на сайт,
			// то авторизуем его
			if ($gotSocial->userId)
			{
				$user = $um->getById($gotSocial->userId);
				if ($user)
				{
					if (($user->isBlocked() || $user->isDeleted()))
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
					Enviropment::vBulletinLogin($this->ownerSiteId, $this->ownerOrgId, $this->host, $user->login, $user->password);

					// $redirMessage = "Для авторизации на форуме используйте Ваш e-mail и пароль";
					$redirMessage = "";

					$goto = Request::getVar("goto");
					if (!$goto)
						Enviropment::redirect("userarea", $redirMessage);
					else
						Enviropment::redirect(base64_decode($goto), $redirMessage);

				}

			}

		}

		// это новичёк
		// передаем в шаблон network и uid
		$this->addData("network", $socUser['network']);
		$this->addData("uid", intval($socUser['uid']));

		/*
		 * Примеры:
		 *
		 * vk:
		 * Array ( [network] => vkontakte [first_name] => Maxim [last_name] => Smirnov [profile] => http://vk.com/id210587411 [uid] => 210587411 [identity] => http://vk.com/id210587411 )
		 *
		 * mail.ru:
		 * Array ( [network] => mailru [identity] => http://my.mail.ru/mail/maximusmirno/ [uid] => 7471094375127783517 [first_name] => Макс [last_name] => Смирнов [profile] => http://my.mail.ru/mail/maximusmirno/ )
		 *
		 * одноклассники:
		 * Array ( [network] => odnoklassniki [identity] => http://odnoklassniki.ru/179443229979 [uid] => 179443229979 [profile] => http://www.odnoklassniki.ru/profile/462951309853 [first_name] => Denis [last_name] => Moskvin )
		 *
		 * facebook.com:
		 * Array ( [network] => facebook [first_name] => Denis [last_name] => Moskvin [profile] => https://www.facebook.com/dmoskvin77 [uid] => 100000026136733 [identity] => https://www.facebook.com/dmoskvin77 )
		 *
		 */

		// $user['network'] - соц. сеть, через которую авторизовался пользователь
		// $user['identity'] - уникальная строка определяющая конкретного пользователя соц. сети
		// $user['first_name'] - имя пользователя
		// $user['last_name'] - фамилия пользователя

	}

}
