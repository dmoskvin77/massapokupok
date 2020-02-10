<?php
/**
 * Сохранение информации об оплате сайту
 *
 * scid - id счета
 *
*/

class OrgscpayAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$scid = FilterInput::add(new IntFilter("scid", true, "ID счёта"));
		$amount = FilterInput::add(new StringFilter("amount", true, "Сумма"));
		$userInfo = FilterInput::add(new StringFilter("userInfo", true, "Информация об оплате"));

		$amount = round($amount, 2);
		if ($amount <= 0)
			FilterInput::addMessage("Сумма должна больше нуля");

		$actor = Context::getActor();
		$scm = new SiteCommisionManager();
		$scObj = $scm->getById($scid);
		if (!$scObj)
			FilterInput::addMessage("Счёт не найден");

		if ($scObj->orgId != $actor->id || $this->ownerSiteId != $scObj->ownerSiteId || $this->ownerOrgId != $scObj->ownerOrgId)
			FilterInput::addMessage("Не достаточно прав на выполнение данного действия");

		if (!FilterInput::isValid())
		{
			FormRestore::add("sc-pay");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		$scObj->payAmount = $amount;
		$scObj->userInfo = $userInfo;
		$scm->save($scObj);

		Enviropment::redirect("orgcommision", "Информация об оплате отправлена, ожидается подтверждение");

	}

}
