<?php
/**
 * Менеджер управления покупателями
 */
class UserManager extends BaseEntityManager
{
	/**
	* Функция возвращает покупателя с заданным логином
	*
	* @param string $login login пользователя
	* @return User
	*/
	public function getByLogin($ownerSiteId, $ownerOrgId, $login)
	{
		if ($login)
			return $this->getOne(new SQLCondition("login = '{$login}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
	}

	// поиск по точному совпадению номера телефона
	public function getByPhone1($ownerSiteId, $ownerOrgId, $phone1)
	{
		if ($phone1)
			return $this->getOne(new SQLCondition("phone1 = '{$phone1}' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
	}

	// кто онлайн
	public function getAllOnline($ownerSiteId, $ownerOrgId)
	{
		$tsTill = time() - 60 * 5;
		return $this->get(new SQLCondition("dateLastVisit > {$tsTill} AND entityStatus = ".User::ENTITY_STATUS_ACTIVE." AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
	}

	// сколько всего участников
	public function countAllUsers($ownerSiteId, $ownerOrgId, $orgsOnly = false)
	{
		$usersCount = 0;

		$sql21 = "SELECT COUNT(*) AS cnt FROM user WHERE entityStatus = ".User::ENTITY_STATUS_ACTIVE." AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		if ($orgsOnly)
			$sql21 .= " AND isOrg = 1";

		$res21 = $this->getOneByAnySQL($sql21);
		if ($res21)
			$usersCount = $usersCount + intval($res21['cnt']);

		return $usersCount;
	}

	// поиск участников по первым буквам ника
	public function searchUsers($ownerSiteId, $ownerOrgId, $q, $limit = 5)
	{
		$sql = new SQLCondition("nickName LIKE '{$q}%' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		$sql->orderBy = "isOrg DESC, nickName";
		$sql->offset = 0;
		$sql->rows = $limit;
		return $this->get($sql);
	}

	// поиск пользователя по сессии php
	public function getBySesId($phpsesid)
	{
		$sql = "SELECT id FROM user WHERE vkSes = '".$phpsesid."' LIMIT 1";

		$res = $this->getColumn($sql);
		if (isset($res[0]))
			$res = $this->getById($res[0]);

		return $res;
	}

	// список оптовиков организатора
	public function getOptList($orgId)
	{
		$orgId = intval($orgId);
		if ($orgId > 0)
		{
			$res = $this->get(new SQLCondition("orgId = ".$orgId." AND isOpt = 1 AND entityStatus = ".User::ENTITY_STATUS_ACTIVE));
			return $res;

		}
	}

	// список всех оптовиков
	public function getAllOpt($ownerSiteId, $ownerOrgId)
	{
		return $this->get(new SQLCondition("isOpt = 1 AND entityStatus = ".User::ENTITY_STATUS_ACTIVE." AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
	}

	// не  занятые оптовики
	public function getFreeOpt($ownerSiteId, $ownerOrgId, $userId)
	{
		return $this->get(new SQLCondition("isOpt = 1 AND (orgId IS NULL OR orgId = {$userId}) AND entityStatus = ".User::ENTITY_STATUS_ACTIVE." AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
	}

	// список оптовиков, ожидающих подтверждения статуса
	// (по всем сайтам - для админа)
	public function getPendingOpts()
	{
		return $this->get(new SQLCondition("isOpt IS NULL AND requestOpt = 1"));
	}


	// орги (ожидающие)
	public function getPendingOrgs()
	{
		return $this->get(new SQLCondition("isOrg IS NULL AND requestOrg = 1"));
	}

	/**
	 * Функция генерирует пароль для пользователя
	 *
	 * @return string
	 */
	public function generatePassword()
	{
		return strtolower(substr(Utils::getGUID(), 0, rand(6, 9)));
	}


	/**
	* Функция возвращает покупателя с заданным никнеймом
	*
	* @param string $login login пользователя
	* @return User
	*/
	public function getByNickName($ownerSiteId, $ownerOrgId, $nickName)
	{
		return $this->getOne(new SQLCondition("nickName = '".$nickName."' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
	}

	/**
	* Функция возвращает покупателя с заданным кодом подтверждения регистрации
	*
	* @param string confirmCode код подтверждения регистрации
	* @return User
	*/
	public function getByConfirmCode($ownerSiteId, $ownerOrgId, $confirmCode)
	{
		return $this->getOne(new SQLCondition("confirmCode = '".$confirmCode."' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}"));
	}

	// update profile
	public function updateUser($ownerSiteId, $ownerOrgId, $regData)
	{
		$actor = Context::getActor();

		if (!$actor)
			return null;

		try
		{
			$actor->lastName = $regData['lastName'];
			$actor->firstName = $regData['firstName'];
			$actor->secondName = $regData['secondName'];

			$actor->ownerSiteId = $ownerSiteId;
			$actor->ownerOrgId = $ownerOrgId;

			// название организации
			if (isset($regData['name']))
			{
				if ($regData['name']!= "")
					$actor->name = $regData['name'];
			}

			$actor->phone1 = $regData['phone1'];
			$actor->phone2 = $regData['phone2'];

			// требование на Орга и Оптовика
			if (isset($regData['reqorg']))
				if ($regData['reqorg'] == 'on')
					$actor->requestOrg = 1;

			if (isset($regData['reqopt']))
				if ($regData['reqopt'] == 'on')
					$actor->requestOpt = 1;

			$newUser = $this->save($actor);

		}
		catch (Exception $e)
		{
			throw new Exception("Не удалось отредактировать данные: " . $e->getMessage());
		}

		return $newUser;
	}


	// организатор добавляет оптовика
	public function addNewOpt($ownerSiteId, $ownerOrgId, $regData)
	{
		$user = new User();
		$user->ownerSiteId = $ownerSiteId;
		$user->ownerOrgId = $ownerOrgId;
		$user->dateCreate = time();
		$user->entityStatus = User::ENTITY_STATUS_ACTIVE;
		if (isset($regData['name']))
		{
			if ($regData['name']!= "")
				$user->name = $regData['name'];
		}

		$user->phone1 = $regData['phone1'];
		$user->isOpt = 1;

		$actor = Context::getActor();
		if ($actor)
			$user->orgId = $actor->id;

		$newUser = $this->save($user);
		return $newUser;

	}

	/**
	* Функция осуществляет регистрацию покупателя
	*
	* @param array $regData
	* @return User
	*/
	public function registerNewUser($ownerSiteId, $ownerOrgId, $regData)
	{
		$newUser = null;

		try
		{
			$nickName = $regData['nickName'];
			$nickName = str_replace("|", "", $nickName);
			$nickName = str_replace("#", "", $nickName);

			// нельзя быть админом
			$nickName = str_ireplace("admin", "", $nickName);
			$nickName = str_ireplace("аdmin", "", $nickName);
			// и модератором
			$nickName = str_ireplace("moderator", "", $nickName);
			$nickName = str_ireplace("moder", "", $nickName);

			$user = new User();
			$user->dateCreate = time();
			$user->dateDigest = time();
			$user->ownerSiteId = $ownerSiteId;
			$user->ownerOrgId = $ownerOrgId;

			if (isset($regData['password']))
			{
				if ($regData['password'] != "")
				{
					$user->password = md5(md5($regData['password']).sha1($regData['password']));
					$user->login = $regData['email'];
					$user->nickName = $nickName;
					$user->confirmCode = Utils::getGUID();
					$user->lastName = $regData['lastName'];
					$user->firstName = $regData['firstName'];
					$user->secondName = $regData['secondName'];
					$user->entityStatus = User::ENTITY_STATUS_NOTACTIVE;

					// название организации
					if (isset($regData['name']))
					{
						if ($regData['name']!= "")
							$user->name = $regData['name'];
					}

				}
			}

			$user->phone1 = $regData['phone1'];
			$user->phone2 = $regData['phone2'];

			// if ($regData['url'])
			// если ввели сайт, то надо создать новую сущность

			// требование на Орга и Оптовика
			if (isset($regData['reqorg']))
				if ($regData['reqorg'] == 'on')
					$user->requestOrg = 1;

			if (isset($regData['reqopt']))
				if ($regData['reqopt'] == 'on')
					$user->requestOpt = 1;

			$newUser = $this->save($user);

		}
		catch (Exception $e)
		{
			throw new Exception("Не удалось создать пользователя: " . $e->getMessage());
		}

		return $newUser;
	}

	/**
	 * Функция возвращает список покупателей по заданному
	 * списку идентификаторов
	 *
	 * @param array $userIds Список ID пользователей
	 * @param string $status Статус пользователей
	 * @return array of Users
	 */
	public function getList($userIds, $status = null)
	{
		if (count($userIds) == 0)
			return null;

		$statusSQL = "";
		if ($status != null)
			$statusSQL = " AND status = '{$status}' ";

		$ids = implode(", ", $userIds);
		$sql = new SQLCondition("id IN ({$ids})" . $statusSQL);
		$users = $this->get($sql);
		return $users;
	}


	/*
	 * Функция отдает список покупателей по массиву id
	 *
	 * @param array $ids
	 * @return array пользователей users
	 */
	public function getByIds($userIds)
	{
		if (!$userIds)
			return null;

		if (count($userIds) == 0)
			return null;

		$ids = implode(",", $userIds);
		$res = $this->get(new SQLCondition("id IN ($ids)", null, "id"));

		return Utility::sort($userIds, $res);
	}

	/*
	 * Функция отдает список всех покупателей
	 *
	 * @return array пользователей users
	 */
	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

	public function getAllActiveIds($ownerSiteId, $ownerOrgId)
	{
		$sql = "SELECT id FROM user WHERE entityStatus = ".User::ENTITY_STATUS_ACTIVE." AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$res = $this->getColumn($sql);
		if(!$res)
			return null;
	}

	/**
	 * Функция возвращает кол-во подтвержденных и неподтвержденных покупателей
	 *
	 * @return array
	 */
	public function getUsersTotal($ownerSiteId, $ownerOrgId, $tsCreateStart = 0, $tsCreateFinish = 0)
	{
		$activated = 0;
		$sql = "SELECT
					COUNT(*) AS total
				FROM
					user
				WHERE
					entityStatus = 1 AND isOrg IS NULL AND isOpt IS NULL AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

		if ($tsCreateStart > 0)
			$sql .= " AND dateCreate >= {$tsCreateStart} ";

		if ($tsCreateFinish > 0)
			$sql .= " AND dateCreate <= {$tsCreateFinish} ";

		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$activated = $res['total'];

		$nonactivated = 0;
		$sql = "SELECT
					COUNT(*) AS total
				FROM
					user
				WHERE
					entityStatus != 1 AND isOrg IS NULL AND isOpt IS NULL AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

        if ($tsCreateStart > 0)
            $sql .= " AND dateCreate >= {$tsCreateStart} ";

        if ($tsCreateFinish > 0)
            $sql .= " AND dateCreate <= {$tsCreateFinish} ";

		$res = $this->getOneByAnySQL($sql);
		if ($res)
			$nonactivated = $res['total'];

		return array('activated' => $activated, 'nonactivated' => $nonactivated);
	}


	// обновляет время последнего входа
	public function updateVisitTime($userId)
	{
		$sql = "UPDATE user SET dateLastVisit = ".time()." WHERE id = {$userId}";
		$this->executeNonQuery($sql);
		return true;
	}


	// обновляет время последнего входа бота
	public function updateBotVisitTime($userId)
	{
		$botTime = time() + 60*rand(5, 20);
		$sql = "UPDATE user SET dateLastVisit = ".$botTime." WHERE id = {$userId}";
		$this->executeNonQuery($sql);
		return true;
	}

	// получает время последнего визита пользователя
	public function getLastVisitTime($id)
	{
		$sql = "SELECT dateLastVisit FROM user WHERE id = {$id}";
		$res = $this->getColumn($sql);
		return date("Y-m-d H:i:s", intval($res[0]));

	}


	// фильтр по пользователям для админки
	public function getFilteredUserIds($ownerSiteId, $ownerOrgId, $filterArray)
	{
		// не выбран ни один фильтр - нет смысла продолжать
		if ($filterArray['basicfilter'] == 1)
		{
			$sql = "SELECT id FROM user WHERE ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ORDER BY id DESC";
			$res = $this->getColumn($sql);
			return $res;
		}

		$res = array();
		$allConditions = array();

		// 1 - фильтр выключен, 2 - включен
		if($filterArray["basicfilter"] == 2)
		{
			$allConditions[] = "ownerSiteId = {$ownerSiteId}";
			$allConditions[] = "ownerOrgId = {$ownerOrgId}";

			if($filterArray["id"] > 0)
				$allConditions[] = "id = {$filterArray["id"]}";

			if($filterArray["login"])
				$allConditions[] = "login like '%{$filterArray['login']}%'";

			if(isset($filterArray["nickName"]) && $filterArray["nickName"])
				$allConditions[] = "nickName like '%{$filterArray['nickName']}%'";

			if (count($allConditions) > 0)
				$allConditions = " WHERE ".implode(" AND ", $allConditions);

			$sql = "SELECT id FROM user {$allConditions} ORDER BY nickName";
			$res = $this->getColumn($sql);
			if(!$res)
				return null;

		}

		if (count($res) > 0)
			return $res;
		else
			return null;

	}

	// получить пачку пользователей для рассылки дайджеста
	// из крона, поэтому не делаем разбор по сайтам
	public function getDigestUsers($limit = 100)
	{
		$ts = time();
		$sql = new SQLCondition("dateDigest + digestFrequency * 24 * 3600 < {$ts} AND dateLastVisit > 0");
		$sql->offset = 0;
		$sql->rows = $limit;
		return $this->get($sql);

	}

}

