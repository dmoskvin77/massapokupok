<?php
/**
 * Менеджер
 */
class OfficeOrderManager extends BaseEntityManager
{
	/*
	 * Функция отдает список по массиву id
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($inpIds)
	{
		if (!$inpIds)
			return null;

		if (count($inpIds) == 0)
			return null;

		$ids = implode(",", $inpIds);
		$res = $this->get(new SQLCondition("id IN ($ids)", null, "id"));

		return Utility::sort($inpIds, $res);
	}

    // получить заказы пользователя в офис по id самих заказов
    public function getByOrderIds($inpIds, $userId)
    {
        if (!$inpIds)
            return null;

        if (count($inpIds) == 0)
            return null;

        $ids = implode(",", $inpIds);
        $res = $this->get(new SQLCondition("orderId IN ({$ids}) AND userId = {$userId}", null, "id"));
        return $res;
    }

	// получить все категории
	public function getAll($ownerSiteId, $ownerOrgId)
	{
		$sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId}");
		return $this->get($sql);
	}

    public function getAllByStatus($ownerSiteId, $ownerOrgId, $status)
    {
        $sql = new SQLCondition("ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} AND status = '{$status}'");

        if ($status == OfficeOrder::STATUS_ORG)
            $sql->orderBy = "officeId, orgId, tsOrg";

        return $this->get($sql);
    }

    // покупатель заберет у организатора
    public function saveOrgAsOfficeToOdrer($orderId, $userId)
    {
        $ohm = new OrderHeadManager();
        $orderHeadObj = $ohm->getById($orderId);
        if (!$orderHeadObj)
            return false;

        // следует ли добавить новую запись или же изменить существующую
        $getOneOfficeOrderObj = $this->getByOrderIdUserId($orderId, $userId);
        if ($getOneOfficeOrderObj)
        {
            // проверить того ли пользователя заказ доставки
            if ($getOneOfficeOrderObj->status == OfficeOrder::STATUS_NEW && $getOneOfficeOrderObj->payStatus == OfficeOrder::STATUS_PAY_NONE)
            {
                // проверить того ли пользователя заказ доставки
                $actor = Context::getActor();
                if ($getOneOfficeOrderObj->userId != $actor->id)
                    return false;

                $getOneOfficeOrderObj->price = 0;
                $getOneOfficeOrderObj->officeId = 0;

                $this->save($getOneOfficeOrderObj);
            }
            else
            {
                return false;
            }
        }
        else
        {
            // добавить новую запись
            $officeOrderObj = $this->addOrgAsOfficeOrder($orderId, $userId, $orderHeadObj);
            // по этой записи у покупателя возникает должок
            if (!$officeOrderObj)
                return false;
        }

        return true;
    }


    // записать выбранный офис
    public function saveOfficeToOdrer($orderId, $userId, $officeId)
    {
        $ohm = new OrderHeadManager();
        $orderHeadObj = $ohm->getById($orderId);
        if (!$orderHeadObj)
            return false;

        $om = new OfficeManager();
        $officeObj = $om->getById($officeId);
        if (!$officeObj || $officeObj->status != Office::STATUS_ENABLED)
            return false;

        // следует ли добавить новую запись или же изменить существующую
        $getOneOfficeOrderObj = $this->getByOrderIdUserId($orderId, $userId);
        if ($getOneOfficeOrderObj)
        {
            if ($getOneOfficeOrderObj->status == OfficeOrder::STATUS_NEW && $getOneOfficeOrderObj->payStatus == OfficeOrder::STATUS_PAY_NONE)
            {
                // проверить того ли пользователя заказ доставки
                $actor = Context::getActor();
                if ($getOneOfficeOrderObj->userId != $actor->id)
                    return false;

                $getOneOfficeOrderObj->price = $officeObj->price;
                $getOneOfficeOrderObj->officeId = $officeObj->id;

                $this->save($getOneOfficeOrderObj);
            }
            else
            {
                return false;
            }
        }
        else
        {
            // добавить новую запись
            $officeOrderObj = $this->addOfficeOrder($orderId, $userId, $officeObj, $orderHeadObj);
            // по этой записи у покупателя возникает должок
            if (!$officeOrderObj)
                return false;
        }

        return true;
    }

    // получить запись по id заказа и id пользователя
    public function getByOrderIdUserId($orderId, $userId)
    {
        $sql = new SQLCondition("orderId = {$orderId} AND userId = {$userId}");
        return $this->getOne($sql);
    }

    // получить записи по закупке
    public function getByHeadId($headId, $status = null)
    {
        if ($status)
            $sql = new SQLCondition("headId = {$headId} AND status = '{$status}' AND officeId > 0");
        else
            $sql = new SQLCondition("headId = {$headId} AND officeId > 0");

        return $this->get($sql);
    }

    // добавляем новую запись о заказе товара в офис
    public function addOrgAsOfficeOrder($orderId, $userId, $orderHeadObj)
    {
        $zhm = new ZakupkaHeaderManager();
        $zakupkaObj = $zhm->getById($orderHeadObj->headId);
        if (!$zakupkaObj)
            return false;

        $officeOrderObj = new OfficeOrder();
        $officeOrderObj->officeId = 0;
        $officeOrderObj->orderId = $orderId;
        $officeOrderObj->userId = $userId;
        $officeOrderObj->headId = $orderHeadObj->headId;
        $officeOrderObj->orgId = $zakupkaObj->orgId;
        $officeOrderObj->status = OfficeOrder::STATUS_NEW;
        $officeOrderObj->payStatus = OfficeOrder::STATUS_PAY_NONE;
        $officeOrderObj->tsOrder = time();

        return $this->save($officeOrderObj);
    }

    // добавляем новую запись о заказе товара в офис
    public function addOfficeOrder($orderId, $userId, $officeObj, $orderHeadObj)
    {
        $zhm = new ZakupkaHeaderManager();
        $zakupkaObj = $zhm->getById($orderHeadObj->headId);
        if (!$zakupkaObj)
            return false;

        $officeOrderObj = new OfficeOrder();
        $officeOrderObj->officeId = $officeObj->id;
        $officeOrderObj->orderId = $orderId;
        $officeOrderObj->userId = $userId;
        $officeOrderObj->headId = $orderHeadObj->headId;
        $officeOrderObj->orgId = $zakupkaObj->orgId;
        $officeOrderObj->status = OfficeOrder::STATUS_NEW;
        $officeOrderObj->payStatus = OfficeOrder::STATUS_PAY_NONE;
        $officeOrderObj->tsOrder = time();

        // а так же что касается стоимости доставки
        $officeOrderObj->price = $officeObj->price;
        return $this->save($officeOrderObj);
    }

    public function orgSendOrdersToOffices($orgId, $idsArray)
    {
        $idsString = implode(',', $idsArray);
        $sql = "UPDATE officeOrder SET status = '".OfficeOrder::STATUS_ORG."', tsOrg = ".time()." WHERE orgId = {$orgId} AND id IN ({$idsString})";
        return $this->executeNonQuery($sql);
    }

    public function getWaitingOfficeOrdersIds($ownerSiteId, $ownerOrgId)
    {
        $sql = "SELECT orderId FROM officeOrder WHERE ownerSiteId = {$ownerSiteId} AND ownerOrgId = {$ownerOrgId} AND status = '".OfficeOrder::STATUS_OFFICE."'";
        $res = $this->getColumn($sql);

        if ($res)
            return $res;
        else
            return null;

    }

    public function isAwaitingInOffice($orderId, $userId)
    {
        $sql = new SQLCondition("orderId = {$orderId} AND userId = {$userId} AND status = '".OfficeOrder::STATUS_OFFICE."'");
        return $this->getOne($sql);
    }

}
