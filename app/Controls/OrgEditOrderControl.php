<?php
/**
* Форма редактирования заказа участника организатором
*/
class OrgEditorderControl extends AuthorizedOrgControl
{
	public $pageTitle = "Редактирование заказа участника";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);

		$orderid = Request::getInt('orderid');
		$orlObj = null;
		$olm = new OrderListManager();
		if ($orderid)
		{
			$orlObj = $olm->getById($orderid);
			if ($orlObj)
				$this->addData("orlObj", $orlObj);
		}

		// поднимем закупку
		$zhObj = null;
		$zhm = new ZakupkaHeaderManager();
		if ($orlObj)
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

		if ($zhObj->orgId != $actor->id || $this->ownerSiteId != $zhObj->ownerSiteId || $this->ownerOrgId != $zhObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		// подготовим данные для вьюшки
		$this->addData("zhObj", $zhObj);

		// передать во вьюшку параметры
		$this->addData("headid", $zhObj->id);
		$this->addData("orderid", $orderid);

		// если заказ сделан из ряда, нодо поднять ряд
		if ($orlObj && $orlObj->zlId)
		{
			$zlm = new ZakupkaLineManager();
			$zlObj = $zlm->getById($orlObj->zlId);
			if ($zlObj)
				$this->addData("zlObj", $zlObj);
		}


	}

}
