<?php
class SessionHelper extends Helper{
	
	public $helpers = array();

	function __construct($view = null, $options = array()) {
		parent::__construct($view, $options = array());
		$this->startSession();
	}

	public function write($key, $content) {
		if(is_array($key)){
			$key = array_reverse($key);
			$data = $content;
			foreach ($key as $k){
				$data = array($k=>$data);
			}
			$_SESSION = $data;
		}else{
			$_SESSION[$key] = $content;
		}
	}
	
	public function check($key) {
		if(isset($_SESSION[$key])){
			return true;
		}
		return false;
	}
	
	public function read($key) {
		$data = null;
		if(is_array($key)){
			$key = array_reverse($key);
			$data = $_SESSION;
			foreach ($key as $k) {
				if (is_array($data) && isset($data[$key])) {
					$data =& $data[$key];
				} else {
					return null;
				}
			}
		}elseif($this->check($key)){
			$data = $_SESSION[$key];
		}
		return $data;
	}
	
	public function delete($key) {
		if($this->check($key)){
			unset($_SESSION[$key]);
			return true;
		}
		return false;
	}
	

	public function setFlash($message, $class = 'flash success'){
		$_SESSION['flashData'][] = array('message'=>$message, 'class'=>$class);
	}
	
	public function flash() {
		$o = '';
		if($this->check('flashData')){
			foreach ($this->read('flashData') as $flashData){
				if(isset($flashData['message']) && isset($flashData['class'])){
					$o .= '<div class="'.$flashData['class'].'">' . $flashData['message'] . '</div>';
				}
			}
			$this->delete('flashData');
		}
		return $o;
	}
	
	public function startSession() {
		if(!isset($_SESSION)){
			session_start();
		}
	}
	
	public function destroySession() {
		if(!isset($_SESSION)){
			session_destroy();
		}
	}
}
?>