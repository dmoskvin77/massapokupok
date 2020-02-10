<?php
/**
 * Менеджер управления владельцами сайтов
 */
class OwnerSiteManager extends BaseEntityManager
{
	public function getByHostName($hostName)
	{
		$sql = new SQLCondition("hostName = '{$hostName}'");
		$page = $this->getOne($sql);
		return $page;
	}

	// получить всех
	public function getAll()
	{
        $sql = new SQLCondition();
        $sql->orderBy = "dateCreate DESC";
        return $this->get($sql);
	}

    public function getByIds($inpIds)
    {
        if(!$inpIds)
            return null;

        if (count($inpIds) == 0)
            return null;

        $ids = implode(",", $inpIds);
        $res = $this->get(new SQLCondition("id IN ($ids)", null, "id"));

        return Utility::sort($inpIds, $res);
    }

}
