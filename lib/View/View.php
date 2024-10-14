<?php 
/**
 * 
 * View Class
 * 
 */
App::uses('Helper', 'View' . DS . 'Helper');
class View {
	public $controller = null;
	
	function __construct($controller) {
		$this->controller = $controller;
		foreach ($this->controller->helpers as $helper){
			$this->_loadHelper($helper);
		}
		$this->_init();
	}
	
	public function requestAction($controller, $action, $params = array(), $return = 'values', $requestHandler = null) {
		include_once(MVC_APP . 'Controller' . DS . $controller . 'Controller.php');
		$requestHandler = ($requestHandler)?$requestHandler:$this->controller->requestHandler;
		$controllerName = $controller . 'Controller';
		${$controller} = new $controllerName($action, $requestHandler);
		if($return =='values'){
			return call_user_func_array(array(${$controller}, $action), (array)$params);
		}elseif($return == 'controller'){
			call_user_func_array(array(${$controller}, $action), (array)$params);
			return ${$controller};
		}
	}
	
	protected function _loadHelper($helper){
		$hName = $helper . 'Helper';
		App::uses($hName, 'View' . DS . 'Helper');
		$this->{$helper} = new $hName($this);
	}
	
	protected function _init() {
		if(!$this->controller->redirect){
			echo $this->fetch('layout');
			foreach ($this->controller->helpers as $helper){
				unset($this->{$helper});
			}
		}
	}
	
	public function fetch($content) {
		$stream = false;
		global $DATABASE_ERROR;
		if($content == 'layout'){
			$stream = MVC_APP . 'View' . DS . $this->controller->layoutPath . DS . $this->controller->layout . '.' . $this->controller->ext;
		}
		if($content == 'content' && !$DATABASE_ERROR){
			$stream = MVC_APP . 'View' . DS . $this->controller->viewPath . DS . $this->controller->view . '.' . $this->controller->ext;
		}else{
			$_SESSION['flashData'][] = array('message'=>$DATABASE_ERROR[2], 'class'=>'flash error');
		}
		
		foreach ($this->controller->helpers as $helper){
			$var = $helper;
			$$var = $this->{$helper};
		}
		foreach ($this->controller->viewVars as $name =>$viewVar){
			$var = $name;
			$$var = $viewVar;
		}
		if($stream){
			ob_start();
			include ($stream);
			return ob_get_clean();
		}
	}
	
}
?>