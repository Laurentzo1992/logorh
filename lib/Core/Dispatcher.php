<?php 
/**
 * 
 * Dispatcher
 * 
 */

class Dispatcher {
	protected $_base = null; // Default Model Goes here
	protected $_controller = null;
	protected $_view = 'index';
	protected $_params = null;
	
	function __construct(RequestHandler $RequestHandler) {
		$params = $RequestHandler->getParams();
		$this->_controller = $this->_getController($params['model']);
		$this->_view = $params['view'];
		$this->_params = $params['p'];
			
		$this->_init($RequestHandler);
	}
	
	protected function _getController($model){
		$controller = '';
		if ($handle = opendir(MVC_APP . 'Controller' . DS)) {
		    while (false !== ($file = readdir($handle))) {
				if(strtolower($file) === strtolower($model . 'Controller.php')){
					$controller = substr($file, 0, -4);
				}
		    }
		    closedir($handle);
		}
		return $controller;
	}
	
	protected function _init(RequestHandler $RequestHandler) {
		App::uses('ConnectionManager', 'Model');
		App::uses('Helper', 'View' . DS . 'Helper');
		include (MVC_APP . 'Controller' . DS . 'AppController.php');
		include (MVC_APP . 'Model' . DS . 'AppModel.php');
		include (MVC_APP . 'Controller' . DS . $this->_controller . '.php');
		$controller = new $this->_controller($this->_view, $RequestHandler);
		call_user_func_array(array($controller, $this->_view), (array)$this->_params);
		
		App::uses($controller->viewClass, 'View');
		$view = new $controller->viewClass($controller);
		
	}
}
?>