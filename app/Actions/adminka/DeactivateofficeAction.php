<?php
/**
* Действие БО для деактивации офиса
*/
class DeactivateofficeAction extends AdminkaAction
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

		$offObj->status = Office::STATUS_DISABLED;
		$offObj = $om->save($offObj);

		Adminka::redirect("manageoffices", "Офис выключен, его нельзя будет выбрать во время заказа, но выдавать товары в нём будет можно");

	}

}
