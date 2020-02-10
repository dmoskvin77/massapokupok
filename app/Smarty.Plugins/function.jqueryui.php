<?php
/**
 * Загружает библиотеку jquery-ui и необходимые компоненты
 *
 * @example
 * {jqueryui plugins="datepicker" theme='base'}
 */
function smarty_function_jqueryui($params, &$smarty)
{
	$result = '';
	$uiPath = '/js/plugin/ui';
	$revision = BaseApplication::getRevision();

	if (!array_key_exists('theme', $params))
		$params['theme'] = "base";

	$uiScripts = array(
		"core" => array($uiPath . "/jquery.ui.core.js", $uiPath . "/themes/{$params['theme']}/jquery.ui.all.css"),
		"draggable" => $uiPath . "/jquery.ui.draggable.js",
		"droppable" => $uiPath . "/jquery.ui.droppable.js",
		"resizeable" => $uiPath . "/jquery.ui.resizable.js",
		"selectable" => $uiPath . "/jquery.ui.selectable.js",
		"sortable" => $uiPath . "/jquery.ui.sortable.js",
		"accordion" => $uiPath . "/jquery.ui.accordion.js",
		"dialog" => $uiPath . "/jquery.ui.dialog.js",
		"slider" => $uiPath . "/jquery.ui.slider.js",
		"tabs" => $uiPath . "/jquery.ui.tabs.js",
		"datepicker" => array($uiPath . "/jquery.ui.datepicker.js", $uiPath . "/i18n/jquery.ui.datepicker-ru.js"),
		"progressbar" => $uiPath . "/jquery.ui.progressbar.js"
	);

	// обрабатываем массив компонентов
	$scripts = isset($params['plugins']) ? explode(",", $params['plugins']) : array();
	array_unshift($scripts, "core");
	foreach ($scripts as $key => $script)
		$scripts[$key] = trim($script);
	$scripts = array_unique($scripts);

	foreach ($scripts as $script)
	{
		$script = $uiScripts[$script];
		if ($script)
		{
			$script = is_array($script) ? $script : (array)$script;
			foreach ($script as $item)
			{
				if (!BaseApplication::loadScript($item))
					continue;

				$type = pathinfo($item, PATHINFO_EXTENSION); // (preg_match('/\.([jsc]{2,3})$/', $item, $matches) ? $matches[1] : "js");

				if ($type == "js")
					$result .= "<script language=\"javascript\" type=\"text/javascript\" src=\"{$item}?{$revision}\"></script>\n";
				if ($type == "css")
					$result .= "<link type=\"text/css\" href=\"{$item}?{$revision}\" rel=\"stylesheet\"/>\n";
			}
		}
	}

	return $result;
}

?>