<?php
/**
 * Менеджер 
 */
class ProManager extends BaseEntityManager
{
	// проверить активен ли pro аккаунт
	public function checkPro($userId)
	{
		$userId = intval($userId);
		if ($userId > 0)
        {
            $sql = "SELECT id FROM pro WHERE userId = ".$userId." AND validTo >= ".time();
            $res = $this->getByAnySQL($sql);
            if ($res != null)
                return true;
			else
                return false;
        }
        return false;
	}

}
