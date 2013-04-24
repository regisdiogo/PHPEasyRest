<?php
error_reporting(E_ALL ^ E_WARNING);

define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR);
define('ACTION_PATH', ROOT_PATH.DIRECTORY_SEPARATOR.'action'.DIRECTORY_SEPARATOR);

require(ROOT_PATH.'PHPEasyRest.php');

PHPEasyRest::getInstance()->init();
?>
