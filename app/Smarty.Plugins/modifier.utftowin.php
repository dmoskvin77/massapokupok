<?php
/**
* перекодировка из utf8 в win-1251
 */

function smarty_modifier_utftowin($value)
{
	return iconv('utf-8', 'windows-1251', $value);
}
