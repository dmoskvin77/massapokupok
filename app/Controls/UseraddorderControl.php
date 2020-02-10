<?php
/**
* Форма добавления и редактирования заказа
*/
class UseraddorderControl extends AuthorizedUserControl
{
	public $pageTitle = "Заказать";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);

		$headid = Request::getInt('headid');
		$orderid = Request::getInt('orderid');

		$orlObj = null;
		$olm = new OrderListManager();
		if ($orderid)
		{
			$orlObj = $olm->getById($orderid);
			if ($orlObj)
			{
				if ($this->ownerSiteId != $orlObj->ownerSiteId || $this->ownerOrgId != $orlObj->ownerOrgId)
					Enviropment::redirectBack("Нет прав на выполнение данного действия");

				$this->addData("orlObj", $orlObj);
			}
		}

		// поднимем закупку
		$zhObj = null;
		$zhm = new ZakupkaHeaderManager();
		if ($orlObj && !$headid)
		{
			$zhObj = $zhm->getById($orlObj->headId);
			$headid = $orlObj->headId;
		}

		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		if (!$zhObj && $headid)
			$zhObj = $zhm->getById($headid);

		if (!$zhObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($this->ownerSiteId != $zhObj->ownerSiteId || $this->ownerOrgId != $zhObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		// подготовим данные для вьюшки
		$this->addData("zhObj", $zhObj);

		// передать во вьюшку параметры
		$this->addData("headid", $headid);
		$this->addData("orderid", $orderid);

		// если заказ сделан из ряда, нодо поднять ряд
		if ($orlObj && $orlObj->zlId)
		{
			$zlm = new ZakupkaLineManager();
			$zlObj = $zlm->getById($orlObj->zlId);
			if ($zlObj)
				$this->addData("zlObj", $zlObj);
		}

		// передадим откуда приходит участник
		$backURL = "";
		// костыль для IE7,8 , т.к. некорректно определяется HTTP_REFERER
		if (Enviropment::isMSIE())
			$backURL = Enviropment::getReferef();
		else
			$backURL = Request::prevUri();

		$this->addData("backURL", urlencode($backURL));

	}

}
