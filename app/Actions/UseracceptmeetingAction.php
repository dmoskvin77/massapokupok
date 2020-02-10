<?php
/**
 * Сохранение информации об оплате заказа
 *
 * id - meeting id
 *
*/

class UseracceptmeetingAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$meetId = FilterInput::add(new IntFilter("meetid", true, "ID встречи"));
		$pubId = FilterInput::add(new IntFilter("pubid", true, "ID сообщения"));

		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		$actor = $this->actor;
		$pbm = new PublicEventManager();
		$pubObj = $pbm->getById($pubId);
		if (!$pubObj)
			Enviropment::redirectBack("Сообщение не найдено");

		if ($this->ownerSiteId != $pubObj->ownerSiteId || $this->ownerOrgId != $pubObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		if ($pubObj->meetId != $meetId)
			Enviropment::redirectBack("Сообщение и встреча отличаются");

		$mtm = new MeetingManager();
		$meetObj = $mtm->getById($meetId);
		if (!$meetObj)
		{
			// надо удалить запись на несуществующую встречу
			$pbm->remove($pubId);
			Enviropment::redirectBack("Встреча не найдена");
		}

		// если бессмысленно записываться, т.к. время прошло
		if (time() >= $meetObj->finishTs)
		{
			$pbm->removeAllUnacceptedByMeetId($meetObj->id);
			Enviropment::redirectBack("К сожалению встреча уже состаялась без Вас, ожидайте объявление о записи на новую");
		}

		if ($meetObj->status == Meeting::STATUS_OVER)
		{
			$pbm->remove($pubId);
			Enviropment::redirectBack("Запись на встречу была завершена, пожалуйста ожидайте назначение новой встречи");
		}

		// не достигнуто ли ограничение на кол-во записавшихся
		if ($meetObj->userLimit > 0 && $meetObj->userCount + 1 > $meetObj->userLimit)
		{
			// удалить тут надо все публичные уведомления о встрече,
			// которые ещё не были приняты
			$pbm->removeAllUnacceptedByMeetId($meetObj->id);
			Enviropment::redirectBack("К сожалению был достигнут лимит участников, которые могли бы записаться на встречу");
		}

		// безопасный инкремент счетчика записавшихся
		$mtm->incrementUserAcceptedMeeting($meetObj->id);

		$pubObj->dateMeetAccept = time();
		$pubObj = $pbm->save($pubObj);

		// если участник записался "под конец" лимита
		// надо удалить уведомления другим, ещё не принявшим встречу
		if ($meetObj->userLimit > 0 && $meetObj->userCount + 1 == $meetObj->userLimit)
			$pbm->removeAllUnacceptedByMeetId($meetObj->id);

		Enviropment::redirectBack("Вы записаны на встречу");

	}

}
