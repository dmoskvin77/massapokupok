<?php
/**
* возвращает строку, разбив длинные слова внутри нее и ограничив размер строки
*
* {$alb->name|forcewrap}
* используются дефолтные значения
*
* {$alb->name|forcewrap:15:50:false:true}
* 15 - максимальная длина слова
* 50 - максимальная длина строки. если 0 - не ограничено
* false - при обрезании текста не показывает title его полного текста при наведении мышки
* true - в тексте есть html теги
*/
function smarty_modifier_forcewrap($text, $maxWordLen = 20, $maxStringLen = 0, $allowTitle = true, $allowHtml = false)
{
	if($text === null)
		return "";

	if(!$allowHtml)
		$text = htmlspecialchars_decode($text);

	if(!$maxWordLen)
		$maxWordLen = $maxStringLen;
	
	// разбиваю длинные слова на короткие мягким переносом (исключая ссылки) 
	$words = array();
	foreach(explode(" ", $text) as $word)
	{
		if($maxWordLen == 0 
			|| mb_strlen($word) <= $maxWordLen
			|| preg_match("#(https?://|ftp://|www\.)\S+[^\s.,>)\];'\"!?]#", $word))
		{
			$words[] = $word . " ";
			continue;
		}

		while(mb_strlen($word) > $maxWordLen)
		{
			$words[] = mb_substr($word, 0, $maxWordLen) . "&shy;";
			$word = mb_substr($word, $maxWordLen, mb_strlen($word) - $maxWordLen);
		}
		$words[] = $word . " ";
	}

	// соединяю слова в строку, пока длина строки позволяет
	$string = "";
	$showTitle = false;
	foreach($words as $word)
	{
		if(mb_strlen(trim(str_replace("&shy;", "", $word))) == 0)
			continue;

		$checkString = trim(str_replace("&shy;", "", $string . $word));
		if($maxStringLen > 0 && mb_strlen($allowHtml ? strip_tags($checkString) : $checkString) > $maxStringLen)
		{
			$string = trim($string) . "…";
			if($allowTitle)
				$showTitle = true;
			break;
		}

		$string = $string . $word;
	}

	$string = trim($string);
	
	// если там html, то закрою незакрытые теги
	if($allowHtml)
	{
		$ignoreTags = array("br");
		if(preg_match_all("/<(\/?)(\w+)/", $string, $matches, PREG_SET_ORDER)) 
		{
			$openedTagsStack = array();
			foreach($matches as $k => $tag) 
			{
				$tagName = strtolower($tag[2]);
				if($tag[1])
				{
					// если тэг закрывается, то удаляем из стека
					if(end($openedTagsStack) == $tagName) 
						array_pop($openedTagsStack);
				}
				else 
				{
					// если тэг открывается и он не одиночный, то кладем в стек
					if(!in_array($tagName, $ignoreTags))
						array_push($openedTagsStack, $tagName);
				}
			}
			while($tag = array_pop($openedTagsStack)) 
				$string .= "</$tag>";
		}
	}
	
	if(!$allowHtml)
	{
		$string = htmlspecialchars($string);
		$string = str_replace("&amp;shy;", "&shy;", $string);
	}
	
	if(!$showTitle)
		return $string;
	
	$text = strip_tags($text);
	$text = str_replace("\"", "'", $text);
	
	return "<span title=\"$text\" >$string</span>";
}
?>