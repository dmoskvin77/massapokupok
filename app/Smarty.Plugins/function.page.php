<?php
/**
 * Возвращает содержимое CMS страницы по alias
 */
function smarty_function_page($params, &$smarty)
{
	$alias = @$params['alias'];
	if ($alias == null)
		throw new Exception("Не указано имя страницы");

	/*
	$cm = new ContentManager();
	$content = $cm->getByAlias($alias);
	if($content->status == Content::STATUS_ENABLED)
		return $content->text;
	 */
}
?>