<?php 
/**
* Контрол для представления информации о закупке
*/
class ZHControl extends BaseAdminkaControl
{
	public $pageTitle = "";
	public $layout = "blank.html";
	
	public function render()
	{
		$headid = Request::getInt("headid");
		$zhm = new ZakupkaHeaderManager();
		$curZHM = $zhm->getById($headid);
		// есть закупка
		if ($curZHM)
		{
			if ($this->ownerSiteId != $curZHM->ownerSiteId || $this->ownerOrgId != $curZHM->ownerOrgId)
				Adminka::redirect("managezh", "Нет прав для выполнения данного действия");

			// это действительно закупка данного орга
			$this->addData("headid", $headid);
			$this->addData("zh", $curZHM);
			$this->addData("headname", $curZHM->name);
			$this->addData("headstatus", $curZHM->status);

			if ($curZHM->pageUrl)
				$this->addData("pageUrl", base64_decode($curZHM->pageUrl));

			$this->addData("desc", str_replace("&quot;", '"', htmlspecialchars_decode($curZHM->description, ENT_NOQUOTES)));

			// создадим список строк закупки чтобы показать оргу как она у него наполнилась
			$zlm = new ZakupkaLineManager();
			$lines = $zlm->getJoinedWProdsByZHId($headid);

			if ($lines)
				$this->addData("zlines", $lines);

			$om = new UserManager();
			$curOrg = $om->getById($curZHM->orgId);

			if ($curOrg)
				$this->addData("curorg", $curOrg);

			$op = new UserManager();
			$curOpt = $op->getById($curZHM->optId);

			if ($curOrg)
				$this->addData("curopt", $curOpt);

		}

	}

}
