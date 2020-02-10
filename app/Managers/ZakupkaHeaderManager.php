<?php
/**
 * Менеджер управления закупками
 */
class ZakupkaHeaderManager extends BaseEntityManager
{
	// список ожидающих модерации закупок
	public function getPending($ownerSiteId, $ownerOrgId)
	{
		$condition = "status = '".ZakupkaHeader::STATUS_VOTING."' AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}";
		$sql = new SQLCondition($condition);
		return $this->get($sql);
	}


	// все новые заказы закупки повернуть в "нет в наличии"
	public function rejectAllNewOrders($headid)
	{
		$sql = "UPDATE `orderList` SET status = '".OrderList::STATUS_REJECT."' WHERE headId = {$headid} AND status = '".OrderList::STATUS_NEW."'";
		$this->executeNonQuery($sql);

		// пересчет всех заказов
		// пересчет набранности закупки встроен в ребилд!
		$actor = Context::getActor();
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask("rebuildzakupkaorders", $actor->id, $actor->nickName, $headid);

		return true;

	}


	// все новые заказы закупки повернуть в "подтвержден"
	public function confirmAllNewOrders($headid)
	{
        $ts = time();
		$sql = "UPDATE `orderList` SET status = '".OrderList::STATUS_CONFIRM."', dateConfirm = {$ts} WHERE headId = {$headid} AND status = '".OrderList::STATUS_NEW."'";
		$this->executeNonQuery($sql);

		// пересчет всех заказов
		// пересчет набранности закупки встроен в ребилд!
		$actor = Context::getActor();
		$qm = new QueueMysqlManager();
		$qm->savePlaceTask("rebuildzakupkaorders", $actor->id, $actor->nickName, $headid);

		return true;

	}


	// id закупок на главную
	public function getMainModeratedIds($ownerSiteId, $ownerOrgId, $mode = null, $cat = null)
	{
		$sql = "SELECT DISTINCT
					zh.id
				FROM
					zakupkaHeader AS zh ";

		if ($mode == 'done')
			$sql = $sql." WHERE zh.status IN ('STATUS_STOP', 'STATUS_CHECKED', 'STATUS_SEND', 'STATUS_DELIVERED', 'STATUS_CLOSED') ";
		else if ($mode == 'voting')
			$sql = $sql." WHERE zh.status IN ('STATUS_VOTING') ";
		else
			$sql = $sql." WHERE zh.status IN ('STATUS_ACTIVE', 'STATUS_ADDMORE') ";

		if ($cat)
			$cat = intval($cat);

		if ($cat)
			$sql = $sql." AND (zh.categoryId1 = {$cat} OR zh.categoryId2 = {$cat} OR zh.categoryId3 = {$cat}) ";

		$sql = $sql."  AND ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";
		$sql = $sql." ORDER BY
					zh.startDate DESC, zh.id DESC";

		$res = $this->getColumn($sql);

		return $res;

	}


	// получить объекты по id
	public function getByIds($ZHIds, $orgId = null)
	{
		if (!$ZHIds)
			return null;

		if (count($ZHIds) == 0)
			return null;

		$additionalCondition = "";
		if ($orgId)
			$additionalCondition = " AND orgId = {$orgId}";

		$ids = implode(",", $ZHIds);
		$res = $this->get(new SQLCondition("id IN ($ids){$additionalCondition}", null, "id"));

		$res = Utility::sort($ZHIds, $res);

		return $res;
	}


	// запрос по id головы и id организатора
	public function getByIdAndOrgId($id, $orgId)
	{
		$id = intval($id);
		$orgId = intval($orgId);
		if ($id > 0 && $orgId > 0)
		{
			$condition = "id = {$id} AND orgId = {$orgId}";
			$sql = new SQLCondition($condition);
			return $this->getOne($sql);
		}
	}


	// запрос по id организатора
	public function getByOrgId($orgId, $status = null)
	{
		$orgId = intval($orgId);
		if ($orgId > 0)
		{
			if ($status)
				$condition = "orgId = {$orgId} AND status = '{$status}'";
			else
				$condition = "orgId = {$orgId}";

			$sql = new SQLCondition($condition);
			return $this->get($sql);

		}
	}


