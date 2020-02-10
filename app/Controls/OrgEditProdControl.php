<?php
/**
 * Контрол для редактирования товара
 * 
 */
class OrgEditProdControl extends AuthorizedOrgControl
{
	public $pageTitle = "Редактирование товара";

	public function preRender()
	{
		parent::preRender();
		$this->layout = "blank.html";
	}

	public function render()
	{
		$actor = $this->actor;
		$this->addData("orgId", $actor->id);

		$headid = Request::getInt("headid");		
		$zhm = new ZakupkaHeaderManager();

		$prodid = Request::getInt("prodid");
		$pm = new ProductManager();

		$curProd = null;
		if (!$headid) $headid = 0;
		if (!$prodid) $prodid = 0;
		
		if ($headid == 0 || $prodid == 0)
		{
			$this->addData("error", "Переданы пустые параметры");
		}
		else
		{

			$curZHM = $zhm->getByIdAndOrgId($headid, $actor->id);
			if (!$curZHM)
			{
				$this->addData("error", "Не указан код редактируемой закупки или она не Ваша");
			}
			else
			{
				$curProd = $pm->getById($prodid);
				if (!$curProd)
				{
					$this->addData("error", "Не найден товар");
				}
				else
				{
					if ($this->ownerSiteId == $curProd->ownerSiteId && $this->ownerOrgId == $curProd->ownerOrgId)
					{
						// всё нормально, продолжаем
						$this->addData("curprod", $curProd);

						// проверим есть ли этот товар в закупках, других пользователей
						$zlm = new ZakupkaLineManager();
						$countProd = $zlm->countProdNoHeadId($prodid, $actor->id);
						$this->addData("countprod", $countProd);

						$this->addData("headid", $headid);
						$this->addData("orgId", $actor->id);

						// пощитаем кол-во фоток
						$countPics = 0;
						if ($curProd->picFile1 != null) $countPics++;
						if ($curProd->picFile2 != null) $countPics++;
						if ($curProd->picFile3 != null) $countPics++;
						if ($curProd->picFile4 != null) $countPics++;
						if ($curProd->picFile5 != null) $countPics++;
						if ($curProd->picFile6 != null) $countPics++;
						if ($curProd->picFile7 != null) $countPics++;
						if ($curProd->picFile8 != null) $countPics++;
						if ($curProd->picFile9 != null) $countPics++;
						if ($curProd->picFile10 != null) $countPics++;

						$this->addData("allowedpics", intval(10 - $countPics));

					}
					else
						$this->addData("error", "Нет прав на выполнение данного действия");

				}

			}

		}

	}

}
