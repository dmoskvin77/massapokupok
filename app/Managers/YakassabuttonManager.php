<?php
/**
 * Менеджер
 */
class YakassabuttonManager extends BaseEntityManager
{
	// по id орга
	public function getByOrgId($orgId)
	{
		return $this->getOne(new SQLCondition("userId = {$orgId}"));
	}

	// не отмодерированные кнопки
	public function getNew()
	{
		return $this->get(new SQLCondition("status = 1"));
	}

	// активная кнопка, которую можно показать покупателю
	public function getApprovedByOrgId($orgId)
	{
		return $this->getOne(new SQLCondition("userId = {$orgId} AND status = 2"));
	}

}
