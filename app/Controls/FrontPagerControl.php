<?php
/**
* Пэйджер многостраничных списков
*/
abstract class FrontPagerControl extends BaseControl
{
	/**
	* Возвращает текущий номер страницы многостраничного списка
	* Если страница не задана, возвращает 1
	* 
	* @param string $pageParam - имя списка
	* @return int
	*/
	public static function getPage($pageParam = "page")
	{
		$page = Request::getInt($pageParam);
		$page = $page > 1 ? $page : 1;
		return $page;
	}

	/**
	* Возвращает текущий номер страницы многостраничного списка.
	* Если страница не задана, возвращает 0
	* 
	* @param string $pageParam - имя списка
	* @return int
	*/
	public static function getPageOrZero($pageParam = "page")
	{
		$page = Request::getInt($pageParam);
		$page = $page > 0 ? $page : 0;
		return $page;
	}
	
	/**
	* Возвращает список сущностей для отображении на данной странице многостраничного списка
	* 
	* @param array $list - все id сущностей списка
	* @param int $perPage - количество сущностей на страницу
	* @param string $pageParam - имя списка
	* @return array
	*/
	public static function limit($list, $perPage, $pageParam = "page")
	{
		if(!$list)
			return array();
			
		$page = FrontPagerControl::getPage($pageParam);
			
		return array_slice($list, ($page - 1) * $perPage, $perPage, true);
	}
}

