<?php
/**
* Утилиты для работы со строками, массивами и ссылками
*/
class Utility
{
	/**
	* Функция урезает строку $text до $maxLen символов, с учетом апострофов
	*
	* @param string $text - строка
	* @param int $maxLen - максимальная длина строки
	* @param bool $allowHtml - разрешены ли теги html
	* @return string
	*/
	public static function limitString($text, $maxLen, $allowHtml = false)
	{
		if(!$allowHtml)
		{
			$text = stripslashes($text);
			$text = htmlspecialchars_decode($text);
		}

		if(mb_strlen($text, "utf-8") > $maxLen)
			$text = mb_substr($text, 0, $maxLen, "utf-8");

		if(!$allowHtml)
		{
			$text = htmlspecialchars($text);
			$text = addslashes($text);
		}

		return $text;
	}

	/**
	* Функция возвращает число и слово в нужном падеже, в зависимости от числа
	*
	* @param int $count - количество
	* @param string $form1 - склонение 1
	* @param string $form2 - склонение 2
	* @param string $form5 - склонение 5
	* @param bool $merge - объединять или нет количество и склонение
	* @return string
	*/
	public static function declension($count, $form1, $form2, $form5, $merge = true)
	{
		$count = $count ? $count : 0;

	    $number = abs($count) % 100;
	    if ($number > 10 && $number < 20)
	    	return $merge ? "$count $form5" : $form5;

	    $number = $number % 10;
	    if ($number > 1 && $number < 5)
	    	return $merge ? "$count $form2" : $form2;

	    if ($number == 1)
	    	return $merge ? "$count $form1" : $form1;

	    return $merge ? "$count $form5" : $form5;
	}

	/**
	* Функция возвращает первые $limit элементов массива
	*
	* @param array object $array - массив
	* @param int $limit - количество элементов которые должны вернуться
	* @return array object
	*/
	public static function limit($array, $limit)
	{
		if(!$array)
			return $array;

    	$chunk = array_chunk($array, $limit, true);
    	return $chunk[0];
	}

	/**
	 * Функция подготавливает строку для почты
	 *
	 * @param string $inputStr
	 * @return string
	 */
	public static function prepareStringForMail($inputStr)
	{
		$outputStr = str_replace("&quot;",'"',$inputStr);
		$outputStr = str_replace("&lt;",'<',$outputStr);
		$outputStr = str_replace("&gt;",'>',$outputStr);
		$outputStr = str_replace("\r\n","<br />",$outputStr);
		$outputStr = str_replace("\n","<br />",$outputStr);

		return $outputStr;
	}

	/**
	* Функция возвращает случайные $limit элементов из массива $array. если $limit не задан, то просто перемешивает
	*
	* @param array object $array - массив
	* @param int $limit - количество элементов которые должны вернуться
	* @return array object
	*/
	public static function random($array, $limit = null)
	{
		if(!$array)
			return $array;

		shuffle($array);

		if($limit)
			$array = Utility::limit($array, $limit);

		return $array;
	}

	/**
	* Функция сортирует массив $array по ключам $key так, как они находятся в массиве $ids
	*
	* @param array int $array - массив ключей
	* @param array object/array $array - массив
	* @param string $key - по какому элементу объекта/массива сортировать
	* @return array object
	*/
	public static function sort($ids, $array, $key = "id")
	{
		if(!$ids || !$array)
			return array();

		$unsorted = array();
		foreach($array as $item)
		{
			if(is_array($item))
			{
				if(array_key_exists($key, $item))
					$unsorted[$item[$key]] = $item;
			}
			else
				$unsorted[$item->$key] = $item;
		}

		$sorted = array();
		foreach($ids as $id)
			if(array_key_exists($id, $unsorted))
				$sorted[$id] = $unsorted[$id];

		return $sorted;
	}

