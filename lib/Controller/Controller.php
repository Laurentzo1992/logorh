<?php 
/**
 * 
 * Controller Class
 * 
 */
App::uses('Component', 'Controller' . DS . 'Component');
class Controller {
	public $model = null;
	public $paginate = null;
	public $helpers = array('Html', 'Form', 'Session', 'Paginator');
	public $components = array('Paginator', 'Session');
	public $uses = array(); //(Models to load)
	public $useModel = true;
	public $ext = 'rrm';
	public $viewClass = 'View';
	public $layoutPath = 'Layouts';
	public $viewPath = null;
	public $view = null;
	public $layout = 'default';
	public $validationErrors = array();
	public $viewVars = array();
	public $data = array();
	public $requestHandler = null;
	public $redirect = false;
	
	function __construct($view = 'index', RequestHandler $RequestHandler = null){
		if($this->view === null){
			$this->view = $view;
		}
		$this->requestHandler = $RequestHandler;
		if($this->viewPath === null){
			$this->viewPath = ucfirst(substr(get_class($this), 0, -10));
		}
		/**
		 * Load controller specific model
		 * */
		if($this->useModel){
			if($this->model === null){
				$this->model = $this->_getModelName();
			}elseif (is_array($this->model)){			
				$this->model = $this->model['name'];
			}
			$this->uses[] = $this->model;
		
			/**
			 * Load controller other models
			 * */
			foreach($this->uses as $key=>$model){
				if(is_array($model)){
					$this->loadModel($key, $model);
				}else {
					$this->loadModel($model);
				}
			}
		}
		
		foreach($this->components as $key=>$component){
			if(is_array($component)){
				$this->loadComponent($key, $component);
			}else {
				$this->loadComponent($component);
			}
		}
		
	}
	public function loadModel($model, $options = false){
		if(!include_once(MVC_APP . 'Model' . DS . $model . '.php')) die ('Couldn\' load model: ' .MVC_APP . 'Model' . DS . $model . '.php');
		$this->{$model} = new $model($options);
	}
	public function loadComponent($component, $options = array()){
		$cName = $component . 'Component';
		App::uses($cName, 'Controller' . DS . 'Component');
		$this->{$component} = new $cName($options);
	}
	public function render($view) {
		$this->view = $view;
	}
	public function set($name, $content) {
		$this->viewVars[$name] = $content;
	}
	/**
	 * option = controller, view, params = array(param1, param2)....
	 */
	public function redirect($options = array()) {
		if(is_array($options)){
			if(array_key_exists('controller', $options)){
				$controller = $options['controller'];
			}else{
				$controller= substr(get_class($this), 0, -10);
			}
		
			if(array_key_exists('view', $options)){
				$view = $options['view'];
			}else{
				$view = 'index';
			}
			$params = '';
			if(array_key_exists('params', $options)){
				$params = implode('/', $options['params']);
			}		
			$link = $controller . '/' . $view . '/' . $params;
		}else{
			$link = $options;
		}
		$link = $this->requestHandler->getUrlRoot(true) . $link;
		$this->redirect = true;
		header("Location: $link");
	}
	/**
	*/
	
	public function requestAction($controller, $action, $params = array()) {
		include_once(MVC_APP . 'Controller' . DS . $controller . 'Controller.php');
		$controllerName = $controller . 'Controller';
		${$controller} = new $controllerName(null, $this->requestHandler);
		return call_user_func_array(array(${$controller}, $action), (array)$params);
	}
	
	public function postData() {
		if(isset($_POST['data'])){
			return $_POST['data'];
		}
		return array();
	}
	/**
	 * 
	 */
	public function paginate($name) {
		$params = $this->Paginator->checkData($this->postData(), $this->getGetParam('paginate'. $name), $name, $this->paginate[$name]);
		
		if($params['Post']){
			$this->Session->write("Paginator.{$name}", array_merge_recursive((array)$params['Post'], (array)$this->Session->read("Paginator.{$name}")));
		}elseif($params['Post']===null){
			$this->Session->delete("Paginator.{$name}");
		}
		
		if(!isset($this->{$this->paginate[$name]['model']})){
			$this->loadModel($this->paginate[$name]['model']);
		}
		return $this->{$this->paginate[$name]['model']}->find('all', array_merge($this->paginate[$name],(array)$this->Session->read("Paginator.{$name}"), (array)$params['Get']));
	}
	
	public function hasMethod($method) {
		if (method_exists($this, $method)) {
			return true;
		}
		return false;
	}
	
	protected function _getModelName() {
		$model = '';
		if ($handle = opendir(MVC_APP . 'Model' . DS)) {
		    while (false !== ($file = readdir($handle))) {
		    	$ff = strtolower(substr($file, 0, -4));
		    	$cc = strtolower(substr(get_class($this), 0, -11));
				if($ff === $cc){
					$model = substr($file, 0, -4);
		    		closedir($handle);
					return $model;
				}
		    }
		}
	}

	protected function _getGetParam($paramName) {
		$requestData = $this->requestHandler->getParams();
		if(isset($requestData['p'])){
			foreach ($requestData['p'] as $param){
				$parts = explode(':', $param);
				if($parts[0]==$paramName){
					return urldecode($parts[1]);
				}
			}
		}
		return null;
	}
	
	public function getGetParam($paramName) {
		return $this->_getGetParam($paramName);
	}
		
	protected function _encode($str, $key = 'QBt2SbTHdUxyy4KIbfd'){
	     $block = mcrypt_get_block_size('rijndael_128', 'ecb');
	     $pad = $block - (strlen($str) % $block);
	     $str .= str_repeat(chr($pad), $pad);
	     return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB));
	}
	
	protected function _decode($str, $key = 'QBt2SbTHdUxyy4KIbfd'){ 
	     $str = base64_decode($str);
	     $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
	     $block = mcrypt_get_block_size('rijndael_128', 'ecb');
	     $pad = ord($str[($len = strlen($str)) - 1]);
	     $len = strlen($str);
	     $pad = ord($str[$len-1]);
	     return substr($str, 0, strlen($str) - $pad);
	}
	
}
?>