	// запрос по id организатора
	public function getActualByOrgId($orgId, $status = null)
	{
		$orgId = intval($orgId);

		if ($status == null)
			$status = ZakupkaHeader::STATUS_NEW;

		if ($orgId > 0)
		{
			if ($status == ZakupkaHeader::STATUS_ACTIVE)
				$condition = "orgId = {$orgId} AND status IN ('STATUS_ACTIVE', 'STATUS_ADDMORE')";
			else
				$condition = "orgId = {$orgId} AND status = '".$status."'";

			$sql = new SQLCondition($condition);
			$sql->orderBy = "startDate DESC, dateCreate DESC";

			return $this->get($sql);
		}
	}


	// пересчет суммы набранности закупки
	public function orgCalcNarate($headId)
	{
		// данным доверяем, т.к. их отправил нам код

		/*
		надо посчитать и заполнить у закупки сл. поля:
		curAmount - сумма на сколько заказано подтвержденных заказов
		curValue - кол-во подствержденных заказов
		dateAmount - ts когда сделан пересчет суммы
		dateValue - ts пересчета кол-ва
		narate - процент набранности закупки
		 */

		$ts = time();

		$curAmount = 0;
		$curValue = 0;
		$dateAmount = $ts;
		$dateValue = $ts;
		$narate = 0;

		// для расчёта narate поднимем закупку, к тому же в неё будем сохранять данные
		$zhm = new ZakupkaHeaderManager();
		$zakObj = $zhm->getById($headId);
		if (!$zakObj)
			return true;

		$sql = "SELECT SUM(optAmount) AS sumAmount, confirmedProdCount AS sumValue FROM orderHead WHERE headId = {$headId}";
		$res = $this->getOneByAnySQL($sql);
		if ($res)
		{
			$curAmount = intval($res['sumAmount']);
			$curValue = intval($res['sumValue']);
		}

		// расчет narate
		if ($zakObj->minAmount > 0 && $curAmount > 0)
			$narate = round($curAmount / $zakObj->minAmount * 100, 2);
		else if ($zakObj->minValue > 0 && $curValue > 0)
			$narate = round($curValue / $zakObj->curAmount * 100, 2);

		if ($narate > 100)
			$narate = 100;

		// сохранение данных
		$zakObj->curAmount = $curAmount;
		$zakObj->curValue = $curValue;
		$zakObj->dateAmount = $dateAmount;
		$zakObj->dateValue = $dateValue;
		$zakObj->narate = $narate;
		$zhm->save($zakObj);

		return true;
	}


	// распределение стоимости доставки по заказам от общей суммы закупки пропорционально
	public function setDeliveryAmount($headid, $curZakAmount, $amount)
	{
		// по отношению к головам заказов
		$sql1 = "UPDATE orderHead SET opttoorgDlvrAmount = optAmount / {$curZakAmount} * {$amount} WHERE headId = {$headid}";
		$this->executeNonQuery($sql1);

		// по отношению с строкам заказов
		$sql2 = "UPDATE orderList SET opttoorgDlvrAmount = optPrice * count / {$curZakAmount} * {$amount} WHERE headId = {$headid}";
		$this->executeNonQuery($sql2);

		// поставим сумму и в саму закупку
		$sql3 = "UPDATE zakupkaHeader SET opttoorgDlvrAmount = {$amount} WHERE id = {$headid}";
		$this->executeNonQuery($sql3);

		return true;

	}


