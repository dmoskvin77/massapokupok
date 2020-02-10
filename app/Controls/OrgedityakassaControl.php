<?php
/**
 *
 */

class OrgedityakassaControl extends AuthorizedOrgControl
{
	public $pageTitle = "Редактирование кнопки Яндекс касса";

	public function render()
	{
		$actor = $this->actor;
		$this->addData("actorId", $actor->id);

		$ybm = new YakassabuttonManager();
        $yabtnObj = $ybm->getByOrgId($actor->id);
        if ($yabtnObj) {
            $this->addData("buttonCode", str_replace("&quot;", '"', htmlspecialchars_decode($yabtnObj->buttonCode, ENT_NOQUOTES)));
        }

	}

}
