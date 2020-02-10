<?php
/**
 * Контрол для представления формы управления кнопками яндекс кассы
 * 
 */
class ManageyakassaControl extends BaseAdminkaControl
{
	public function render()
	{
        if ($this->ownerSiteId != 1) {
            Adminka::redirectBack("Нет прав на выполнение данного действия");
        }

		$ykm = new YakassabuttonManager();
        $unmoderatedButtons = $ykm->getNew();

		$userIds = array();
		if (count($unmoderatedButtons))
		{
			foreach ($unmoderatedButtons AS $oneButton)
				$userIds[] = $oneButton->userId;
		}

		$this->addData("buttonlist", $unmoderatedButtons);

		if (count($userIds))
		{
			$um = new UserManager();
			$users = $um->getByIds($userIds);
			$this->addData("users", $users);
		}
	}
}
