<?php
/**
 * парсер bbcode
 *
 */

function smarty_modifier_bbcode($text)
{
	$text = Utility::bbcode2html($text);
    return $text;
}

?>