	// добавление новой закупки (заголовка),
	// либо редактирование закупки
	public function addNewZH($ownerSiteId, $ownerOrgId, $addData, $headId = null)
	{
		$ts = time();
		$actor = Context::getActor();


        $oldOrgRate = 0;
		if ($headId) {
			$newZH = $this->getById($headId);
			if ($newZH) {
				$oldOrgRate = $newZH->orgRate;
			}
		}
		else
		{
			$newZH = new ZakupkaHeader();
			$newZH->entityStatus = ZakupkaHeader::ENTITY_STATUS_PENDING;
			$newZH->dateCreate = $ts;
			$newZH->ownerSiteId = $ownerSiteId;
			$newZH->ownerOrgId = $ownerOrgId;
		}

		$newZH->name = $addData['name'];
		if (isset($addData['catid1']) && $addData['catid1'])
			$newZH->categoryId1 = $addData['catid1'];

		if (isset($addData['catid2']) && $addData['catid2'])
			$newZH->categoryId2 = $addData['catid2'];

		if (isset($addData['catid3']) && $addData['catid3'])
			$newZH->categoryId3 = $addData['catid3'];

		$newZH->orgId = $addData['orgId'];
		$newZH->optId = $addData['optId'];
        $newZH->orgRate = $addData['orgRate'];
		$newZH->minAmount = $addData['minAmount'];
		$newZH->minValue = $addData['minValue'];
		$newZH->useForm = $addData['useForm'];
		$newZH->usePay = $addData['usePay'];
		$newZH->usePay = $addData['usePay'];
		$newZH->description = $addData['description'];
		$newZH->specialNotes = $addData['specialNotes'];

		// дата обновления
		$newZH->dateUpdate = $ts;

		if (isset($addData['currency']))
			$newZH->currency = $addData['currency'];

		if ($addData['status'] && $headId)
			$newZH->status = $addData['status'];

		if (!$headId)
			$newZH->status = ZakupkaHeader::STATUS_NEW;

		// это менеджер ассинхронных задач
		$qm = new QueueMysqlManager();

		// произошла смена статуса закупки
		if ($addData['oldStatus'] != $addData['status'])
		{
			// можно здесь поймать и открытие закупки,
			// например сделать рассылку уведомлений заинтересованным

			// произошло открытие закупки
			if ($addData['status'] == ZakupkaHeader::STATUS_ACTIVE)
			{
				if (!$newZH->startDate && $headId)
				{
					if ($newZH->vikupId)
					{
						// надо разослать подписчикам уведомления о том,
						// что открылся выкуп регулярной закупки
						$qm->savePlaceTask("vikupzhstart", $actor->id, $actor->nickName, $headId, $newZH->name, null, $newZH->vikupId, $ts);
					}

					// поставим дату открытия закупки
					$newZH->startDate = $ts;
				}
			}

			// произошёл стоп
			if ($addData['status'] == ZakupkaHeader::STATUS_STOP)
			{
				// первый раз
				if (!$newZH->validDate)
				{
					$newZH->validDate = $ts;
					// если у закупки есть выкуп, то увеличим кол-во выкупов на единицу безопасно
					if ($newZH->vikupId)
					{
						$vm = new ZakupkaVikupManager();
						$vm->saveIncrement($newZH->vikupId);
					}
				}
				// надо уведомить участников закупки о смене статуса
				// для этого поставим такс в очередь
				if ($headId)
				{
					$qm = new QueueMysqlManager();
					$qm->savePlaceTask("stopzakupka", $actor->id, $actor->nickName, $headId, $newZH->name, null, null, $ts);
				}
			}

			// закупка проверена
			if ($addData['status'] == ZakupkaHeader::STATUS_CHECKED)
			{
				// надо уведомить участников закупки о смене статуса
				// для этого поставим такс в очередь
				if ($headId)
				{
					$qm = new QueueMysqlManager();
					$qm->savePlaceTask("checkzakupka", $actor->id, $actor->nickName, $headId, $newZH->name, null, null, $ts);

					// а ещё надо выставить комиссию организатору
					// т.е. счёт, который орг будет оплачивать за использование движка сайта
					$scm = new SiteCommisionManager();
					// поиск уже существующего счета
					$stComObj = $scm->getByHeadId($headId, SiteCommision::TYPE_ZAK);
					// счет добавляется только один раз
					if (!$stComObj)
					{
						$stComObj = new SiteCommision();
						$stComObj->orgId = $addData['orgId'];
						$stComObj->headId = $headId;
						$stComObj->dateCreate = time();
						$stComObj->type = SiteCommision::TYPE_ZAK;
						$stComObj->status = SiteCommision::STATUS_NEW;
						$stComObj->ownerSiteId = $ownerSiteId;
						$stComObj->ownerOrgId = $ownerOrgId;
						// включаем кнопку "Оплатить" в корзинах участников (если орг забыл)
						$newZH->usePay = 'on';
						// самое интересное
						// по-честному пересчитываем набранность закупки
						$newZH = $this->save($newZH);
						$this->orgCalcNarate($headId);
						$newZH = $this->getById($headId);
						// два процента
						// здесь needAmount - в контексте сколько взять с орга
						$orgPersent = $actor->orgPersent;
						if (!$orgPersent) {
							// берём значение из настроек
							$orgPersent = SettingsManager::getValue($ownerSiteId, $ownerOrgId, 'orgcommision');
						}
						$stComObj->orgPersent = $orgPersent;
						$stComObj->baseAmount = round($newZH->curAmount, 2);
						$stComObj->needAmount = round($newZH->curAmount / 100 * $orgPersent, 2);
						$scm->save($stComObj);
					}
				}
			}

			// закупка доставлена
			if ($addData['status'] == ZakupkaHeader::STATUS_DELIVERED) {
				// надо уведомить участников закупки о смене статуса
				// для этого поставим такс в очередь
				if ($headId) {
					$qm->savePlaceTask("dlvrzakupka", $actor->id, $actor->nickName, $headId, $newZH->name, null, null, $ts);
				}
			}
		}

		$newZH = $this->save($newZH);
        $headId = $newZH->id;
		// запишем в лог смену статуса
		if (!isset($addData['oldStatus']) || $addData['oldStatus'] != $addData['status'])
		{
			$zlm = new ZakupkaStatusLogManager();
			$zlogObj = new ZakupkaStatusLog();
			$zlogObj->headId = $newZH->id;
			$zlogObj->status = $newZH->status;
			$zlogObj->dateCreate = time();
			$zlogObj->ownerSiteId = $ownerSiteId;
			$zlogObj->ownerOrgId = $ownerOrgId;
			$zlm->save($zlogObj);
		}

        // смена orgRate в заказах
        if ($oldOrgRate != $addData['orgRate']) {
            $ohm = new OrderHeadManager();
            $ohm->setOrgRateByHeadId($headId, $addData['orgRate']);
        }

		return $newZH;
	}


