<?php
class PaginatorHelper extends Helper{	
	public $helpers = array('Form', 'Html', 'Session');
	
	protected function _checkData($name) {
		$data = $this->view->controller->Paginator->checkData(
			$this->view->controller->postData(),
			$this->view->controller->getGetParam('paginate'. $name),
			$name,
			$this->view->controller->paginate[$name]
		);
		return array_merge((array)$data['Post'], (array)$this->Session->read("Paginator.{$name}"), (array)$data['Get']);		
	}

	public function numbers($name, $options = array()) {
		$default = array(			
			'before'=>'',
			'after'=>'',
			'controller'=>substr(get_class($this->view->controller), 0, -10),
			'view'=>$this->view->controller->view,
			'modulus'=>4,
			'separator'=>'|',
			'tag'=>'span',
			'ellipsis'=>' ... ',
			'first'=>'<<',
			'last'=>'>>',
			'class'=>'',
			'currentClass'=>'',
			'counter'=>' Page {:page} of {:pages}'
		);
		$options = array_merge($default, $options);
		$limit = (empty($this->view->controller->paginate[$name]['limit']))? 30 : $this->view->controller->paginate[$name]['limit'];
		$params = array_merge($this->view->controller->paginate[$name], $this->_checkData($name));	
		if(isset($params['limit']) && $params['limit']!=10000){
			$limit = $params['limit'];
		}
		$countParams = $params;		
		unset($countParams['limit']);
		unset($countParams['fields']);
		$count = $this->view->controller->{$this->view->controller->paginate[$name]['model']}->find('count', $countParams);
		$nbPages = ceil($count/$limit);
		$page = $params['page'];
		
		$out = $options['before'];
		$start = 1;
		$end = $nbPages;
		$range = $options['modulus']*2 + 1;
		$before = false;
		$after = false;
		
		$link = array('controller'=>$options['controller'], 'view'=>$options['view']);
				
		if($range<=$nbPages){
			//paginate		
			if(($page-$options['modulus'])>1){
				//sprintf($link, );
				$out .= $this->Html->tag($options['tag'], $this->Html->link($options['first'], array_merge($link, array('params'=>$this->_getLink('1', 'page', $name)))));
				$out .= $this->Html->tag($options['tag'], $this->Html->link($options['ellipsis'], array_merge($link, array('params'=>$this->_getLink(($page-$options['modulus'])-1, 'page', $name)))));
				$start = $page-$options['modulus'];
			}else{
				$before = true;
			}
			if($page+$options['modulus']<$nbPages){
				$end = $page+$options['modulus'];
			}else{
				$after = true;
			}
			
			if(($end-$start)<$range){
				if($before)$end += $options['modulus']-($page-$start);
				if($after)$start -= $options['modulus']-($end-$page);
			}
			
			$out .= $options['separator'];
			
			for($i=$start; $i<=$end; $i++){
				if($i==$page){
					$out .= $this->Html->tag($options['tag'], '<b>' . $i . '</b>') . $options['separator'];
				}else{
					$out .= $this->Html->tag($options['tag'], $this->Html->link($i, array_merge($link, array('params'=>$this->_getLink($i, 'page', $name))))) . $options['separator'];
				}
			}
			
			if($end<$nbPages){
				$out .= $this->Html->tag($options['tag'], $this->Html->link($options['ellipsis'], array_merge($link, array('params'=>$this->_getLink(($page+$options['modulus'])+1, 'page', $name)))));
				$out .= $this->Html->tag($options['tag'], $this->Html->link($options['last'], array_merge($link, array('params'=>$this->_getLink($nbPages, 'page', $name)))));
			}
		}else{
			$out .= $options['separator'];
			for($i=$start; $i<=$end; $i++){
				if($i==$page){
					$out .= $this->Html->tag($options['tag'], '<b>' . $i . '</b>') . $options['separator'];
				}else{
					$out .= $this->Html->tag($options['tag'], $this->Html->link($i, array_merge($link, array('params'=>$this->_getLink($i, 'page', $name))))) . $options['separator'];
				}
			}
		}
				
		$out .= str_replace(array('{:page}','{:pages}'), array($page, $nbPages), $options['counter']);
		$out .= $options['after'];
		
		return $out;
		
		
	}
	
	public function counter() {
		;
	}

