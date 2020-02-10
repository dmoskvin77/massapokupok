<?php
/**
* перекодировка win-1251 в utf8
 */

function smarty_modifier_wintoutf($value)
{
	return iconv('windows-1251', 'utf-8', $value);
}
