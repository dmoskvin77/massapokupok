<?php
/**
 * Базовый класс для всех контролов админки
 * 
 */
class BaseAdminkaControl extends AuthorizedAdminkaControl 
{
	public $layout = "adminka.html";
	public $folder = "adminka";	
	public $pageTitle = "Область оператора";
	public $currentUserName = "";
	
	public function preRender()
	{
		// сначала всегда вызвать этот метод
		parent::preRender();
	}
	
	/**
	 * его тоже не будем рисовать
	 *
	 */
	public function render()
	{
		Request::send404();	
	}	
}
