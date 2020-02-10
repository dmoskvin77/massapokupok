<?php
/**
 * Контрол для просмотра содержимого закупки
 *
 */
class OrgAddZlControl extends AuthorizedOrgControl
{
	public $pageTitle = "Добавить ряд";

	public function render()
	{
		$actor = $this->actor;
		// id закупки
		$headid = Request::getInt("headid");
		if (!$headid)
			Enviropment::redirectBack("Не указан ID закупки");

		// id ряда если редактируется
		$id = Request::getInt("id");
		$zhm = new ZakupkaHeaderManager();
		$curZHM = $zhm->getByIdAndOrgId($headid, $actor->id);
		// есть закупка
		if ($curZHM)
		{
			if ($curZHM->orgId != $actor->id || $this->ownerSiteId != $curZHM->ownerSiteId || $this->ownerOrgId != $curZHM->ownerOrgId)
				Enviropment::redirectBack("Нет прав на редактирование закупки");

			// проверим статус закупки
			if (!in_array($curZHM->status, array('STATUS_NEW', 'STATUS_ACTIVE', 'STATUS_ADDMORE', 'STATUS_VOTING')))
				Enviropment::redirectBack("Статус закупки не позволяет редактировать ряд");

			// это действительно закупка данного орга
			$this->addData("headid", $headid);
			$this->addData("headobj", $curZHM);
			$this->addData("headname", $curZHM->name);
			$this->addData("headstatus", $curZHM->status);

			// остальные данные
			$this->addData("zakListStatusNames", OrderList::getStatusDesc());

			$um = new UserManager();
			$orgList = $um->getByIds(array($curZHM->orgId));
			$this->addData("orgList", $orgList);

			$zlm = new ZakupkaLineManager();
			$pm = new ProductManager();

			if ($id)
			{
				$editZline = $zlm->getById($id);
				if (!$editZline)
					Enviropment::redirectBack("Ряд не найден");

				if ($editZline->orgId != $actor->id || $this->ownerSiteId != $editZline->ownerSiteId || $this->ownerOrgId != $editZline->ownerOrgId)
					Enviropment::redirectBack("Нет прав на редактирование ряда");

				if (!$editZline->minValue)
				{
					$gotSizes = @unserialize($editZline->sizes);
					if (count($gotSizes))
						$editZline->sizes = implode(',', $gotSizes);
				}

				$this->addData("editZline", $editZline);

				// поиск товара
				if ($editZline->productId)
				{
					$editProduct = $pm->getById($editZline->productId);
					$this->addData("editProduct", $editProduct);
				}

				// есть ли заказы по данному ряду (можно ли редактировать позиции)
				$olm = new OrderListManager();
				$orderList = $olm->getByZlId($id);
				if (count($orderList))
					$this->addData("isOrdered", true);

			}

		}
		else
			Enviropment::redirectBack("Не найдена закупка");

	}

}
