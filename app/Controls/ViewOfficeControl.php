<?php
/**
* Контрол покажет инфу об офисе
*/
class ViewOfficeControl extends AuthorizedUserControl
{
	public $pageTitle = "Информация об офисе";

	public function render()
	{
		$id = Request::getInt("id");

		$ts = time();
		$this->addData("ts", $ts);

		if ($id > 0)
		{
			$om = new OfficeManager();
			$office = $om->getById($id);
			if (!$office)
				Enviropment::redirectBack("Не найден офис");

			$this->addData("office", $office);
		}
		else
		{
			Enviropment::redirectBack("Не задан ID офиса");
		}

	}

}
