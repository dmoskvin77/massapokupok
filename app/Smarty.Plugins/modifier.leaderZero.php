<?php
/**
* дополняет число ведущими нулями 
*
* {$user->id|leaderZero:8}
*/
function smarty_modifier_leaderZero($string, $length)
{
	if(!$string || !$length)
		return $string;

	while(strlen($string) < $length)
		$string = "0$string";
	
	return $string;
}
?>