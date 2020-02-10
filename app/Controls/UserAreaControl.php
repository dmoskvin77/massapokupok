<?php
/**
 * Контрол для визуального представления окружения покупателя
 * после входа в систему
 *
 */
class UserAreaControl extends AuthorizedUserControl
{
	public $pageTitle = "Новости";

	public function render()
	{
		$this->addData("host", $this->host);

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

