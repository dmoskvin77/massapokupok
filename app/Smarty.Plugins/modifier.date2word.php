<?php
/**
 */
function smarty_modifier_date2word($string)
{
	if (!$string)
		return "Неизвестно когда";
	// приводим отображаемую дату в формат день-месяц-год	
	$date = new XDateTime($string);
	$dateDMY = $date->format("d-m-Y");

	// текущая дата в в формате день-месяц-год
	$today = new XDateTime();
	$todayDMY = $today->format("d-m-Y");
	
	// сегодня?
	if ($dateDMY == $todayDMY)
		return "cегодня в " . $date->format("H:i:s");
	
	// вчера?	
	$ysdt = new XDateTime( date("d-m-Y",  strtotime("-1 day {$todayDMY}" ) ) );
	$ystdDMY = $ysdt->format("d-m-Y");
	if ($dateDMY == $ystdDMY)
		return "вчера в " . $date->format("H:i:s");

	// если позавчера и позже, то выводим как есть
	// приведение к формату 16 авг, в 18:10
	$replaceDate = array(
		'Jan' => 'января'
		, 'Feb' => 'февраля'
		, 'Mar' => 'марта'
		, 'Apr' => 'апреля'
		, 'May' => 'мая'
		, 'Jun' => 'июня'
		, 'Jul' => 'июля'
		, 'Aug' => 'августа'
		, 'Sep' => 'сентября'
		, 'Oct' => 'октября'
		, 'Nov' => 'ноября'
		, 'Dec' => 'декабря'
		);
    return $date->format("d ").$replaceDate[$date->format("M")].$date->format(" Y, в H:i:s");
}
?>