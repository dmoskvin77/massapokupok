<?php
/**
 */
function smarty_function_captcha()
{
	require_once APPLICATION_DIR . "/Lib/kcaptcha/kcaptcha.php";
	$captcha = new KCAPTCHA();
	$code = $captcha->getKeyString();
	Session::set("captcha", $code);
}
?>