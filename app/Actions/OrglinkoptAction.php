<?php
/**
* Действие для привязки поставщика
*
*/
class OrglinkoptAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$id = Request::getInt("id");
		$actor = $this->actor;
		if (!$id)
			Enviropment::redirectBack("Не указан ID поставщика");

		$op = new OptovikManager();
		$curOpt = $op->getById($id);
		if (!$curOpt)
			Enviropment::redirectBack("Поставщик не найден");

		if ($this->ownerSiteId != $curOpt->ownerSiteId || $this->ownerOrgId != $curOpt->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		if ($curOpt->userId && $curOpt->userId != $actor->id)
			Enviropment::redirectBack("Этот поставщик уже кем-то прикреплен");

		$curOpt->userId = $actor->id;
		$curOpt->status = Optovik::STATUS_ACTIVE;
		$curOpt->dateUpdate = time();
		$op->save($curOpt);

		Enviropment::redirectBack("Поставщик был прикреплен к Вам");

	}

}
