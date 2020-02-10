<?php
/**
* Пейджер
* 
* пример: {frontPager total=10 per=2}
* пример: {frontPager total=10 per=2 pageParam=textpage}	- имя пэйджера при нескольких пэйджерах на странице
* пример: {frontPager total=10 per=2 addParamName=tab addParamValue=comment}	- дополнительный параметр для ссылки пейджера
* 
*/
function smarty_function_frontPager($params, &$smarty)
{
	extract($params);
	
	if(!$total || !$per)
		return false;

	$pageParam = $pageParam ? $pageParam : "page";
	
	// привожу url к виду slashMode (т.к. при запросах GET не приведено)
	$url = preg_replace("/[?&=]/", "/", Request::requestUri());

    if ($url == '/')
        $url = '/zakupki';
	
	// формируем URL: из текущего надо убрать значение $pageParam
	$url = preg_replace("/[\/]{$pageParam}[\/][-\d]+/i", "", $url);
	
	// добавляю в frontPager если нужно параметр $addParamName со значением $addParamValue (при пустом значении, параметр удаляется)
	if($addParamName)
	{
		$url = preg_replace("/[\/]{$addParamName}[\/][^\/]+/i", "", $url);
		if($addParamValue)
			$url .= "/$addParamName/$addParamValue";
	}
	
	$url .= "/$pageParam";
	
	// $url в формате get-mode
	$url = Utility::toGetUrl($url);
	$smarty->assign("url", Utility::toGetUrl($url));
	
	$totalPages = 0;
	while($total > 0)
	{
		$totalPages ++;
		$total -= $per;
	}
	
	$currentPage = FrontPagerControl::getPage($pageParam) - 1;
	if($currentPage > $totalPages)
		$currentPage = $totalPages;
	
	// если мы на первой странице и все записи помещаются на одну страницу, пэйджер не показывать
	if($currentPage == 0 && $totalPages < 2)
		return false;
	
	$area = 2;	// количество линков слева и страва от текущей страницы
	$pre = false;
	$post = false;
	$pages = array();
	for($index = 0; $index < $totalPages; $index++)
	{
		if($index == 0 || $index == $totalPages - 1 || abs($index - $currentPage) <= $area || ($index == 1 && abs($index - $currentPage) == $area + 1) || ($index == $totalPages - 2 && abs($index - $currentPage) == $area + 1))
		{
			$first = $index * $rowsPerPage + 1;
			$last = $first + $rowsPerPage - 1;
			if($last > $count)
				$last = $count;
			$pages[$index + 1] = $index + 1;
		}
		else if($index < $currentPage && !$pre)
		{
			$pages[($currentPage - 1) / 2] = "...";
			$pre = true;
		}
		else if($index > $currentPage && !$post)
		{
			$pages[($totalPages + $currentPage + 3) / 2] = "...";
			$post = true;
		}
	}

	if($currentPage > 0 && $currentPage <= $totalPages - 1)
		$smarty->assign("prevPage", $currentPage);
	
	$smarty->assign("currentPage", $currentPage + 1);
	
	if($currentPage < $totalPages - 1)
		$smarty->assign("nextPage", $currentPage + 2);
	
	$smarty->assign("pages", $pages);
		
	$templatesDir = Configurator::get("framework:directory.templates");
	$smarty->display($templatesDir . "/FrontPagerControl.html");
}
?>