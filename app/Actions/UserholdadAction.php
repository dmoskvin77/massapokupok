<?php
/**
 * Скрыть объявление
 *
 * id
 *
*/

class UserholdadAction extends AuthorizedUserAction implements IPublicAction
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

		$actor = $this->actor;
		if ($adObj->userId != $actor->id || $this->ownerSiteId != $adObj->ownerSiteId || $this->ownerOrgId != $adObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		if ($adObj->status != BoardAd::STATUS_ACTIVE)
			Enviropment::redirectBack("Объявление не активно");

		$adObj->status = BoardAd::STATUS_HIDE;
		$adObj = $bam->save($adObj);

		// пересчитаем в категории и в типе кол-во объявлений, обновим данные
		$bam->rebuildCounters($adObj->typeId, $adObj->catId);

		Enviropment::redirectBack("Объявление скрыто, для повторного показа отредактируйте объявление");

	}

}