	// удаление закупки
	public function removeZakupka($id)
	{
		// удалить строки заказа
		$olm = new OrderListManager();
		$olm->removeByHeadId($id);
		// удалим ряды
		$zlm = new ZakupkaLineManager();
		$zlm->removeByHeadId($id);
		// саму закупку
		$this->remove($id);
	}


	// список закупок по дате создания сгруппированные по статусам
	public function getAllGroupedStatus($orgId)
	{
		$orgId = intval($orgId);
		if (!$orgId) {
			return false;
		}

		$sql = "SELECT * FROM zakupkaHeader WHERE orgId = ".$orgId." ORDER BY status, dateCreate, startDate";
		$res = $this->getByAnySQL($sql);

		return $res;
	}

    // список закупок для админки
	public function getAllIds($ownerSiteId, $ownerOrgId)
	{
        $sql = "SELECT DISTINCT id FROM zakupkaHeader WHERE ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ORDER BY dateCreate DESC";
        $res = $this->getColumn($sql);

        return $res;
	}

    // сколько всего закупок
    public function getZakTotal($ownerSiteId, $ownerOrgId, $tsCreateStart = 0, $tsCreateFinish = 0)
    {
        $created = 0;
        $sql = "SELECT
					COUNT(*) AS total
				FROM
					zakupkaHeader
				WHERE
					ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

        if ($tsCreateStart > 0)
            $sql .= " AND dateCreate >= {$tsCreateStart} ";

        if ($tsCreateFinish > 0)
            $sql .= " AND dateCreate <= {$tsCreateFinish} ";

        $res = $this->getOneByAnySQL($sql);
        if ($res)
            $created = $res['total'];

        // opened
        $opened = 0;
        $sql = "SELECT
					COUNT(*) AS total
				FROM
					zakupkaHeader
				WHERE
					ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} ";

        if ($tsCreateStart > 0)
            $sql .= " AND startDate >= {$tsCreateStart} ";

        if ($tsCreateFinish > 0)
            $sql .= " AND startDate <= {$tsCreateFinish} ";

        $res = $this->getOneByAnySQL($sql);
        if ($res)
            $opened = $res['total'];

        // finished
        // определить по кол-ву выставленных счетов
		$scm = new SiteCommisionManager();
		$siteCommList = $scm->getByType(SiteCommision::TYPE_ZAK, $ownerSiteId, $ownerOrgId, $tsCreateStart, $tsCreateFinish);
		$finished = count($siteCommList);
        // сумма

        $amountZak = 0;
        $amountSiteCommisionAdded = 0;
        $amountSiteCommisionPayed = 0;
        if ($finished) {
            foreach ($siteCommList AS $siteCommItem) {
                $amountZak = $amountZak + $siteCommItem->baseAmount;
                $amountSiteCommisionAdded = $amountSiteCommisionAdded + $siteCommItem->needAmount;
				$amountSiteCommisionPayed = $amountSiteCommisionPayed + $siteCommItem->payAmount;
            }
        }

        return array('created' => $created, 'opened' => $opened, 'finished' => $finished, 'amountZak' => $amountZak, 'amountSiteCommisionAdded' => $amountSiteCommisionAdded, 'amountSiteCommisionPayed' => $amountSiteCommisionPayed);
    }


}
