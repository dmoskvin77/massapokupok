<?php
/**
* Действие для отвязки поставщика от орга если тот ему принадлежит
*
*/
class OrgunlinkoptAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		$id = Request::getInt("id");
		if (!$id)
			Enviropment::redirectBack("Не указан ID поставщика");

		$op = new OptovikManager();
		$curOpt = $op->getById($id);
		if (!$curOpt)
			Enviropment::redirectBack("Поставщик не найден");

		if ($curOpt->userId != $actor->id || $this->ownerSiteId != $curOpt->ownerSiteId || $this->ownerOrgId != $curOpt->ownerOrgId)
			Enviropment::redirectBack("Это не ваш поставщик, либо он уже был откреплен");

		$curOpt->status = Optovik::STATUS_FREE;
		$curOpt->dateUpdate = time();
		$op->save($curOpt);

		Enviropment::redirectBack("Поставщик был откреплен");


	}

}
