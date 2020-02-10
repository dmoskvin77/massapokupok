
<?php
/**
* Отправить сообщене в личку другому пользователю
*
*/
class SendpvtmessageAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		// кому отправляем сообщение
		$userId = Request::getInt("userId");
		if (!$userId)
			Enviropment::redirectBack("Не выбран получатель");

		$actor = $this->actor;
		// автор сообщения
		$actorId = $actor->id;
		if ($userId == $actorId)
			Enviropment::redirectBack("Нельзя отправить сообщение самому себе");

		$um = new UserManager();
		$userObj = $um->getById($userId);
		if (!$userObj)
			Enviropment::redirectBack("Не найден адресат");

		if ($this->ownerSiteId != $userObj->ownerSiteId || $this->ownerOrgId != $userObj->ownerOrgId)
			Enviropment::redirectBack("Выбранный собеседник не из Вашей матрицы!");

		// само сообщение
		$message = Request::getVar("message");
		if (!$message || $message == '')
			Enviropment::redirectBack("Сообщение не должно быть пустым");

		if (mb_strlen($message) > 10000000)
			Enviropment::redirectBack("Слишком длинное сообщение");

		$dlgId = Request::getInt("dlgId");

		$mdm = new MessageDialogueManager();
		$mdm->addMessage($this->ownerSiteId, $this->ownerOrgId, $actorId, $userId, $message);

		if ($dlgId)
			Enviropment::redirect("messages/userid/{$userId}/dlgid/{$dlgId}", "Сообщение отправлено");
		else
			Enviropment::redirect("messages", "Сообщение отправлено");

	}

}
