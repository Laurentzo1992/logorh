<?php 
/**
 * 
 * App Class
 * 
 */
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
	define ('MVC_APP', ROOT . DS . 'app' . DS);}
class App {
	public static function uses($className, $location) {
		include_once (MVC_LIB . $location . DS . $className . '.php');
	}
}
?>