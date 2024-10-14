<?php 
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(dirname(__FILE__))));
}

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('MVC_LIB')) {
	define ('MVC_LIB', ROOT . DS . 'lib' . DS);
}
if (!defined('MVC_APP')) {
	define ('MVC_APP', ROOT . DS . 'app' . DS);
}

if(!include(MVC_LIB . 'bootstrap.php')){
	die ('MVC Core Not Found!!!');
}
if(!include(MVC_APP . 'Config/core.php')){
	die ('MVC Core Not Found!!!');
}

App::uses('RequestHandler', 'Core');
App::uses('Dispatcher', 'Core');
$RequestHandler = new RequestHandler;
$Dispatcher = new Dispatcher($RequestHandler);

?>