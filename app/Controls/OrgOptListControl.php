<?php
/**
 * Просмотрт списка оптовиков под организатором
 *
 */
class OrgOptListControl extends AuthorizedOrgControl
{
	public $pageTitle = "Поставщики";

	public function render()
	{
		$op = new OptovikManager();
		$ulm = new UrlListManager();

		$viewOption = Request::getVar("view");
		$this->addData("view", $viewOption);

		$url = Request::getVar("url");
		if ($url)
		{
			$this->addData("url", $url);
			$url = urldecode($url);

			// если не указан домен, добавим .ru
			if (strpos($url, '.') === false)
				$url .= '.ru';

			$url = Utility::prepareHostName($url);
			if ($url)
			{
				// поиск поставщика по url
				$getLinkedUrl = $ulm->getByUrl($this->ownerSiteId, $this->ownerOrgId, $url);
				if ($getLinkedUrl)
				{
					$getOptId = $getLinkedUrl->optId;
					$getOptovik = $op->getById($getOptId);
					if ($getOptovik)
					{
						$this->addData("getOptovik", $getOptovik);
						// а так же передадим ссылки по найденному оптовику
						// urlListSearch
						$urlListSearch = $ulm->getByOptovik($getOptovik->id);
						$this->addData("urlListSearch", $urlListSearch);
					}
				}
			}
		}

		$actor = $this->actor;

		if ($viewOption == 'my')
			$optList = $op->getByUserId($actor->id);
		else
			$optList = $op->getFree($this->ownerSiteId, $this->ownerOrgId);

		$this->addData("optList", $optList);

		// поднимаем ссылки на сайты оптовиков
		if (count($optList))
		{
			$optIds = array();
			foreach ($optList AS $oneOptovik)
				$optIds[] = $oneOptovik->id;

			if (count($optIds))
			{
				$urlList = $ulm->getByOptIds($optIds);
				if (count($urlList))
				{
					$gotedUrl = array();
					foreach ($urlList AS $gotOneUrl)
						$gotedUrl[$gotOneUrl->optId][] = $gotOneUrl;

					$this->addData("urlList", $gotedUrl);

				}
			}
		}
	}
}
