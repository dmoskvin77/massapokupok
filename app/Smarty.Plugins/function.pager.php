<?php
/**
 *
 * примеры :
 * {pager total=10 per=2}
 * {pager total=10 per=2 param=my_page}
 * {pager total=10 per=2 url='/index.php?myparam=10'} - для формирования ссылки типа /index.php?myparam=10&page=1
 * 
 */
function smarty_function_pager($params, &$smarty)
{
	
	// сколько всего записей
	$total = intval( $params["total"] ); 
	
	 // URL для формирования линка
	$url = $params["url"] == null ? Request::requestUri() : $params["url"];

	// сколько записей на странице
	$per = intval($params["per"]);

	// префикс для параметра page, если несколько пейджингов на странице
	$param = $params["param"] == null ? "page" : $params["param"];
	$currentPage = Request::getInt($param, 1);
	
	$ui = UIGenerator::getInstance();
	$ui->forceViewComponent = true;	
	
	// рисуем контрол пейджера напрямую
	$ui->renderControl(new PagerControl($currentPage, $total, $per, $url, $param ));	
	return $ui->display();
}
?>