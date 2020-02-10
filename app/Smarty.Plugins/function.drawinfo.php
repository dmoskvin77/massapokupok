<?php
/**
 * вставляет информацию (строку) о ближайшем тираже
 *
 * {drawinfo alias="rt" mode="image"}
 * {drawinfo|dateformat:"d.m.Y" lid=2 mode="date"}
 * {drawinfo lid="1" mode="number"}
 * {drawinfo lid="1" drawid="2" mode="number"}
 */
function smarty_function_drawinfo($params, &$smarty)
{

	if (array_key_exists('alias', $params))
		$alias = $params['alias'];
	else
		$alias = null;

	if (array_key_exists('lid', $params))
		$lid = $params['lid'];
	else
		$lid = null;

	if (array_key_exists('mode', $params))
		$mode = $params['mode'];
	else
		$mode = null;

	if (array_key_exists('drawid', $params))
		$drawid = $params['drawid'];
	else
		$drawid = null;

	if (!$mode)
		return null;

	$dm = new DrawingManager();

	if ($alias)
		$drawList = $dm->getShowList($alias);
	else
		$drawList = null;

	$lm = new LotteryManager();

	if ($lid)
		$curLoto = $lm->getByLid(intval($lid));
	else 
		$curLoto = null;

	if ($curLoto && !$drawList)
		$drawList = $dm->getShowList($curLoto->alias);

	$answer = null;

	// по одному тиражу
	if ($drawid != null)
	{
		$currentDraw = $dm->getById($drawid);
		if ($currentDraw)
			$drawList[0] = $currentDraw;
	}

	if (isset($drawList[0]))
	{
		if ($mode == "image")
			$answer = "/storage/drawing/small_".$drawList[0]->ticketFile;

		if ($mode == "mini")
			$answer = "/storage/drawing/mini_".$drawList[0]->ticketFile;

		if ($mode == "maxi")
			$answer = "/storage/drawing/small_".$drawList[0]->ticketFile;

		if ($mode == "buy")
			$answer = "/buyticket/lottery/".$drawList[0]->lotteryAlias."/draw/".$drawList[0]->id;

		if ($mode == "number")
			$answer = $drawList[0]->number;

		if ($mode == "date")
			$answer = $drawList[0]->startDate;

	}

	return $answer;
	
}
?>