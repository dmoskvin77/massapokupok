<?php
/**
 * Орг меняют статусы заказов в закупке с "новый" на "подтвержден"
 *
 * headid - id закупки
 * mode - режим (allnewtoreject и allnewtoconfirm) - применимо к закупке в целом
 * (oneorderreject и oneorderconfirm)
 * zlorderid - id заказа
 *
*/

class OrgorderconfirmAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$mode = FilterInput::add(new StringFilter("mode", true, "Действие"));
		$headid = FilterInput::add(new IntFilter("headid", false, "ID закупки"));
		$zlorderid = FilterInput::add(new IntFilter("zlorderid", false, "ID заказа"));

		if (!$headid && !$zlorderid)
			FilterInput::addMessage("Не задан идентификатор");

		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		$actor = $this->actor;

		$orderLineObj = null;
		$olm = new OrderListManager();
		if ($zlorderid)
		{
			$orderLineObj = $olm->getById($zlorderid);
			if (!$orderLineObj)
				Enviropment::redirectBack("Не найден заказ");

			if ($this->ownerSiteId != $orderLineObj->ownerSiteId || $this->ownerOrgId != $orderLineObj->ownerOrgId)
				Enviropment::redirectBack("Нет прав на выполнение данного действия");

			$headid = $orderLineObj->headId;

			// проверим надо ли что-то делать
			if ($mode == "oneorderreject" && $orderLineObj->status == OrderList::STATUS_REJECT)
				Enviropment::redirectBack("Статус заказа уже нет в наличии");

			if ($mode == "oneorderconfirm" && $orderLineObj->status == OrderList::STATUS_CONFIRM)
				Enviropment::redirectBack("Статус заказа уже есть в наличии");

		}

		if (!$headid)
			Enviropment::redirectBack("Не задан ID закупки");

		$zhm = new ZakupkaHeaderManager();
		// найдём закупку, проверим того ли она орга
		$zhObj = $zhm->getById($headid);
		if (!$zhObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($zhObj->orgId != $actor->id || $this->ownerSiteId != $zhObj->ownerSiteId || $this->ownerOrgId != $zhObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав на выполнение данного действия");

		if (!in_array($zhObj->status, array(ZakupkaHeader::STATUS_STOP, ZakupkaHeader::STATUS_CHECKED, ZakupkaHeader::STATUS_SEND, ZakupkaHeader::STATUS_DELIVERED)))
			Enviropment::redirectBack("Статус закупки не подволяет менять наличие заказов");

		// стартуем транзакцию
		$isAction = false;
		$zhm->startTransaction();
		try
		{
			// режимы наличия по закупке в целом
			if ($mode == "allnewtoreject")
			{
				$zhm->rejectAllNewOrders($headid);
				$isAction = true;
			}

			if ($mode == "allnewtoconfirm")
			{
				$zhm->confirmAllNewOrders($headid);
				$isAction = true;
			}

			// режимы для проставления наличия по одному заказу
			if ($mode == "oneorderreject" && $orderLineObj)
			{
				$olm->rejectOrder($orderLineObj->id, $orderLineObj->userId, $orderLineObj->headId);
				$isAction = true;
			}

			if ($mode == "oneorderconfirm" && $orderLineObj)
			{
				$olm->confirmOrder($orderLineObj->id, $orderLineObj->userId, $orderLineObj->headId);
				$isAction = true;
			}

		}
		catch (Exception $e)
		{
			$zhm->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirectBack("Не удалось поменять статусы заказов");
		}

		$zhm->commitTransaction();

		if ($isAction)
			Enviropment::redirectBack("Операция выполнена");
		else
			Enviropment::redirectBack("Операция не выполнена");

	}

}
