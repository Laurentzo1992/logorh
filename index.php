<?php
define('APP_DIR', 'app');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('WEBROOT_DIR', 'boot');
define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);
require APP_DIR . DS . WEBROOT_DIR . DS . 'index.php';
?>