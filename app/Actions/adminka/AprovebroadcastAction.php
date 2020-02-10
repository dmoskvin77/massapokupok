<?php
/**
* Действие БО для одобрения рассылки
*/
class AprovebroadcastAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("managebroadcast", "Не задан ID рассылки");

		$bcm = new BroadcastManager();
		$bcObj = $bcm->getById($id);
		if (!$bcObj)
			Adminka::redirect("managebroadcast", "Рассылка не найдена");

		if ($this->ownerSiteId != $bcObj->ownerSiteId || $this->ownerOrgId != $bcObj->ownerOrgId)
			Adminka::redirect("managebroadcast", "Нет прав на выполнение выбранного действия");

		if ($bcObj->status != Broadcast::STATUS_NEW)
			Adminka::redirect("managebroadcast", "Статус объявления не позволяет его одобрить");

		// можно забирать кроном и отправлять
		$bcObj->status = Broadcast::STATUS_APPROVED;
		$bcObj = $bcm->save($bcObj);

		Adminka::redirect("managebroadcast", "Рассылка одобрена");

	}

}
