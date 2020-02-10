<?php
/**
 * Добавление заказа, Редактирование заказа
 *
*/

class UseraddorderAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$headid = FilterInput::add(new IntFilter("headid", false, "ID закупки"));
		$orderid = FilterInput::add(new IntFilter("orderid", false, "ID заказа"));

		// TODO: надо передать сюда источник перехода (корзина или закупка), чтобы туда же вернуть

		$comment = Request::getVar("comment");
		if (mb_strlen($comment) > 10000000)
			FilterInput::addMessage("Слишком большой текст комментария");

		if (!$headid && !$orderid)
			FilterInput::addMessage("Не указаны параметры заказа");

		$headObj = null;
		$zhm = new ZakupkaHeaderManager();
		$olm = new OrderListManager();
		if ($orderid)
		{
			$olObj = $olm->getById($orderid);
			if (!$olObj)
				FilterInput::addMessage("Не найден заказ");
			else
			{
				if ($this->ownerSiteId != $olObj->ownerSiteId || $this->ownerOrgId != $olObj->ownerOrgId)
					FilterInput::addMessage("Нет прав для выполнения данного действия");

				if (!$headid)
				{
					$headObj = $zhm->getById($olObj->headId);
					if ($headObj)
					{
						if ($this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
							FilterInput::addMessage("Нет прав для выполнения данного действия");
					}
				}
			}
		}
		else
		{
			$olObj = new OrderList();
			$olObj->ownerSiteId = $this->ownerSiteId;
			$olObj->ownerOrgId = $this->ownerOrgId;
		}

		// на сколько меняется кол-во
		$deltaCount = 0;
		$firstCount = 0;

		// данные ряда
		$zlm = new ZakupkaLineManager();
		$zlObj = null;

		// надо сначала получить строку заказа, а потом уже понять все ли реквизиты
		// формы являются обязательными
		if ($olObj->zlId)
		{
			$count = $olObj->count;
			$firstCount = $olObj->count;

			// получить ряд
			$zlObj = $zlm->getById($olObj->zlId);
			if ($zlObj)
			{
				if ($this->ownerSiteId != $zlObj->ownerSiteId || $this->ownerOrgId != $zlObj->ownerOrgId)
					FilterInput::addMessage("Нет прав для выполнения данного действия");

				if ($zlObj->minValue)
					$count = FilterInput::add(new StringFilter("count", false, "Кол-во"));
			}

			$prodName = $olObj->prodName;
			$prodArt = $olObj->prodArt;
			$optPrice = $olObj->optPrice;
			$size = $olObj->size;
			$color = $olObj->color;
		}
		else
		{
			$prodName = FilterInput::add(new StringFilter("prodName", true, "Название"));
			$prodArt = FilterInput::add(new StringFilter("prodArt", false, "Артикул"));
			$optPrice = FilterInput::add(new StringFilter("optPrice", true, "Цена"));
			$count = FilterInput::add(new StringFilter("count", false, "Кол-во"));
			$size = FilterInput::add(new StringFilter("size", false, "Размер"));
			$color = FilterInput::add(new StringFilter("color", false, "Цвет"));
		}

		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		if (!$headObj && $headid)
			$headObj = $zhm->getById($headid);

		if (!$headObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
			Enviropment::redirectBack("Не достаточно прав для данного действия");

		if ($headObj->status != ZakupkaHeader::STATUS_ACTIVE && $headObj->status != ZakupkaHeader::STATUS_ADDMORE)
			Enviropment::redirectBack("Статус закупки не позволяет добавить заказ");


		$ts = time();
		$actor = $this->actor;
		$ohm = new OrderHeadManager();

		// начало транзакции
		$ohm->startTransaction();
		try
		{
			// добавить голову заказа, если её ещё нет
			$ohm->addNewOrder($this->ownerSiteId, $this->ownerOrgId, $actor->id, $headObj->id);
			$orderHeadObj = $ohm->getByUserZakId($actor->id, $headObj->id);
			if (!$orderHeadObj)
				throw new Exception("Не найден заказ");

			// добавить новую строку
			$olObj->orderId = $orderHeadObj->id;
			$olObj->userId = $actor->id;
			$olObj->orgId = $headObj->orgId;
			$olObj->headId = $headid;

			$olObj->prodName = $prodName;
			$olObj->prodArt = $prodArt;
			$olObj->optPrice = $optPrice;
			$olObj->count = $count;

			$olObj->size = $size;
			$olObj->color = $color;
			$olObj->comment = $comment;

			$olObj->status = OrderList::STATUS_NEW;
			$olObj->dateCreate = $ts;
			$olObj->dateUpdate = $ts;

			$olm->save($olObj);

			// увеличим кол-во заказов в закупке
			$headObj->orderCount = $headObj->orderCount + $count - $firstCount;
			if ($headObj->orderCount < 0)
				$headObj->orderCount = 0;

			$zhm->save($headObj);

			// кол-во у ряда по кол-ву так же уточним
			if ($zlObj && $zlObj->minValue)
			{
				$zlObj->orderedValue = $zlObj->orderedValue + $count - $firstCount;
				if ($zlObj->orderedValue < 0)
					$zlObj->orderedValue = 0;

				$zlm->save($zlObj);
			}

			// проапдейтить заказ
			// для этого запустим ф-ю полного пересчёта данных
			$ohm->rebuildOrder($actor->id, $headObj->id);

			// в очередь добавим задание пересчитать набранность закупки
			$qm = new QueueMysqlManager();
			$qm->savePlaceTask("calcnarate", null, null, $headObj->id);

		}
		catch (Exception $e)
		{
			$ohm->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirectBack("Не удалось записать заказ");
		}

		$ohm->commitTransaction();

		// куда вернуть пользователя
		$backURL = Request::getVar("backURL");
		if (!$backURL || $backURL == '')
			$backURL = "/viewcollection/id/{$headid}";
		else
			$backURL = urldecode($backURL);

		if (strpos($backURL, 'baskethead') !== false && $headid)
			$backURL .= "#bh".$headid;

		Context::setError("Заказ записан");
		Request::redirect($backURL);

	}

}