	/**
	* Функция конвертирует url в формат slash
	*
	* @param string $url - ссылка в формате "a?b=c"
	* @return string
	*/
	public static function toSlashUrl($url)
	{
		$url = str_replace("index.php", "", $url);
		$url = str_replace("?show=", "", $url);
		$url = preg_replace("/[&=]/", "/", $url);

		if($url[0] != "/" && strpos($url, "http://") === false && strpos($url, "https://") === false)
			$url = "/$url";

		return $url;
	}

	/**
	* Функция конвертирует url в формат get
	*
	* @param string $url - ссылка в формате "a/b/c"
	* @return string
	*/
	public static function toGetUrl($url)
	{
		$split = explode("/", $url);
		$url = "";

		foreach($split as $key => $value)
		{
			if($key == 0)
				$url .= "/";
			else if($key == 1)
				$url .= $value;
			else if($key == 2)
				$url .= "?{$value}";
			else if($key % 2)
				$url .= "={$value}";
			else
				$url .= "&amp;{$value}";
		}

		return $url;
	}

	/**
	 * Функция рекурсивно удаляет каталог
	 *
	 * @param string $dir Каталог
	 * @param boolean $deleteRootToo Удалить указанный каталог
	 * @return boolean Результат выполнения
	 */
	public static function unlinkRecursive($dir, $deleteRootToo)
	{
		foreach (glob($dir . '/*') as $one)
			if (is_file($one))
				unlink($one);
			elseif (is_dir($one) && $one != '.' && $one != '..')
				self::unlinkRecursive($one, true);

		return $deleteRootToo ? rmdir($dir) : true;
	}


	// преобразование даты от датапикера в unixtime
	public static function pickerDateToTime($strData)
	{
		$strData = trim($strData);
		$timeArr = array(0, 0, 0);
		// 30.07.2010 12:30
		// проверим не было ли выбрано время
		$seArr = explode(" ", $strData);
		if (count($seArr) == 2)
		{
			$timeArr = explode(":", $seArr[1]);
			$timeArr[2] = 0;
			$deArr = explode(".", $seArr[0]);
		}
		else
		{
			$deArr = explode(".", $strData);
		}

		if (count($deArr) == 3)
		{
			return mktime($timeArr[0], $timeArr[1], $timeArr[2], $deArr[1], $deArr[0], $deArr[2]);
		}
		else
			return null;

	}

	// преобразование unit time() в дату как у датапикера
	public static function pickerTimeToDate($timestamp)
	{
		// инфа здесь - http://www.php.net/manual/en/function.strftime.php
		return strftime('%d.%m.%Y', $timestamp);
	}


	public static function getRnd4Diz()
	{
		return rand(1,4);
	}


