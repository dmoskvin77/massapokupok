<?php
/**
 * 
 * Показывает Лог SQL запросов
 * @example
 * 	{sqlhistory name='master, slave'}
 */
function smarty_function_sqlhistory($params, &$smarty)
{
	try 
	{		
		if (!Configurator::get("application:debug"))
			return false;
			
		if(!isset($params['name']))
			return "SQLHISTORY TAG : Undefined 'name'";
		
		$names = explode(",", $params['name']);
		$html = "";
		$globalUrl = "http://".str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

		foreach ($names as $name)
		{			
			$text = trim($name);
			$html .= "<b>{$text}:</b>";
			$history = Application::getConnection($text)->getQueryHistory();
			if (null == $history)
			{
				$html .= "<i>No requests. Ensure that debug mode is switched on.</i><br/>";
				continue;
			}
			
			$html .= "<ul>";
			foreach($history as $num => $log)
			{
				if (preg_match("/SELECT/i", $log))
				{
					// креатив для показа в плане запросов максимального кол-ва затронутых строк при
					// селекте
					//$explain = Application::getConnection($text)->getRows("EXPLAIN {$log}");
					$maxAffectedRows = 0;
					//$tmp = array();
//					
//					foreach ($explain as $exp)
//					{
//						 $tmp[] = intval($exp["rows"]);
//					}
					
					//$maxAffectedRows = max($tmp);
					$encoded = urlencode($log);				
					$link =  "<a href='{$globalUrl}/install/tools/explain.php?db={$text}&amp;sql=$encoded'>$log/* Max affected rows {$maxAffectedRows}*/</a>";
				}
				else 
				{
					$link = $log;
				}
				$html .= "<li>[$num] :  $link</li>";
			}	
			$html .= "</ul>";		
		}	
	}
	catch (Exception $e)
	{
		$html = "SQLHISTORY TAG : " . $e->getMessage();
	}
	return $html;
}
?>