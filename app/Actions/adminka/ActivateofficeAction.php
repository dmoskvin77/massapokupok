<?php
/**
* Действие БО для активации офиса
*/
class ActivateofficeAction extends AdminkaAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirect("manageoffices", "Не задан ID офиса");

		$om = new OfficeManager();
		$offObj = $om->getById($id);
		if (!$offObj)
			Adminka::redirect("manageoffices", "Офис не найден");

		if ($this->ownerSiteId != $offObj->ownerSiteId || $this->ownerOrgId != $offObj->ownerOrgId)
			Adminka::redirect("manageoffices", "Нет прав на выполнение данной операции");

		$offObj->status = Office::STATUS_ENABLED;
		$offObj = $om->save($offObj);

		Adminka::redirect("manageoffices", "Офис включен и доступен для выбора");

	}

}
