<?php
/**
 * Добавление заказа в ряд
 *
 * zlid - zakupkaLine id
 * val название размера в ряду, если пусто, то первое свободное, либо это был заказа в ряд с кол-вом
 *
*/

class UserplaceshortorderAction extends AuthorizedUserAction implements IPublicAction
{
	private $curActor = null;
	private $curLid = 0;
	private $curHeadid = 0;
	private $curMode = "basic";

	public function execute()
	{
		$lid = FilterInput::add(new IntFilter("zlid", true, "ID ряда"));
		$val = FilterInput::add(new StringFilter("val", false, "Место в ряду"));
		if ($val)
			$val = urldecode($val);

		if (!FilterInput::isValid())
			$this->redirectBackCover(FilterInput::getMessages());

		// режим просмотра рядов
		$this->curMode = Request::getVar('mode');
		if (!in_array($this->curMode, array('compact', 'short')))
			$this->curMode = "basic";

		$zlm = new ZakupkaLineManager();
		$zlineObj = $zlm->getById($lid);
		if (!$zlineObj)
			$this->redirectBackCover("Не найден ряд");

		if ($this->ownerSiteId != $zlineObj->ownerSiteId || $this->ownerOrgId != $zlineObj->ownerOrgId)
			Enviropment::redirectBackCover("Нет прав для выполнения данного действия");

		$zhm = new ZakupkaHeaderManager();
		$headObj = $zhm->getById($zlineObj->headId);
		if (!$headObj)
			$this->redirectBackCover("Не найдена закупка");

		if ($this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
			Enviropment::redirectBackCover("Нет прав для выполнения данного действия");

		if ($headObj->status != ZakupkaHeader::STATUS_ACTIVE && $headObj->status != ZakupkaHeader::STATUS_ADDMORE)
			$this->redirectBackCover("Статус закупки не позволяет добавить заказ");

		// товар, по которому построен ряд поднимаем
		$pm = new ProductManager();
		$prodObj = $pm->getById($zlineObj->productId);
		if (!$prodObj)
			$this->redirectBackCover("Не найден товар");

		if ($this->ownerSiteId != $prodObj->ownerSiteId || $this->ownerOrgId != $prodObj->ownerOrgId)
			Enviropment::redirectBackCover("Нет прав для выполнения данного действия");

		$ts = time();
		$actor = $this->actor;
		$this->curActor = $actor;
		$this->curLid = $lid;
		$this->curHeadid = $zlineObj->headId;

		// оргов надо возвращить только в их собственные закупки!
		if ($actor->id != $headObj->orgId)
			$this->curActor->isOrg = 0;

		$ohm = new OrderHeadManager();

		// $rp и $num - надо вычислить
		$rp = null;
		$num = null;

		$params = $zlm->getAvailablePosition($lid, $val);
		if (!$params)
		{
			$params = $zlm->getAvailablePosition($lid, $val, "-");
			if (!$params)
				$params = $zlm->getAvailablePosition($lid, $val, "+");
		}

		if (!$params)
			$this->redirectBackCover("Ошибка добавления заказа");

		$rp = $params['rp'];
		$num = $params['num'];

		// начало транзакции
		$ohm->startTransaction();
		try
		{
			$ohm->placeRowOrder($actor, $headObj, $zlineObj, $prodObj, $rp, $num, $this->ownerSiteId, $this->ownerOrgId);
		}
		catch (Exception $e)
		{
			$ohm->rollbackTransaction();
			Logger::error($e->getMessage());
			$this->redirectBackCover("Не удалось добавить заказ: ".$e->getMessage());
		}

		$ohm->commitTransaction();

		$this->redirectBackCover("Заказ добавлен");

	}

	// переадресация на якорь
	private function redirectBackCover($message)
	{
		if ($this->curActor && $this->curActor->isOrg)
		{
			if ($this->curHeadid > 0 && $this->curLid > 0)
				Enviropment::redirect("orgviewzakupka/mode/".$this->curMode."/headid/".$this->curHeadid."#r".$this->curLid, $message);
			else if ($this->curHeadid > 0)
				Enviropment::redirect("orgviewzakupka/mode/".$this->curMode."/headid/".$this->curHeadid, $message);
			else
				Enviropment::redirectBack($message);
		}
		else
		{
			if ($this->curHeadid > 0 && $this->curLid > 0)
				Enviropment::redirect("viewcollection/mode/".$this->curMode."/id/".$this->curHeadid."#r".$this->curLid, $message);
			else if ($this->curHeadid > 0)
				Enviropment::redirect("viewcollection/mode/".$this->curMode."/id/".$this->curHeadid, $message);
			else
				Enviropment::redirectBack($message);
		}

		return false;
	}

}
