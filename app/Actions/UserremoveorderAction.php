<?php
/**
 * Удаление заказа
 *
 * oid - orderLine id
 *
*/

class UserremoveorderAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$oid = FilterInput::add(new IntFilter("oid", true, "ID строки заказа"));

		if (!FilterInput::isValid())
			Enviropment::redirectBack(FilterInput::getMessages());

		$olm = new OrderListManager();
		$olineObj = $olm->getById($oid);
		if (!$olineObj)
			Enviropment::redirectBack("Не найдена строка заказа");

		if ($this->ownerSiteId != $olineObj->ownerSiteId || $this->ownerOrgId != $olineObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		$zhm = new ZakupkaHeaderManager();
		$headObj = $zhm->getById($olineObj->headId);
		if (!$headObj)
			Enviropment::redirectBack("Не найдена закупка");

		if ($this->ownerSiteId != $headObj->ownerSiteId || $this->ownerOrgId != $headObj->ownerOrgId)
			Enviropment::redirectBack("Нет прав для выполнения данного действия");

		if ($headObj->status != ZakupkaHeader::STATUS_ACTIVE && $headObj->status != ZakupkaHeader::STATUS_ADDMORE && $headObj->status != ZakupkaHeader::STATUS_CHECKED)
			Enviropment::redirectBack("Статус закупки не позволяет удалить заказ");

		// если закупка уже проверена и заказа не в наличии, то можно удалить
		if (in_array($headObj->status, array(ZakupkaHeader::STATUS_CHECKED, ZakupkaHeader::STATUS_SEND, ZakupkaHeader::STATUS_DELIVERED, ZakupkaHeader::STATUS_CLOSED)) && $olineObj->status != OrderList::STATUS_REJECT)
			Enviropment::redirectBack("Статус закупки не позволяет удалить заказ");

		$actor = $this->actor;

		// найдем ряд, если заказ был сделан из ряда
		$zlObj = null;
		$newSizes = null;
		$zlm = new ZakupkaLineManager();
		if ($olineObj->zlId)
		{
			$zlObj = $zlm->getById($olineObj->zlId);
			if ($zlObj)
			{
				if ($zlObj->sizesChoosen && $zlObj->sizes)
				{
					$sizesChoosen = @unserialize($zlObj->sizesChoosen);
					$sizes = @unserialize($zlObj->sizes);
					if (count($sizesChoosen) && count($sizes))
					{
						$newSizes = array();
						foreach ($sizesChoosen AS $scKey => $scVal)
						{
							// очистка ячейки
							if (is_array($scVal) && $olineObj->rp.'_'.$olineObj->num == $scKey)
							{
								$preparedSzKey = str_replace('_'.$olineObj->num, '', $scKey);
								$newSizes[$scKey] = $sizes[$preparedSzKey];
							}
							else
								$newSizes[$scKey] = $scVal;

						}
					}
				}
			}
		}


		$ohm = new OrderHeadManager();

		// начало транзакции
		$ohm->startTransaction();
		try
		{
			// уменьшим кол-во заказов
			$headObj->orderCount = $headObj->orderCount - $olineObj->count;
			if ($headObj->orderCount < 0)
				$headObj->orderCount = 0;

			$zhm->save($headObj);

			$saveZlObj = false;
			// обновляем список покупателей ряда если это был ряд
			if ($zlObj && $newSizes)
			{
				$zlObj->sizesChoosen = @serialize($newSizes);
				$saveZlObj = true;
			}

			// уменьнаем кол-во заказанного если заказ в ряду с кол-вом
			if ($zlObj && $zlObj->minValue)
			{
				$zlObj->orderedValue = $zlObj->orderedValue - $olineObj->count;
				if ($zlObj->orderedValue < 0)
					$zlObj->orderedValue = 0;

				$saveZlObj = true;
			}

			if ($saveZlObj)
				$zlm->save($zlObj);

			$olm->remove($oid);

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
			Enviropment::redirectBack("Не удалось удалить заказ");
		}

		$ohm->commitTransaction();

		Enviropment::redirect("baskethead#bh".$olineObj->headId, "Заказ удален");

	}

}
