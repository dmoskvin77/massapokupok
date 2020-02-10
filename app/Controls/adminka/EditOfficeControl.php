<?php 
/**
* Контрол для  создания/редактирования офиса раздач
*/
class EditOfficeControl extends BaseAdminkaControl
{
	public $pageTitle = "Редактирование офиса раздач";
	
	public function render()
	{
		$id = Request::getInt("id");
		if (!$id)
			$this->pageTitle = "Создание офиса раздач";
		
		if ($id)
		{
			$ofm = new OfficeManager();
			$officeObj = $ofm->getById($id);
			if (!$officeObj)
				Adminka::redirect("manageoffices", "Офис не найден");

			if ($this->ownerSiteId != $officeObj->ownerSiteId || $this->ownerOrgId != $officeObj->ownerOrgId)
				Adminka::redirect("manageoffices", "Нет прав на выполнение действия");

			$this->addData("office", $officeObj);

		}
	}
}