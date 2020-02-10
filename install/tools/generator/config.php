<?php

//require_once "../../../config/framework.php";
require_once  APPLICATION_DIR . "/Config/framework.php";

//define("CONFIG", "develop");
define("CONFIG", Request::getVar("config"));

define("DATABASE", Request::getVar("connection"));
//define("DATABASE", "database");

// Таблица, для к-рой будут строиться сущности
define ("CURRENT_TABLE", Request::getVar("table"));
//define ("CURRENT_TABLE", "test");
// Имя генерируемого класса
define ("CLASS_NAME", Request::getVar("class"));
//define ("CLASS_NAME", "Test");
?>