<?php
/**
 * Контрол для добавления новой закупки (добавление головы)
 *
 */
class OrgAddZakupkaHeadControl extends AuthorizedOrgControl
{
	public $pageTitle = "Закупка";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actor", $actor);

		// если задан id то поднимаем закупку и смотрим что с ней можно делать
		$status = ZakupkaHeader::STATUS_NEW;
		$id = Request::getInt('id');
		if ($id)
		{
			$zhm = new ZakupkaHeaderManager();
			$zakObj = $zhm->getById($id);
			if (!$zakObj)
				Enviropment::redirectBack("Не найдена закупка");

			if ($zakObj->orgId != $actor->id || $this->ownerSiteId != $zakObj->ownerSiteId || $this->ownerOrgId != $zakObj->ownerOrgId)
				Enviropment::redirectBack("Нет прав на редактирование закупки");

			$status = $zakObj->status;
			$this->addData("zakObj", $zakObj);

		}

		// получить список статусов, доступных для данного текущего
		$allowedStatuses = ZakupkaHeader::getStatusDescCurrent($status);
		$this->addData("allowedStatuses", $allowedStatuses);

		// оптовики
		$om = new OptovikManager();
		$optList = $om->getByUserId($actor->id, Optovik::STATUS_ACTIVE);
		$this->addData("optList", $optList);
		if (!count($optList))
			Enviropment::redirect("orgoptlist/view/my", "У Вас пока нет ни одного закрепленного поставщика, добавьте пожалуйста.");

		// категории
		$cm = new CategoryManager();
		$ctl = $cm->get();
		$catList = array();
		if (count($ctl) > 0)
		{
			foreach ($ctl as $catItem)
				$catList[$catItem->id] = $catItem->name;
		}

		if (count($catList) > 0)
			$this->addData("catList", $catList);

		// валюты закупки
		$currency = array("руб" => "руб", "УЕ" => "УЕ");
		$this->addData("currency", $currency);

		// является ли аккаунт pro
		$prm = new ProManager();
		$this->addData("pro", $prm->checkPro($actor->id));

	}
}
