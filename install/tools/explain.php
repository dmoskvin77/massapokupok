<?php
header("Content-Type: text/html; charset=utf-8");
set_time_limit(0);
require_once "../../app/Config/framework.php";

$sqlLog = Request::getVar("sql");
if($sqlLog)
{
	$db = Request::getVar("db");
	$oConn = Application::getConnection($db);	
	//$sql = htmlspecialchars( stripslashes( urldecode( Request::getVar("sql") )) );
	$sql = stripslashes(html_entity_decode( urldecode( Request::getVar("sql") )));
	try 
	{
		$res = $oConn->getRows(" EXPLAIN " . $sql);
	}
	catch (Exception $e)
	{
		echo "Недопустимая операция <br/><br/>";
		echo $e->getMessage();
		die();
	}
	$html = "";
	$html = "<a href='{$_SERVER['HTTP_REFERER']}'>НАЗАД</a>";
	$html .= "<h1>План SQL запроса : </h1>";
	$html .= "<b>{$sql}</b><br/><br/>";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "	<td>id</td>";
	$html .= "	<td>select type</td>";
	$html .= "	<td>table</td>";
	$html .= "	<td>type</td>";
	$html .= "	<td>possible keys</td>";
	$html .= "	<td>key</td>";
	$html .= "	<td>key len</td>";
	$html .= "	<td>ref</td>";
	$html .= "	<td>rows</td>";
	$html .= "	<td>Extra</td>";
	$html .= "</tr>";
	
	for($i =0; $i < count($res); $i++)
	{	
		$html .= "<tr>";
		foreach ($res[$i] as $item)
		{			
			$item = htmlentities( $item ? $item : "NULL" );
			$html .= "<td>{$item}</td>";
		}
		$html .= "</tr>";
	}
	

	$html .= "</table>";
	
	$html .= "<h3>Читаем подробнее об EXPLAIN</h3>";
	$html .= "<a href='http://www.mysql.ru/docs/man/EXPLAIN.html' target='_blank'>Здесь информация из документации</a><br/>";
	$html .= "<a href='http://phpclub.ru/detail/article/mysql_optimize' target='_blank'>Оптимизация запросов в MySQL</a><br/>";
	echo $html;
	die();
}
?>