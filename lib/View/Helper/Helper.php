<?php
class Helper{
	public $view = null;
	public $options = array();
	
	function __construct($view, $options = array()){
		$this->view = $view;
		$this->options = $options;
		
		foreach ($this->helpers as $helper){
			$this->_loadHelper($helper, $view, $options);
		}

	}
	
	protected function _loadHelper($helper, $view, $options){
		$hName = $helper . 'Helper';
		App::uses($hName, 'View' . DS . 'Helper');
		$this->{$helper} = new $hName($view, $options);
	}
}
?>