<?php
/**
 * Контрол для визуального представления окружения организатора
 * после входа в систему
 *
 */
class OrgAreaControl extends AuthorizedOrgControl
{
	public $pageTitle = "Новости";

	public function render()
	{
		$dateTimeArray = getdate( time() );
		$dateHour = $dateTimeArray['hours'];

		$helloWord = "";

		if ($dateHour >= 17 || $dateHour <= 3)
			$helloWord = "Добрый вечер";

		if ($dateHour >= 12 && $dateHour < 17)
			$helloWord = "Добрый день";

		if ($dateHour >= 3 && $dateHour < 12)
			$helloWord = "Доброе утро";

		$this->addData("hellotimeday", $helloWord);

	}
}