	public static function getRefUrl()
	{
		// откуда пришел запрос
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$refURL = $_SERVER['HTTP_REFERER'];
			if (isset($_SERVER['HTTPS']))
			{
				$cleanUrl = str_replace("https://www.".$_SERVER['HTTP_HOST']."/", "", $refURL);
				if (strstr($cleanUrl, "htt"))
					$cleanUrl = str_replace("https://".$_SERVER['HTTP_HOST']."/", "", $refURL);
			}
			else
			{
				$cleanUrl = str_replace("http://www.".$_SERVER['HTTP_HOST']."/", "", $refURL);
				if (strstr($cleanUrl, "htt"))
					$cleanUrl = str_replace("http://".$_SERVER['HTTP_HOST']."/", "", $refURL);
			}
			return $cleanUrl;
		}

	}

	public static function rus2lat($str)
	{
		$ttable = array(
		"й" => "y",
		"ц" => "ts",
		"у" => "u",
		"к" => "k",
		"е" => "e",
		"н" => "n",
		"г" => "g",
		"ш" => "sh",
		"щ" => "shh",
		"з" => "z",
		"х" => "h",
		"ъ" => "",
		"ф" => "f",
		"ы" => "y",
		"в" => "v",
		"а" => "a",
		"п" => "p",
		"р" => "r",
		"о" => "o",
		"л" => "l",
		"д" => "d",
		"ж" => "zh",
		"э" => "je",
		"я" => "ia",
		"ч" => "ch",
		"с" => "s",
		"м" => "m",
		"и" => "i",
		"т" => "t",
		"ь" => "",
		"б" => "b",
		"ю" => "ju",
		"ё" => "yo",
		" " => "",
		"-" => "",
		"Й" => "Y",
		"Ц" => "TS",
		"У" => "U",
		"К" => "K",
		"Е" => "E",
		"Н" => "N",
		"Г" => "G",
		"Ш" => "SH",
		"Щ" => "SHH",
		"З" => "Z",
		"Х" => "H",
		"Ъ" => "",
		"Ф" => "F",
		"Ы" => "Y",
		"В" => "V",
		"А" => "A",
		"П" => "P",
		"Р" => "R",
		"О" => "O",
		"Л" => "L",
		"Д" => "D",
		"Ж" => "ZH",
		"Э" => "JE",
		"Я" => "IA",
		"Ч" => "CH",
		"С" => "S",
		"М" => "M",
		"И" => "I",
		"Т" => "T",
		"Ь" => "",
		"Б" => "B",
		"Ю" => "JU",
		"Ё" => "Yo",
		);
		$result = strtr($str, $ttable);
		return $result;
	}

	// выделяет имя хоста из ссылки
	public static function prepareHostName($url)
	{
		if (!$url || $url == '')
			return false;

		$url = mb_strtolower($url, 'utf8');
		if (strstr($url, 'http://') === false && strstr($url, 'https://') === false)
			$url = 'http://'.$url;

		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs))
		{
			$url = $regs['domain'];
			$url = str_replace('..', '', $url);
			$url = str_replace(',', '', $url);
			$url = str_replace('_', '', $url);
			$url = str_replace('*', '', $url);
			$url = str_replace('^', '', $url);
			$url = str_replace('#', '', $url);
			$url = str_replace('@', '', $url);
			$url = str_replace(' ', '', $url);
			$url = str_replace('(', '', $url);
			$url = str_replace(')', '', $url);
			$url = str_replace('=', '', $url);

			$getLastPosSymb = substr($url, mb_strlen($url)-1, 1);
			if ($getLastPosSymb == '.')
				$url = substr ($url, 0, -1);

			return $url;
		}

		return false;
	}

	// выделает центральную часть ссылки (домен без .ru)
	public static function prepareMainUrlPart($url, $isSearch = false)
	{
		$stopArray = array('www', 'ww', 'http', 'https');
		$url = mb_strtolower($url,'utf8');

		$urlParts = explode('.', $url);
		if (!count($urlParts) && !$isSearch)
			return false;

		if (!count($urlParts) && $isSearch)
		{
			if (in_array($url, $stopArray))
				return false;

			return $url;
		}

		$mainPart = $urlParts[0];
		if (in_array($mainPart, $stopArray))
			return false;

		return $mainPart;

	}

	// преобразование даты
	public static function dateFormat($string, $format = "d-m-Y")
	{
		$monthShortEn = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$monthLongEn = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$daysLongEn = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		$daysShortEn = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

		$monthShortRu = array("янв", "фев", "мар", "апр", "мая", "июн", "июл", "авг", "сен", "окт", "ноя", "дек");
		$monthLongRu = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
		// $monthLongRu = array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
		$daysRu = array("понедельник", "вторник","среда","четверг","пятница","суббота","воскресенье");

		// если это timestamp
		if (preg_match("/^[0-9]+$/", $string))
			$string = date("c", $string);

		$date = new XDateTime($string);
		$txt = $date->format($format);

		$txt = str_replace($monthLongEn, $monthLongRu, $txt);
		$txt = str_replace($monthShortEn, $monthShortRu, $txt);
		$txt = str_replace($daysLongEn, $daysRu, $txt);
		$txt = str_replace($daysShortEn, $daysRu, $txt);
		return $txt;
	}

	// получить содержимое страницы по ссылке
	public static function getContent($url)
	{
		$ch = curl_init($url);

		// Параметры курла
		curl_setopt($ch, CURLOPT_USERAGENT, 'IE20');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');

		// Получаем html
		$text = curl_exec($ch);
		curl_close($ch);

		return $text;

	}

	// html -> bbcode
	public static function html2bbcode($text)
	{
		if (!$text || $text == '')
			return false;

		$text = str_replace("</p>", "
", $text);

		$text = str_replace("<br>", "
", $text);

		$text = str_replace("<br/>", "
", $text);

		$text = str_replace("<br />", "
", $text);

		$text = str_replace("<p>", "", $text);

		$text = strip_tags($text);

		return $text;

	}

	// bbcode -> html
	public static function bbcode2html($text)
	{
		if (!$text || $text == '')
			return false;

		$str_search = array(
			"#\\\n#is",
			"#\[table\](.+?)\[\/table\]#is",
			"#\[tr\](.+?)\[\/tr\]#is",
			"#\[td\](.+?)\[\/td\]#is",
			"#\[b\](.+?)\[\/b\]#is",
			"#\[i\](.+?)\[\/i\]#is",
			"#\[u\](.+?)\[\/u\]#is",
			"#\[code\](.+?)\[\/code\]#is",
			"#\[quote\](.+?)\[\/quote\]#is",
			"#\[url=(.+?)\](.+?)\[\/url\]#is",
			"#\[url\](.+?)\[\/url\]#is",
			"#\[img\](.+?)\[\/img\]#is",
			"#\[img width=(.+?),height=(.+?)\](.+?)\[\/img\]#is",
			"#\[video\](.+?)\[\/video\]#is",
			"#\[size=(.+?)\](.+?)\[\/size\]#is",
			"#\[color=(.+?)\](.+?)\[\/color\]#is",
			"#\[list\](.+?)\[\/list\]#is",
			"#\[list=(1|a|I)\](.+?)\[\/list\]#is",
			"#\[\*\](.+?)\[\/\*\]#"
		);

		$str_replace = array(
			"<br />",
			"<table>\\1</table>",
			"<tr>\\1</tr>",
			"<td>\\1</td>",
			"<b>\\1</b>",
			"<i>\\1</i>",
			"<span style='text-decoration:underline'>\\1</span>",
			"<code class='code'>\\1</code>",
			"<table width = '95%'><tr><td>Цитата</td></tr><tr><td class='quote'>\\1</td></tr></table>",
			"<a href='\\1' target='_blank'>\\2</a>",
			"<a href='\\1' target='_blank'>\\1</a>",
			"<img src='\\1' />",
			"<img width='\\1' height='\\2' src='\\3' />",
			"<iframe src='http://www.youtube.com/embed/\\1' width='400' height='300' frameborder='0'></iframe>",
			"<span style='font-size:\\1%'>\\2</span>",
			"<span style='color:\\1'>\\2</span>",
			"<ul>\\1</ul>",
			"<ol type='\\1'>\\2</ol>",
			"<li>\\1</li>"
		);

		$text = preg_replace($str_search, $str_replace, $text);

		// смайлы
		$text = str_replace(':)', '<img src="/images/smiles/sm1.png" class="sm">', $text);
		$text = str_replace(':D', '<img src="/images/smiles/sm2.png" class="sm">', $text);
		$text = str_replace(';)', '<img src="/images/smiles/sm3.png" class="sm">', $text);
		$text = str_replace(':up:', '<img src="/images/smiles/sm4.png" class="sm">', $text);
		$text = str_replace(':down:', '<img src="/images/smiles/sm5.png" class="sm">', $text);
		$text = str_replace(':shock:', '<img src="/images/smiles/sm6.png" class="sm">', $text);
		$text = str_replace(':angry:', '<img src="/images/smiles/sm7.png" class="sm">', $text);
		$text = str_replace(':(', '<img src="/images/smiles/sm8.png" class="sm">', $text);
		$text = str_replace(':sick:', '<img src="/images/smiles/sm9.png" class="sm">', $text);

		return $text;
	}

}

