<?php
/**
* Орг показывает ряд в закупке
*/

class OrgshowzlAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		// закупка
		$headid = Request::getInt("headid");
		// ряд
		$id = Request::getInt("id");
		if (!$id)
			Enviropment::redirectBack("Не указан ID ряда");

		$zlm = new ZakupkaLineManager();
		$zlineObj = $zlm->getById($id);
		if (!$zlineObj)
			Enviropment::redirectBack("Не найден ряд");

		$actor = $this->actor;
		if ($zlineObj->orgId != $actor->id || $this->ownerSiteId != $zlineObj->ownerSiteId || $this->ownerOrgId != $zlineObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$zlineObj->status = ZakupkaLine::STATUS_ACTIVE;

		$zlm->save($zlineObj);

		// всё готово
		Enviropment::redirectBack("Ряд опубликован");

	}

}
