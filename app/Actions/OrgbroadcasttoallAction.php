<?php
/**
* Рассылка все участникам
*
*/
class OrgbroadcasttoallAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		// само сообщение
		$message = Request::getVar("message");
		if (!$message || $message == '')
			Enviropment::redirectBack("Сообщение не должно быть пустым");

		if (mb_strlen($message) > 10000000)
			Enviropment::redirectBack("Слишком длинное сообщение");

		$bcm = new BroadcastManager();
		$bcObj = new Broadcast();
		$bcObj->orgId = $actor->id;
		$bcObj->dateCreate = time();
		$bcObj->status = Broadcast::STATUS_NEW;
		$bcObj->message = $message;
		$bcObj->ownerSiteId = $this->ownerSiteId;
		$bcObj->ownerOrgId = $this->ownerOrgId;
		$bcm->save($bcObj);

		Enviropment::redirect("messages/mode/hidebroadcast", "Рассылка будет сделана после проверки администратором");

	}

}
