<?php
/**
 * Контрол для отображения первой страницы и формы для входа в фдминку
 * 
 */
class AdminkaindexControl extends BaseControl
{
	public $pageTitle = "Область оператора";
	public $folder = "adminka";

	public function preRender()
	{
		parent::preRender();
		$this->layout = "adminkaindex.html";
	}

	public function render()
	{
		
	}	
}
