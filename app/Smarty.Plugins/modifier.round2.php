<?php
/**
* округление до сотых, с точкой в качестве разделителя
 */

function smarty_modifier_round2($value)
{
	return round($value, 2);
}
