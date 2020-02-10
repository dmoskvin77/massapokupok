<?php
/**
 * Вычисляет полный возраст по дате рождения
 */
function smarty_modifier_age($date)
{
	if (!$date)
		return "неизвестно";
		
	$d = new XDateTime($date);
	$curr = new XDateTime();
	
	$start = $d->format("U");
	$end = $curr->format("U");
	$diff = $end - $start;
	// количество полных лет
	$secs = 365*24*60*60;
	return floor($diff / $secs);
}
?>