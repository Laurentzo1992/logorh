<?php
class PaginatorComponent extends Component{

	function __construct($options = array()) {
		parent::__construct($options);
	}
	
	public function checkData($postData, $getData, $name, $paginate) {
		$getParamsArray = array_merge(array('page'=>1,'sort'=>'id', 'direction'=>'DESC', 'limit'=>30), $paginate);
		if($getData){
			$getData = explode('-', urldecode($getData));
			foreach ($getData as $param){
				$parts = explode(' ', $param);
				if(count($parts)==2){
					$getParamsArray[$parts[0]] = $parts[1];
				}
			}
		}
		
		$getParams = array(
			'page'=>$getParamsArray['page'],
			'order'=>$getParamsArray['sort'] . ' ' .$getParamsArray['direction'],
			'limit'=>$getParamsArray['limit'],
		);
		
		if(isset($postData['otherData']))$postData = $postData['otherData'];
		$postParams = array();
		
		if(isset($postData["Paginator.{$name}.Reinit"])){
			return array('Post'=>null, 'Get'=>$getParams);;
		}
		if(isset($postData["Paginator.{$name}.Field"]) && isset($postData["Paginator.{$name}.Value"])){
			// convert to date
			$operator = 'AND';
			if(isset($postData["Paginator.{$name}.Operator"])){
				$operator = $postData["Paginator.{$name}.Operator"];
			}
			if(isset($postData["Paginator.{$name}.Strict"]) && $postData["Paginator.{$name}.Strict"]>0){
				$postParams['conditions'][$operator][$postData["Paginator.{$name}.Field"]] = $postData["Paginator.{$name}.Value"];
			}else{
				$postParams['conditions'][$operator][$postData["Paginator.{$name}.Field"].' LIKE'] = "%" . $postData["Paginator.{$name}.Value"] . "%";
			}
		}
		return array('Post'=>$postParams, 'Get'=>$getParams);
	}
}
// Paginator.Post.Field = Post.title
// Paginator.Post.Value = %hello%
//if(preg_match("/^Paginator\.{$name}\.*/i", $key)){
?>