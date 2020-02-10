<?php
/**
 * Удаление объявления
 *
 * id
 *
*/

class UserremoveadAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$id = FilterInput::add(new IntFilter("id", true, "ID объявления"));
		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		$bam = new BoardAdManager();
		$adObj = $bam->getById($id);
		if (!$adObj)
			Enviropment::redirectBack("Не найдено объявление");

		if ($this->ownerSiteId != $adObj->ownerSiteId || $this->ownerOrgId != $adObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		$actor = $this->actor;
		if ($adObj->userId != $actor->id)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		$adObj->status = BoardAd::STATUS_DELETE;
		$adObj = $bam->save($adObj);

		// пересчитаем в категории и в типе кол-во объявлений, обновим данные
		$bam->rebuildCounters($adObj->typeId, $adObj->catId);

		Enviropment::redirectBack("Объявление удалено");

	}

}
