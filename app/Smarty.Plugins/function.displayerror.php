<?php
/**
 * Show error from session
 */

function smarty_function_displayerror($params, &$smarty)
{
$ht_start = <<<EOT
<div class="alert alert-danger">
 	<ul class="ul-error">
EOT;

$ht_end = <<<EOT
	</ul>
</div>
EOT;

	$message = Context::getError();

	if ($message == null)
		return false;

	$res = "";
	$text = "";
	if (is_array($message))
	{
		for ($i=0; $i < count($message); $i++)
		{
			$res .= "<li>";
			$res .= smarty_function_displayerror_replace($message[$i]);
			$res .= "</li>";
		}
	}
	else
	{
		$res .= "<li>";
		$res .= smarty_function_displayerror_replace($message) ;
		$res .= "</li>";
	}

	return $ht_start. $res . $ht_end;
}

function smarty_function_displayerror_replace($string)
{
	$res = str_replace("%%VALIDATOR_REQUIRED_FIELD%%", " обязательное поле", $string);
	$res = str_replace("%%VALIDATOR_INCORRECT_VALUE%%", " некорректное значение", $res);
	$res = str_replace("%%VALIDATOR_MUSTBE_LESS_THEN%%", " значение должно быть меньше, чем", $res);
	$res = str_replace("%%SYMBOLS%%", "символов", $res);
	return $res;
}
?>