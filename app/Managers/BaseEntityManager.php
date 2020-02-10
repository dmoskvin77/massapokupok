<?php
/**
* Базовый класс для мэнэджеров проекта
*/
class BaseEntityManager extends EntityManager 
{
	function __construct()
	{
		$this->setCommonConnection(Application::getConnection("master"));
	}
}
