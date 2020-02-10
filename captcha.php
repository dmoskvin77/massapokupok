<?php
/**
 * Показ капчи и ее проверка
 * 
 * @version $Id: captcha.php 9 2010-01-19 11:00:28Z afi $
 * @author Andrey Filippov
 */

require_once "app/Config/framework.php";
require_once APPLICATION_DIR . "/Lib/kcaptcha/kcaptcha.php";

$task = Request::getVar("task", "new");

Context::start(Configurator::get("application:name"));

// показываем новую картинку
if ("new" == $task)
{
	$captcha = new KCAPTCHA();
	$code = $captcha->getKeyString();
	Session::set("captcha", $code);
}

// проверка введенных символов
if("check" == $task)
{
	$stored = Session::get("captcha");
	$captcha = Request::getVar("captcha");
	echo $stored == $captcha ? "true" : "false";
}

exit();
?>