	public function filter($name = null, $fields = array(), $status = false, $action = null) {
		if($name == null){
			$name = $this->view->controller->model;
		}
		$params = array();
		if($action){
			$params['action'] = $action;
		}
		$o = '';
		$o .= $this->Form->create($params);
		$o .= '<table class="table little">';
		$o .= '<tr><th>Filtre</th><th>Valeur</th><th></th><th></th></tr>';
		$o .= $this->Form->input('data[otherData][Paginator.'.$name.'.Field]', array('type'=>'select', 'format'=>false, 'before'=>'<tr><td>', 'after'=>'</td>', 'options'=>$fields));
		$o .= $this->Form->input('data[otherData][Paginator.'.$name.'.Value]', array('type'=>'text', 'format'=>false, 'before'=>'<td>', 'after'=>'</td>'));
		//$o .= $this->Form->input('data[otherData][Paginator.'.$name.'.Strict]', array('type'=>'checkbox', 'value'=>'1', 'format'=>false, 'before'=>'<td>', 'after'=>'</td>'));
		//$o .= $this->Form->input('data[otherData][Paginator.'.$name.'.Operator]', array('type'=>'select', 'value'=>'AND', 'options'=>array('AND'=>'ET', 'OR'=>'OU'), 'format'=>false, 'before'=>'<td>', 'after'=>'</td>'));
		$o .= $this->Form->input('data[otherData][Paginator.'.$name.'.Go]', array('type'=>'submit', 'format'=>false, 'value'=>'Valider', 'before'=>'<td>', 'after'=>'</td>'));
		$o .= $this->Form->input('data[otherData][Paginator.'.$name.'.Reinit]', array('type'=>'submit', 'format'=>false, 'value'=>'Réinitialiser', 'before'=>'<td>', 'after'=>'</td></tr>'));
		$o .= '</table>';
		$o .= $this->Form->end();
		if($status){
			/*$data = $this->Session->read('Paginator.' . $name);
			$phrase  = implode(' ET ', $data['conditions']['AND']);
			$phrase  = implode(' OU ', $data['conditions']['OR']);
			$needles = array("LIKE", "%",);
			$reps   = array("CONTENANT", "",);
			
			$o .= str_replace($needles, $reps, $phrase);*/		
		}
		return $o;
		
	}

	public function url() {
		;
	}
	
	public function tableHeaders($name, $data) {
		$headers = array();
		$getData = $this->view->controller->getGetParam('paginate' . $name);
		$getData = explode('-', (string)$getData);
		$urlData = $this->view->controller->requestHandler->getParams();
		foreach ($data as $title=>$field){
			if($field == false){
				$headers[] = $title;
			}else{
				$params = array();				
				if(in_array('sort ' . $field, $getData)){
					if(in_array('direction DESC', $getData)){
						$params = $this->_getLink('ASC', 'direction', $name);
						$title = $title . ' &#9660;';
					}else{
						$params = $this->_getLink('DESC', 'direction', $name);
						$title = $title . ' &#9650;';						
					}
				}else{
					$params = $this->_getLink('ASC', 'direction', $name);					
					$title = $title;
					$params = $this->_getLink($field, 'sort', $name);
				}
				$link = $this->Html->link($title, array('controller'=>$urlData['model'], 'view'=>$urlData['view'], 'params'=>$params), array('escape'=>false));
				$headers[] = $link;
			}
		}
		return $this->Html->tableHeaders($headers);
	}
	
	protected function _getLink($value, $paramName = 'page', $name) {	
		$result = array();
		$getData = $this->view->controller->requestHandler->getParams();
		$getData = $getData['p'];
		$rr = array();
		if(!empty($getData)){
			foreach ($getData as $param){
				$parts = explode(':', $param);
				if(count($parts)==2){
					if($parts[0]=='paginate' . $name){
						$params = explode('-', urldecode($parts[1]));
						foreach ($params as $p){
							$pts = explode(' ', $p);
							if($pts[0]==$paramName){
								continue;
							}else{
								$rr[] = urlencode($p);
							}
						}
					}else{
						$result[] = $param;
					}
				}
			}
		}
		$rr[] = sprintf($paramName . '+%s', $value);
		$result []= 'paginate' . $name . ':' . implode('-', (array)$rr);		
		return $result;
	}
}
?>