<?php
/**
* дополняет число ведущими нулями 
*
* {$user->id|leaderZero:8}
*/
function smarty_modifier_null2zero($var)
{
	return is_null($var) ? 0 : $var;
}
