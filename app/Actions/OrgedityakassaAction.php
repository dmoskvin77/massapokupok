<?php
/**
*
*/
class OrgedityakassaAction extends AuthorizedOrgAction implements IPublicAction
{
	public function execute()
	{
		$actor = $this->actor;

		// само сообщение
		$message = Request::getVar("buttonCode");
		if (!$message || $message == '')
			Enviropment::redirectBack("Код кнопки не должен быть пустым");

		if (mb_strlen($message) > 10000000)
			Enviropment::redirectBack("Слишком длинный код кнопки");

        $prevCode = null;
		$ybm = new YakassabuttonManager();
		$yabtnObj = $ybm->getByOrgId($actor->id);
		if (!$yabtnObj) {
			$yabtnObj = new Yakassabutton();
		}
        else {
            $prevCode = $yabtnObj->buttonCode;
        }

		$yabtnObj->userId = $actor->id;
		$yabtnObj->buttonCode = $message;

        if ($prevCode != $message) {
            $yabtnObj->status = 1;
        }

        $yabtnObj->ownerSiteId = $this->ownerSiteId;
        $yabtnObj->ownerOrgId = $this->ownerOrgId;

		$ybm->save($yabtnObj);

		Enviropment::redirect("orgfinanses", "Код кнопки сохранен и будет доступен после проверки администратором.");

	}

}
