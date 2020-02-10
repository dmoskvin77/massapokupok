<?php
/**
 * Действие БО для сохранения офиса
 */
class SaveOfficeAction extends AdminkaAction
{
	public function execute()
	{
		$id = FilterInput::add(new IntFilter("id", false, "id"));
		$name = FilterInput::add(new StringFilter("name", true, "Название"));
        $price = FilterInput::add(new StringFilter("price", false, "Стоимость доставки"));
        $price = round(floatval($price), 2);
        if ($price < 0)
            FilterInput::addMessage("Стоимость доставки должна быть положительной");

        $address = Request::getVar("address");
		if (mb_strlen($address) > 10000000)
			FilterInput::addMessage("Слишком длинный адрес");

		$schedule = Request::getVar("schedule");
		if (mb_strlen($schedule) > 10000000)
			FilterInput::addMessage("Слишком длинное расписание");

		if (!FilterInput::isValid())
		{
			FormRestore::add("form");
			Adminka::redirect("manageoffices", FilterInput::getMessages());
		}

		$om = new OfficeManager();
		$doAct = "Изменен ";
		if ($id)
		{
			$offObj = $om->getById($id);
			if (!$offObj)
				Adminka::redirect("manageoffices", "Офис не найден");

			if ($this->ownerSiteId != $offObj->ownerSiteId || $this->ownerOrgId != $offObj->ownerOrgId)
				Adminka::redirect("manageoffices", "Нет прав на выполнение данной операции");
		}
		else 
		{
			$offObj = new Office();
			$offObj->status = Office::STATUS_ENABLED;
			$offObj->ownerSiteId = $this->ownerSiteId;
			$offObj->ownerOrgId = $this->ownerOrgId;
			$doAct = "Добавлен ";
		}

		$offObj->name = $name;
        $offObj->price = $price;

		if ($address && $address != '')
			$offObj->address = $address;

		if ($schedule && $schedule != '')
			$offObj->schedule = $schedule;

		$offObj = $om->save($offObj);

		Adminka::redirect("manageoffices", $doAct."офис");

	}

}