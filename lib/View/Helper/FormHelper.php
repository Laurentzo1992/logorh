<?php
class FormHelper extends Helper{	
	public $helpers = array('Html');
	public $data = array();
	
	function __construct($view = null, $options = array()) {
		parent::__construct($view, $options);
		$this->data = $view->controller->data;
	}
	
	public function create($options = array()){
		$default = array(
			'method'=>'POST',
			'action'=>$this->Html->getUrl('full'),
			'id'=>ucfirst(strtolower($this->view->controller->model)) . ucfirst(strtolower($this->view->controller->view)) . 'Form'
		);
		$options = array_merge($default, $options);
		return $this->Html->tag('form', null, $options);
		
	}
	/**
	 * array(name=>;, options[])
	 */
	public function end($submit=null){
		$default = array(
			'type'=>'submit',
			'value'=>(isset($submit['name'])) ? $submit['name'] : 'execute',
		);
		$out = '';
		if(is_array($submit)){
			$name = $submit['name'];
			unset($submit['name']);
			$out .= " \t\n " .$this->Html->useTag('input', $name, array_merge($default, $submit));
		}
		
		$out .= " \r\n " .'</form>';
		return $out;
	}
	
	public function input($fieldName, $options = array(), $data = null){
		$data = ($data)?$data:$this->data;
		$default = array(
			'before'=>'',
			'between'=>'',
			'after'=>'',
			'id'=>$this->_getFieldId($fieldName),
			'name'=>$fieldName,
			'type'=>'text',
			'value'=>'',
			'label'=>null,
			'options'=>null,
			'format'=>true
		);
		$options = array_merge($default, $options);
		
		if($options['format']){
			$path = $fieldName;
			$fieldName = $this->_formatFieldName($fieldName);
			if($options['value']==''){
				$options['value'] = $this->_getArrayData($path, $data);
			}else{
				if($options['type']=='checkbox'){
					$options['checked'] = (bool)$this->_getArrayData($path, $data);
				}
			}
			if($options['value']=='empty')$options['value'] = '';
		}
		$out = $options['before'];
		
		if($options['label']){
			$out .= $this->label($options);				
			$out .= $options['between'];
		}
		$out .= $this->{$options['type']}($fieldName, $options);
		
		$out .= $options['after'];
		return " \t\n " .$out;
		
	}
	public function label($options){
		return $this->Html->tag('label', $options['label'], array('for'=>$options['id']));
	}
	public function password($fieldName, $options){
		$default = array(
			'type'=>'password',
		);
		if(isset($options['populate']) && $options['populate']==false){
			unset($options['value']);
			unset($options['populate']);
		}
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	public function hidden($fieldName, $options){
		$default = array(
			'type'=>'hidden',
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	public function text($fieldName, $options){
		$default = array(
			'type'=>'text',
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	/*---------------------------------*/
	public function heure($fieldName, $options){
		$default = array(
			'type'=>'time',
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	
	
	public function mouton($fieldName, $options){
		$default = array(
			'type'=>'date',
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	
	public function month($fieldName, $options){
		$default = array(
			'type'=>'month',
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	
	public function email($fieldName, $options){
		$default = array(
			'type'=>'email'
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	
	public function telephone($fieldName, $options){
		$default = array(
			'type'=>'tel'
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	
	public function number($fieldName, $options){
		$default = array(
			'type'=>'number'
		);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		unset($options['min']);
		unset($options['max']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	/*---------------------------------*/
	public function textarea($fieldName, $options){
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		$options['name'] = $fieldName;
		$value = ($options['value'])?$options['value']:'';
		unset($options['value']);
		return $this->Html->tag('textarea', $value, $options);
	}
	
	public function checkbox($fieldName, $options){
		$default = array(
			'type'=>'checkbox',
		);
		$out = '';
		if(isset($options['addHidden']) && $options['addHidden']===false){
			
		}else{
			$optionChk = $options;
			$optionChk['value'] = (isset($options['hiddenField']))?$options['hiddenField']:0;
			unset($optionChk['id']);
			unset($optionChk['checked']);
			$out .= "\t\n" .$this->hidden($fieldName, $optionChk);
		}
		
		unset($options['hiddenField']);
		unset($options['addHidden']);
		unset($options['before']);
		unset($options['between']);
		unset($options['after']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		$out .= "\t\n" .$this->Html->useTag('input', $fieldName, array_merge($default, $options));
		return $out;
	}
	public function radio($fieldName, $options){
		$default = array(
			'type'=>'ratio',
			'between'=>'<br/>'
		);
		$options = array_merge($default, $options);
		unset($options['before']);
		unset($options['after']);
		unset($options['name']);
		unset($options['label']);
		$noLabel = (isset($options['nolabel']))?$options['nolabel']:false;
		$inputOpts = $options['options'];
		unset($options['options']);
		$between = $options['between'];
		unset($options['between']);
		unset($options['format']);
		$out = '';
		foreach ($inputOpts as $value) {
			$newOptions = $options;
			$newOptions['id'] = $newOptions['id'].$value;
			$newOptions['value'] = $value;
			if($options['value']==$value){
				$newOptions['checked']='checked';
			}
			$out .= $this->Html->useTag('input', $fieldName,  $newOptions);
			if(!$noLabel){
				$newOptions['label'] = $value;
				$out .= $this->label($newOptions) . $between;
			}
		}
		return $out;
	}
	public function select($fieldName, $options){
		$default = array(
			'empty'=>false,
			'options'=>array(),
			'selectClass'=>'',
			'optionClass'=>'',
		);
		$options = array_merge($default, $options);
		$selectOptions = $options;
		$selectOptions['name'] = $fieldName;
		unset($selectOptions['before']);
		unset($selectOptions['between']);
		unset($selectOptions['after']);
		unset($selectOptions['type']);
		unset($selectOptions['value']);
		unset($selectOptions['label']);
		unset($selectOptions['options']);
		unset($selectOptions['format']);
		unset($selectOptions['empty']);
		$selectOptions['class'] = $selectOptions['selectClass'];
		unset($selectOptions['optionClass']);
		unset($selectOptions['selectClass']);
		$out = $this->Html->tag('select', null, $selectOptions);
		
		if($options['empty']){
			$out .= " \t\n " . $this->Html->tag('option', $options['empty'], array('value'=>'', 'class'=>$options['optionClass']));
		}
		
		foreach ($options['options'] as $key=>$value) {
			if($key==$options['value']){
				$out .= " \t\n " . $this->Html->tag('option', $value, array('value'=>$key, 'selected'=>'selected', 'class'=>$options['optionClass']));;
			}else{
				$out .= " \t\n " . $this->Html->tag('option', $value, array('value'=>$key, 'class'=>$options['optionClass']));;
			}
			
		}
		
		$out .= '</select>';
		return $out;
	}
	public function file($fieldName, $options){
		$default = array(
			'type'=>'file',
		);
		unset($options['before']);
		unset($options['after']);
		unset($options['between']);
		unset($options['name']);
		unset($options['type']);
		unset($options['value']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	public function submit($fieldName, $options){
		$default = array(
			'type'=>'submit',
		);
		unset($options['before']);
		unset($options['after']);
		unset($options['between']);
		unset($options['name']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		return $this->Html->useTag('input', $fieldName, array_merge($default, $options));;
	}
	public function button($fieldName, $options){
		unset($options['before']);
		unset($options['after']);
		unset($options['between']);
		unset($options['type']);
		unset($options['label']);
		unset($options['options']);
		unset($options['format']);
		$options['name'] = $fieldName;
		$value = $options['value'];
		unset($options['value']);
		return $this->Html->tag('button', $value, $options);
	}
	public function date($fieldName, $options){
		if(!isset($options['dateFormat']))$options['dateFormat']='d-m-Y';
		$options['class'] = (isset($options['class']))?$options['class'] . ' dateSelect':'dateSelect';
		if($options['value']<>'') $options['value'] = date($options['dateFormat'], strtotime($options['value']));
		return $this->text($fieldName, $options);
	}
	public function dateTime($fieldName, $options){
		$options['class'] = (isset($options['class']))?$options['class'] . ' datetimeSelect':'datetimeSelect';
		$options['value'] = date('d-m-Y H:i:s', strtotime($options['value']));
		return $this->text($fieldName, $options);
	}
	public function error(){}
	public function isFieldError(){}
	public function inputDefault(){}
	
	protected function _formatFieldName($fieldName){
		if(!strpos($fieldName, '.')){
			$model=$this->view->controller->{$this->view->controller->model}->alias;
			$fieldName = "{$model}.{$fieldName}";
		}
		return 'data[' . str_replace(array('[]', '.'), array('',']['), $fieldName) . ']';
	}
	
	protected function _getArrayData($path, $data){
		if (empty($data) || empty($path)) {
			return null;
		}
		if(!strpos($path, '.')){
			$model=$this->view->controller->{$this->view->controller->model}->alias;
			$path = "{$model}.{$path}";
		}
		if (is_string($path) || is_numeric($path)) {
			$parts = explode('.', $path);
		} else {
			$parts = $path;
		}
		foreach ($parts as $key) {
			if (is_array($data) && isset($data[$key])) {
				$data =& $data[$key];
			} else {
				return null;
			}
		}
		return $data;
	}
	
	protected function _getFieldId($fieldName){
		if(!strpos($fieldName, '.')){
			$model=$this->view->controller->{$this->view->controller->model}->alias;
			$fieldName = "{$model}.{$fieldName}";
		}
		$data = array_map('ucfirst', explode('.', $fieldName));
		return implode('', $data);
	}
}
?>