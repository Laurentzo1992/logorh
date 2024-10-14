<?php 
/**
 * Class RequestHandler
 */

class RequestHandler {
	private $_prettyUrl = PRETTY_URL;
	
	public function getParams () {
		$params = array('model'=>DEFAULT_MODEL, 'view'=>'index', 'p'=>array());
		if ($this->_prettyUrl) {
			// Operate With Htaccess
			$vars = substr($_SERVER['REQUEST_URI'], (strlen($this->getUrlRoot())));
			$vars = explode('/', $vars);
			if(isset($vars[0]) && $vars[0]<>''){
				$params['model'] = array_shift($vars);
			}
			if(isset($vars[0]) && $vars[0]<>''){
				$params['view'] = array_shift($vars);
				if(!empty($vars)){
					$params['p'] = $vars;
				}
			}
		}else{
			// Operate php pretty Url
			if(isset($_SERVER['PATH_INFO'])){
				$vars = explode('/', $_SERVER['PATH_INFO']);
				array_shift($vars);
				if(isset($vars[0]) && $vars[0]<>''){
					$params['model'] = array_shift($vars);
				}
				if(isset($vars[0]) && $vars[0]<>''){
					$params['view'] = array_shift($vars);
					if(!empty($vars)){
						$params['p'] = $vars;
					}
				}
			}
		}
		return $params;
	}
	
	public function getUrlRoot($index = false) {
		$urlRoot = '';
		if ($this->_prettyUrl) {
			// Operate With Htaccess
			$urlRoot = substr($_SERVER['SCRIPT_NAME'], 0, -18);
		}else{
			// Operate With pretty Url
			$urlRoot = substr($_SERVER['SCRIPT_NAME'], 0, -9);
		}
		if($index && !$this->_prettyUrl) $urlRoot .= 'index.php/';
		return $urlRoot;
	}
}
?>