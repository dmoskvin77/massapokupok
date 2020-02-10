<?php
/**
* Добавление камента к контенту
*
*/
class PlaceCOCommentAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		$body = Request::getVar("body");
		$headid = Request::getInt("headid");
		$sourceId = Request::getInt("sourceId");
		$rootId = Request::getInt("rootId");
		$messagetype = Request::getVar("messagetype");
		$gotcommtype = Request::getVar("commtype");
		$mode = Request::getVar("mode");

		if (!$actor)
			Enviropment::redirectBack("Вы должны быть авторизованы, чтобы оставить комментарий");

		if (!$body)
			Enviropment::redirectBack("Пустое сообщение не было добавлено");

		if ($mode == "answer" && intval($sourceId) == 0)
			Enviropment::redirectBack("Вы не выбрали сообщение на которое следует ответить");

		if ($gotcommtype == 'collection')
			$commType = CoComment::COMMENT_ZAKUPKA;
		else
			$commType = CoComment::COMMENT_CONTENT;

		$zakObj = null;

		// если всё нормально сохраняем камент
		$cocm = new CoCommentManager();

		// определение уровня нового камента
		$newLevel = null;
		if ($sourceId > 0)
		{
			$sourceMessage = $cocm->getByIdAndType($sourceId, $commType);
			if ($sourceMessage)
			{
				$newLevel = intval($sourceMessage->level) + 1;
				// а на своё ли сообщение мы отвечаем
				if ($this->ownerSiteId != $sourceMessage->ownerSiteId || $this->ownerOrgId != $sourceMessage->ownerOrgId)
					Enviropment::redirectBack("Нет прав на выполнение выбранного действия");
			}

			if ($newLevel > 30)
				Enviropment::redirectBack("Глубина комментария слишком велика, вместо ответа, напишите комментарий в корне");

			// достанем id закупки
			if ($mode == "answer" && $sourceMessage)
				$headid = $sourceMessage->headId;

		}

		if (!$headid)
			Enviropment::redirectBack("Не указан id страницы");

		if ($mode == "answer" && $sourceMessage)
		{
			$sourceMessage->wasRead = 1;
			$cocm->save($sourceMessage);
		}

		$newCOCom = new CoComment();
		$newCOCom->headId = $headid;
		$newCOCom->userId = $actor->id;
		$newCOCom->type = $commType;
		$newCOCom->ownerSiteId = $this->ownerSiteId;
		$newCOCom->ownerOrgId = $this->ownerOrgId;

		if ($newLevel)
			$newCOCom->level = $newLevel;

		if ($sourceId > 0)
		{
			$newCOCom->sourceId = $sourceId;
			if ($newLevel)
				$newCOCom->weight = $newLevel * 10 * $sourceId;

			if ($sourceMessage)
				$newCOCom->toId = $sourceMessage->userId;

		}

		// rootId - id головы цепочки
		if ($rootId > 0)
			$newCOCom->rootId = $rootId;

		// достраиваем цепочку к корню если ответили на сообщение из середины
		if (intval($rootId) == 0 && $mode == "answer" && $sourceMessage)
		{
			if ($sourceMessage->rootId)
				$newCOCom->rootId = $sourceMessage->rootId;
			else
				$newCOCom->rootId = $sourceMessage->id;
		}

		$isModerated = false;

		if ($messagetype == "anon")
			$newCOCom->isAnon = 1;

		if ($messagetype == "private")
			$newCOCom->isPrivate = 1;

        $newCOCom->nickName = $actor->nickName;

		if (!$actor->isAproved)
		{
			if ($messagetype == "private")
			{
				$isModerated = true;
				$newCOCom->status = CoComment::STATUS_MODERATED;
			}
			else
				$newCOCom->status = CoComment::STATUS_NEW;

		}

		if ($actor->isAproved || $commType == CoComment::COMMENT_ORDER)
		{
			$newCOCom->status = CoComment::STATUS_MODERATED;
			$isModerated = true;
		}

		if ($commType == CoComment::COMMENT_ZAKUPKA)
		{
			$zhm = new ZakupkaHeaderManager();
			$zakObj = $zhm->getById($headid);
			if ($zakObj)
			{
				// в своей закупке сообщение орга сразу же становится отмодерированным
				if ($actor->isOrg && $zakObj->orgId == $actor->id)
				{
					$newCOCom->status = CoComment::STATUS_MODERATED;
					$isModerated = true;
				}

			}
		}

		$newCOCom->body = $body;
		$newCOCom->dateCreate = time();

		$cocm->save($newCOCom);

		// сообщение участника было отмодерировано
		if ($zakObj && $newCOCom->userId != $newCOCom->toId && $newCOCom->toId && $isModerated && $commType == CoComment::COMMENT_ZAKUPKA)
		{
			$com = new CoNotificationManager();
			$com->saveAddNotification($this->ownerSiteId, $this->ownerOrgId, $zakObj->id, $newCOCom->toId, $zakObj->name);
		}

		// надо уведомить орга о новом каменте в его закупке
		if ($zakObj)
		{
			if ($zakObj->orgId != $actor->id)
			{
				$com = new CoNotificationManager();
				$com->saveAddNotification($this->ownerSiteId, $this->ownerOrgId, $zakObj->id, $zakObj->orgId, $zakObj->name);
			}
		}

		if (!$isModerated)
			Enviropment::redirectBack("Ваше сообщение будет показано после проверки модератором");
		else
			Enviropment::redirectBack("Ваше сообщение добавлено");

	}

}
