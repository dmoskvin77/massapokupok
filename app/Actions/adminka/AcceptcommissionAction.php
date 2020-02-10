<?php
/**
 * Действие БО для подтверждения прихода оплаты
 *
 */
class AcceptcommissionAction extends AdminkaAction
{
	public function execute()
	{
		// некоторые акшены могут выполняться только в базовой локации
		$baseSideId = intval(Configurator::get("application:baseSiteId"));
		if ($this->ownerSiteId != $baseSideId)
			Adminka::redirectBack("Нет прав на выполнение данного действия");

		$id = Request::getInt("id");
		if (!$id)
			Adminka::redirectBack("Не указан ID счета");

		$scm = new SiteCommisionManager();
		$scObj = $scm->getById($id);
		if (!$scObj)
			Adminka::redirectBack("Не найден счет");

		if ($scObj->status == SiteCommision::STATUS_NEW && $scObj->payAmount > 0)
		{
			// ок
		}
		else
			Adminka::redirectBack("Статус счёта не позволяет подтвердить оплату");

		$scObj->status = SiteCommision::STATUS_SUCCED;
		$scObj->dateConfirm = time();
		$scm->save($scObj);

		Adminka::redirect("managecommision", "Оплата счета подтверждена");

	}

}
