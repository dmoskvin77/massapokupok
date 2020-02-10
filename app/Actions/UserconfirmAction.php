<?php
/**
* Действие для подтверждения регистрации пользователя
*
*/
class UserconfirmAction extends BaseAction implements IPublicAction
{
	public function execute()
	{
		$confirmCode = Request::getVar("code");
		if (!$confirmCode)
			Enviropment::redirect("userregister", "Не указан код подтверждения");
		
		$um = new UserManager();
		$currentUser = $um->getByConfirmCode($this->ownerSiteId, $this->ownerOrgId, $confirmCode);
		if ($currentUser)
		{
			if ($this->ownerSiteId != $currentUser->ownerSiteId || $this->ownerOrgId != $currentUser->ownerOrgId)
				Enviropment::redirectBack("Нет прав для выполнения данного действия");

			if ($currentUser->entityStatus == User::ENTITY_STATUS_NOTACTIVE)
			{
				// подтвердил, меняем статус
				$currentUser->entityStatus = User::ENTITY_STATUS_ACTIVE;
				$currentUser = $um->save($currentUser);

				// данные все готовим, в том числе и nickName
				$login = $currentUser->login;
				$nickName = $currentUser->nickName;
				$password = $currentUser->password;
				$salt = $currentUser->id."".substr(Utils::getGUID(), 0, rand(15, 20));

				$ts = time();

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

				// перед редиректом засунем инфу в базу форума
				$extDBConnData = Configurator::getSection($confSection);
				$vbConn = null;
				$vbConn = @mysql_connect($extDBConnData['host'].":".$extDBConnData['port'], $extDBConnData['user'], $extDBConnData['password']);
				if ($vbConn)
				{
					mysql_select_db($extDBConnData['database']);
					mysql_query("SET NAMES utf8", $vbConn);

					$sql = "INSERT INTO ".$extDBConnData['prefix']."users (group_id, username, password, salt, email, timezone, dst, registered) VALUES (3, '{$nickName}', '{$password}', '{$salt}', '{$login}', 2, 1, {$ts})";
					mysql_query($sql, $vbConn);

					mysql_close($vbConn);
				}

				Enviropment::redirect("userlogin", "Ваш аккаунт активирован, Вы можете войти на сайт");
			}
			else
			{
				if ($currentUser->entityStatus == User::ENTITY_STATUS_BLOCKED)
					Enviropment::redirect("userlogin", "Ваш аккаунт заблокирован, обратитесь к администратору");
				
				if ($currentUser->entityStatus == User::ENTITY_STATUS_DELETED)
					Enviropment::redirect("userlogin", "Ваш аккаунт удален, обратитесь к администратору");
					
				if ($currentUser->entityStatus == User::ENTITY_STATUS_PENDING)
					Enviropment::redirect("userlogin", "Ваш аккаунт ожидает модерации, пожалуйста подождите или обратитесь к администратору");
					
				if ($currentUser->entityStatus == User::ENTITY_STATUS_ACTIVE)
					Enviropment::redirect("userlogin", "Ваш аккаунт уже активен, Вы можете войти на сайт");
				
			}	
		}
		else
		{
			Enviropment::redirect("userregister", "Неверный код подтверждения");
		}

	}

}