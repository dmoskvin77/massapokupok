<?php
/**
 * Голосование за открытие закупки
 *
 * oheadid - orderHead id
 *
*/

class UservoteAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$headid = FilterInput::add(new IntFilter("headid", true, "ID закупки"));
		if (!FilterInput::isValid())
		{
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		$zhm = new ZakupkaHeaderManager();
		$headObj = $zhm->getById($headid);
		if (!$headObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($headObj->status != ZakupkaHeader::STATUS_VOTING)
			Enviropment::redirectBack("Статус закупки не позволяет проголосовать за неё");

		$actor = $this->actor;
		$zvm = new ZakupkaVoteManager();

		$votes = $zvm->getByUserIdAndZHId($actor->id, $headid);
		if ($votes)
			Enviropment::redirectBack("Вы уже голосовали за данную закупку");

		$voteObj = new ZakupkaVote();
		$voteObj->headId = $headid;
		$voteObj->orgId = $headObj->orgId;
		$voteObj->userId = $actor->id;
		$voteObj->ownerSiteId = $this->ownerSiteId;
		$voteObj->ownerOrgId = $this->ownerOrgId;
		$zvm->save($voteObj);

		Enviropment::redirectBack("Ваш голос принят");

	}

}
