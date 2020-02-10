<?php
/**
 * Базовый класс для всех действий
 * 
 */

class BaseAction extends Action
{
	public $ownerSiteId = 0;
	public $ownerOrgId = 0;
	public $host;
	public $tplFolder;
	public $siteDomain;
	public $orgSubDomain;

	/**
	 * Выполняется первым при обработке действия.
	 * Здесь проверяется, включены ли куки у клиента.
	 * 
	 * @return void
	 */
	public function preExecute()
	{
		if (isset($_SERVER['HTTP_HOST']))
			$this->host = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
		else
			Request::send404();

		// определяем где мы (домен ли это 2-го или 3-го уровня)
		$domainParts = explode('.', $this->host);
		if (count($domainParts) == 3)
		{
			$gotSubDomain = $domainParts[0];
			$gotDomain = $domainParts[1].'.'.$domainParts[2];
		}
		else if (count($domainParts) == 2)
		{
			$gotSubDomain = null;
			$gotDomain = $domainParts[0].'.'.$domainParts[1];
		}
		else
			Request::send404();

		$osm = new OwnerSiteManager();
		$oom = new OwnerOrgManager();

		if ($gotSubDomain)
		{
			$ownerOrgObj = $oom->getByAlias($gotSubDomain);
			if ($ownerOrgObj)
			{
				$this->tplFolder = $ownerOrgObj->tplFolder;
				$this->ownerOrgId = $ownerOrgObj->id;
				$this->orgSubDomain = $ownerOrgObj->alias;
			}
		}

		if ($gotDomain)
		{
			$ownerSiteObj = $osm->getByHostName($gotDomain);
			if ($ownerSiteObj)
			{
				if (!$this->tplFolder)
					$this->tplFolder = $ownerSiteObj->tplFolder;

				$this->ownerSiteId = $ownerSiteObj->id;
				$this->siteDomain = $ownerSiteObj->hostName;
			}
		}

		if (!$this->tplFolder)
			Request::send404();

		return true;
	}

	/**
	 * не отрабатываем этот Action
	 * 
	 * @return void
	 */
	public function execute()
	{
		Request::redirect("/");
	}
}
