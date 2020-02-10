<?php
/**
 * Контрол загрузка рядов из внешнего файла
 *
 */
class OrgimportzakupkaControl extends AuthorizedOrgControl
{
	public $pageTitle = "Загрузка рядов из файла";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headid);
		if (!$zakObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение выбранного действия");

		// всё хорошо, покажем форму рассылки
		$this->addData("headid", $headid);
		$this->addData("zakObj", $zakObj);

		// для безопасности прикрутим к форме токен, который живёт 10 минут
		// у токена нет id в качестве первичного ключа, поэтому он будет лежать в табличке
		// а управление будет голыми запросами через спечиальный класс
		$token = Tocken::createTocken(Utils::getGUID(), Enviropment::getBasketGUID(), 'Orgimportzakupka', $actor->id, $this->ownerSiteId, $this->ownerOrgId);
		$this->addData("token", $token);

	}

}
