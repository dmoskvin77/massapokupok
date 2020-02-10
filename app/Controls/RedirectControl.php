<?php 
/**
* Контрол для переадресации на внешний сайт
*/
class RedirectControl extends IndexControl
{
	public $pageTitle = "Редирек на внешний сайт";
	
	public function render()
	{
		$this->layout = "empty.html";
		$url = Request::getVar("url");
		$this->addData("url", $url);
	}
}
