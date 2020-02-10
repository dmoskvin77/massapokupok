<?php
/**
 * Менеджер
 */
class PurseManager extends BaseEntityManager
{
	// поиск по merchantId (string)
	public function getByMerchantId($merchantId)
	{
		return $this->getOne(new SQLCondition("merchantId = '{$merchantId}' AND status = 1"));
	}

	// по id орга
	public function getByOrgId($orgId)
	{
		return $this->getOne(new SQLCondition("userId = {$orgId} AND status = 1"));
	}

}
