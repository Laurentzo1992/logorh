<?php
App::uses('ConnectionManager', 'Model');
/**
 * 
 * Model Class 
 * 
 */
class Model{
	public $name = null;
	public $alias = null;
	public $table = null;
	public $useTable = true;
	public $tablePrefix = null;
	public $dbConfig = 'default';
	public $id = null;
	public $insertID = null;
	public $data = null;
	public $schema = null;
	public $schemaName = null;
	public $labels = array();
	public $whitelist = array();
	public $recursive = 0;
	public $primaryKey = 'id';
	public $foreignKey = '_id';
	public $associations = array(
		'belongsTo',
		'hasMany',
		'hasOne',
		'hasAndBelongsToMany'
	);
	public $belongsTo = array();
	public $hasOne = array();
	public $hasMany = array();
	public $hasAndBelongsToMany = array();
	public $plugins = array(); // name=>array(OPTIONS)
	public $validator = null;
	public $validateErrors = null;
	protected $_affectedRows = null;
	protected $_create = false;
	protected $_loadedModels = array();
	protected $_associatedModels = array();
	public $findMethods = array(
		'all' => true, 'first' => true, 'count' => true,
		'neighbors' => true, 'list' => true
	);
	
	function __construct($id = false){
		$ds = null;
		$recursive = 1;
		if (is_array($id)) {
			extract(array_merge(
				array(
					'id' => $this->id, 'table' => $this->table, 'ds' => $this->dbConfig,
					'name' => $this->name, 'alias' => $this->alias, 'recursive'=>1
				),
				$id
			));
		}
		if ($ds !== null) {
			$this->dbConfig = $ds;
		}
		if ($this->name === null) {
			$this->name = (isset($name) ? $name : $this->_getName());
		}

		if ($this->alias === null) {
			$this->alias = (isset($alias) ? $alias : $this->name);
		}
		
		if ($this->useTable !== false) {
			App::uses('ConnectionManager', 'Model');
			if ($this->table === null) {
				$this->table = (isset($table) ? $table : $this->_getTable());
			}
			$this->buildAssoc();
			$db = $this->getDataSource();
			$this->schemaName = $db->getSchemaName();
			$this->_describe();
			if($recursive>0){
				$modelSet = array_merge((array)$this->belongsTo, (array)$this->hasOne, (array)$this->hasMany);
				foreach ($modelSet as $key=>$m){
					$this->_associatedModel[] = $m['className'];
					$options = array(
						'name'=>$m['className'],
						'alias'=>$key,
						'recursive'=>$recursive-1, 
						'table'=>isset($m['table'])? $m['table'] : $this->_getTable($m['className'])
					);
					$this->loadModel($m['className'], $options);
				}
			}
		}		
	
		foreach($this->plugins as $key=>$plugin){
			if(is_array($plugin)){
				$this->loadPlugin($key, $plugin);
			}else {
				$this->loadPlugin($plugin);
			}
		}
	}
	
	public function buildAssoc() {
		$hasMany = array();
		foreach ((array)$this->hasMany as $n=>$m){			
			if(is_array($m)){
				$className = (isset($m['className']))?$m['className']:$n;
				$key = (is_string($n))? $n : $className;
				$foreignKey = (isset($m['foreignKey']))?$m['foreignKey']: (strtolower($this->name) . $this->foreignKey);
				$array = $m;
			}else{
				$className = $m;
				$foreignKey = strtolower($this->name) . $this->foreignKey;
				$key = $className;
				$array = array();
			}
			$hasMany[$key] = array_merge(
				array(
					'className'=>$className,
					'foreignKey'=>$foreignKey,
					'alias'=>ucfirst($key),
				),
			$array);				
		}
		$this->hasMany = $hasMany;
		
		$hasOne = array();
		foreach ((array)$this->hasOne as $n=>$m){			
			if(is_array($m)){
				$className = (isset($m['className']))?$m['className']:$n;
				$key = (is_string($n))? $n : $className;
				$foreignKey = (isset($m['foreignKey']))?$m['foreignKey']: (strtolower($this->name) . $this->foreignKey);
				$array = $m;
			}else{
				$className = $m;
				$foreignKey = strtolower($this->name) . $this->foreignKey;
				$key = $className;
				$array = array();
			}
			$hasOne[$key] = array_merge(
				array(
					'className'=>$className,
					'foreignKey'=>$foreignKey,
					'alias'=>ucfirst($key),
				),
			$array);				
		}
		$this->hasOne = $hasOne;
		
		$belongsTo = array();
		foreach ((array)$this->belongsTo as $n=>$m){			
			if(is_array($m)){
				$className = (isset($m['className']))?$m['className']:$n;
				$key = (is_string($n))? $n : $className;
				$foreignKey = (isset($m['foreignKey']))?$m['foreignKey']: (strtolower($className) . $this->foreignKey);
				$array = $m;
			}else{
				$className = $m;
				$foreignKey = strtolower($className) . $this->foreignKey;
				$key = $className;
				$array = array();
			}
			$belongsTo[$key] = array_merge(
				array(
					'className'=>$className,
					'foreignKey'=>$foreignKey,
					'alias'=>ucfirst($key),
				),
			$array);				
		}
		$this->belongsTo = $belongsTo;
	}
	public function read($id=null){
		if(!$id){
			$id = $this->id;
		}
		$params = array(
			'conditions'=>array(
				'AND'=>array($this->primaryKey=>$id),
			),
			'recursive'=>($this->recursive)?$this->recursive:'0'
		);
		if($this->exists($params)){
			return $this->find('first', $params);
		}
		return array();
	}
	
	public function create() {
		$this->_create = true;
	}
/**
 * first, all, list, neighbors
 * 
 * Find Function
 * Types = all, first, list, neighbors, count
 * params
 * array(
 * 	conditions => array (
 * 		OR => array( condition1=1, condition2 like 123)
 * 		AND => array( condition1=1, condition2 like 123)
 * 		NOT => array( condition1=1, condition2 like 123)
 * 	),
 * 	recursive => 1,
 * 	fields => array(DISTINCT colName, colName)
 * 	order => array(colName1, colName2 DESC),
 * 	group => array (colName),
 * 	limit => n
 * 	page => n
 * 	offset => n
 * )
 */
	
	public function find($type = 'first', $query = array()) {
		$query = $this->buildQuery($type, $query);
		if ($query === null) {
			return null;
		}
		return $this->_readDataSource($type, $query);
	}

	public function query(){
		;
	}
	
	public function buildQuery($type = 'first', $query = array()) {
		$query = array_merge(
			array(
				'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
				'offset' => null, 'order' => null, 'page' => 1, 'group' => null, 'callbacks' => true,
			),
			(array)$query
		);

		if ($this->findMethods[$type] === true) {
			$query = $this->{'_find' . ucfirst($type)}('before', $query);
		}

		if (!is_numeric($query['page']) || intval($query['page']) < 1) {
			$query['page'] = 1;
		}

		if ($query['page'] > 1 && !empty($query['limit'])) {
			$query['offset'] = ($query['page'] - 1) * $query['limit'];
		}

		return $query;
	}
	
	public function getDataSource() {
		return ConnectionManager::getDataSource($this->dbConfig);
	}
	
	protected function _findAll($state, $query, $results = array()) {
		if ($state === 'before') {
			return $query;
		}

		return $results;
	}

/**
 * Handles the before/after filter logic for find('first') operations. Only called by Model::find().
 *
 * @param string $state Either "before" or "after"
 * @param array $query
 * @param array $results
 * @return array
 * @see Model::find()
 */
	protected function _findFirst($state, $query, $results = array()) {
		if ($state === 'before') {
			$query['limit'] = 1;
			return $query;
		}

		if (empty($results[0])) {
			return array();
		}

		return $results[0];
	}

/**
 * Handles the before/after filter logic for find('count') operations. Only called by Model::find().
 *
 * @param string $state Either "before" or "after"
 * @param array $query
 * @param array $results
 * @return integer The number of records found, or false
 * @see Model::find()
 */
	protected function _findCount($state, $query, $results = array()) {
		if ($state === 'before') {
			if (!empty($query['fields']) && is_array($query['fields'])) {
				if (!preg_match('/^count/i', current($query['fields']))) {
					unset($query['fields']);
				}
			}

			if (empty($query['fields'])) {
				$query['fields'] = 'COUNT(*) as count';
			}
			if (!isset($query['recursive']) || $query['recursive'] === null) {
				$query['recursive'] = -1;
			}
			return $query;
		}
		foreach (array('VirtualFields', $this->alias) as $key) {
			if (isset($results[0][$key]['count'])) {
				if ($query['group']) {
					return count($results);
				}
				return intval($results[0][$key]['count']);
			}
		}

		return false;
	}

/**
 * Handles the before/after filter logic for find('list') operations. Only called by Model::find().
 *
 * @param string $state Either "before" or "after"
 * @param array $query
 * @param array $results
 * @return array Key/value pairs of primary keys/display field values of all records found
 * @see Model::find()
 */
	protected function _findList($state, $query, $results = array()) {
		if ($state === 'before') {
			if (empty($query['fields'])) {
				$secondfield = array_keys($this->schema);
				$secondfield = $secondfield[1];
				$query['list'] = (empty($query['list']))?array($this->primaryKey, $secondfield):$query['list'];
				$query['fields'] = $query['list'];
			} else {
				if (!is_array($query['fields'])) {
					$query['fields'] = (array)$query['fields'];
				}
				if(empty($query['list'])) $query['list'] = $query['fields'];
				
			}
			foreach ($query['list'] as $index=>$item){
				if(!strpos($item, '.')){
					$query['list'][$index] = $this->alias . '.' . $item;
				}
			}

			if (!isset($query['recursive']) || $query['recursive'] === null) {
				$query['recursive'] = -1;
			}
			return $query;
		}

		if (empty($results)) {
			return array();
		}
		$resultSet = array();
		foreach($results as $data){
			if(count($query['list'])==2){
				$resultSet[$this->_getArrayData($query["list"][0], $data)] = $this->_getArrayData($query["list"][1], $data);
			}else{
				$array = array();
				foreach ($query['list'] as $listItem){
					$dataParts = explode('.', $listItem);
					$array[end($dataParts)] = $this->_getArrayData($listItem, $data);;
				}
				$resultSet[$this->_getArrayData($query["list"][0], $data)] = $array;
			}
		}
		return $resultSet;
	}
	
	protected function _getArrayData($path, $data){
		if (empty($data) || empty($path)) {
			return null;
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

/**
 * Detects the previous field's value, then uses logic to find the 'wrapping'
 * rows and return them.
 *
 * @param string $state Either "before" or "after"
 * @param array $query
 * @param array $results
 * @return array
 */
	protected function _findNeighbors($state, $query, $results = array()) {
		extract($query);

		if ($state === 'before') {
			$conditions = (array)$conditions;
			if (!isset($field) && !isset($value)) {
				$field = $this->primaryKey;
				$value = $this->id;
			}

			$query['conditions'] = array_merge($conditions, array($field . ' <' => $value));
			$query['order'] = $field . ' DESC';
			$query['limit'] = 1;
			$query['field'] = $field;
			$query['value'] = $value;
			return $query;
		}

		unset($query['conditions'][$field . ' <']);
		$return = array();
		if (isset($results[0])) {
			$prevVal = $this->_getArrayData($results[0], $field);
			if($prevVal) $query['conditions'][$field . ' >='] = $prevVal;
			$query['conditions'][$field . ' !='] = $value;
			$query['limit'] = 2;
		} else {
			$return['prev'] = null;
			$query['conditions'][$field . ' >'] = $value;
			$query['limit'] = 1;
		}

		$query['order'] = $field . ' ASC';
		$neighbors = $this->find('all', $query);		
		if (!array_key_exists('prev', $return)) {
			$return['prev'] = isset($neighbors[0]) ? $neighbors[0] : null;
		}

		if (count($neighbors) === 2) {
			$return['next'] = $neighbors[1];
		} elseif (count($neighbors) === 1 && !$return['prev']) {
			$return['next'] = $neighbors[0];
		} else {
			$return['next'] = null;
		}

		return $return;
	}

	protected function _readDataSource($type, $query) {
		$results = $this->getDataSource()->read($this, $query);
		if ($this->findMethods[$type] === true) {
			return $this->{'_find' . ucfirst($type)}('after', $query, $results);
		}
	}
	
	public function save($data = null, $params = array(), $validate = true) {
		if(isset($params['saveMany'])){
			$this->saveMany($data, $params, $validate);
		}elseif(isset($data[$this->alias])){
			$this->data = array();
			if((isset($data[$this->alias][$this->primaryKey]) && $data[$this->alias][$this->primaryKey]) && !$this->_create){
				$on = 'update';
				$dateFields = array('modified', 'updated');
			}else{
				$on = 'create';
				$dateFields = array('created' ,'modified', 'updated');
			}
			
			if($validate && isset($params['deep'])){
				if(!$this->validateDeep($data, $on, (bool)isset($params['skip'])))return false;
			}elseif($validate && !$this->validate($data, $on)){
				return false;
			}
			$now = date('Y-m-d H:i:s', strtotime('now'));
			
			foreach ($this->schema as $col=>$colInfo){
				if(isset($data[$this->alias][$col])){
					if($colInfo['type']=='integer' && ($data[$this->alias][$col]==null || $data[$this->alias][$col]=='')){
						$this->data[$this->alias][$col] = 0;
					}elseif($colInfo['type']=='datetime'){
						$this->data[$this->alias][$col] = date('Y-m-d H:i:s', strtotime($data[$this->alias][$col]));
					}elseif($colInfo['type']=='date'){
						$this->data[$this->alias][$col] = date('Y-m-d', strtotime($data[$this->alias][$col]));
					}else{
						$this->data[$this->alias][$col] = $data[$this->alias][$col];
					}
				}elseif(in_array($col, $dateFields) && (!isset($data[$this->alias][$col]) || !$data[$this->alias][$col])){
					$this->data[$this->alias][$col] = $now;
				}
			}
			$db = $this->getDataSource();
			if(isset($params['deep'])){
				$this->_saveBelongsTo($data, $params);
			}
			$this->beforeSave();
			$success = (bool)$this->{'_' . $on}();
			$this->afterSave();
			if(isset($params['deep'])){
				$this->_saveHasOne($data, $params, $this->id);
				$this->_saveHasMany($data, $params, $this->id);
			}
			return $success;
			
		}
	}
	
	protected function _create(){	
		$db = $this->getDataSource();
		if ($db->execute($db->createStatement($this))) {
			$this->id = $db->lastInsertId($db->fullTableName($this, false, false), $this->primaryKey);
			return $this->id;
		}
		return false;
	}
	
	protected function _update(){
		$db = $this->getDataSource();
		if (!$db->execute($db->updateStatement($this))) {
			return false;
		}
		$this->id = $this->data[$this->alias][$this->primaryKey];
		return true;
	}
	
	protected function _saveBelongsTo($data, $params) {
		$dataKeys = array_keys($data[$this->alias]);
		foreach ($this->belongsTo as $key=>$assocData){
			$saveCondition = in_array($key, $dataKeys);
			if($saveCondition){
				$saveData[$key] = $data[$this->alias][$key];
				$this->loadModel($assocData['className'], array_merge($assocData, array('recursive'=>-1)), true);
				$linkModel = $this->{$assocData['className']};
				$linkModel->save($saveData, $params);
				$this->data[$this->alias][$assocData['foreignKey']] = $linkModel->id;
			}
		}
	}

	protected function _saveHasMany($data, $params, $id){
		$dataKeys = array_keys($data[$this->alias]);
		foreach ($this->hasMany as $key=>$assocData){
			$saveCondition = in_array($key, $dataKeys);
			if($saveCondition){
				$saveData[$key] = $data[$this->alias][$key];
				$params['addFields'][$assocData['foreignKey']] = $id;
				$params['saveMany'] = true;
				$this->loadModel($assocData['className'], array_merge($assocData, array('recursive'=>-1)), true);
				$linkModel = $this->{$assocData['className']};
				$linkModel->save($saveData, $params);
			}
		}
	}
	protected function _saveHasOne($data, $params, $id){
		$dataKeys = array_keys($data[$this->alias]);
		foreach ($this->hasOne as $key=>$assocData){
			$saveCondition = in_array($key, $dataKeys);
			if($saveCondition){
				$saveData[$key] = $data[$this->alias][$key];
				$saveData[$key][$assocData['foreignKey']] = $id;
				$this->loadModel($assocData['className'], array_merge($assocData, array('recursive'=>-1)), true);
				$linkModel = $this->{$assocData['className']};
				$linkModel->save($saveData, $params);
			}
		}
	}
	
	public function saveMany($data = array(), $params = array(), $validate = true){
		$dataArray = array();
		foreach ($data as $als=>$d){
			if((is_string($als) && $this->alias==$als) || is_int($als)){
				foreach ($d as $alias=>$saveD){
					if((is_int($als) && $this->alias==$alias) || is_int($alias)){
						$saveData[$this->alias] = $saveD;
						if(isset($params['addFields'])){
							foreach ($params['addFields'] as $field=>$value){
								$saveData[$this->alias][$field]=$value;
							}
						}
						if((isset($saveData[$this->alias][$this->primaryKey]) && $saveData[$this->alias][$this->primaryKey]) && !$this->_create){
							$on = 'update';
						}else{
							$on = 'create';
						}
						
						$dataArray[] = $saveData;
					}
				}
			}
		}
		unset($params['saveMany']);
		unset($params['addFields']);
		$ids = array();
		foreach ($dataArray as $d){
			$this->save($d, $params, $validate);
			$ids[] = $this->id;
		}
		return $ids;
	}
	
	public function getColumnType($column) {
		if(isset($this->schema[$column])){
			return $this->schema[$column]['type'];
		}
		return null;
	}
	
	/**
	 * params, conditions, limit
	 */
	public function updateAll($data = array(), $params = array()){
		extract($params);
		$this->data = $data;
		$db = $this->getDataSource();
		if(!empty($updateSql)){
			$sql = $updateSql;
		}else{
			$sql = $db->updateStatement($this, array(), null, $conditions);
		}
		$this->beforeSave();
		if (!$db->execute($sql)) {
			return false;
		}
		$this->afterSave();
		return true;
	}
	
	public function delete($id = null){
		$db = $this->getDataSource();
		if(!$id && !$id = $this->id){
			return false;
		}
		$conditions[] = array($this->primaryKey=>$id);
		$this->beforeDelete();
		return $db->delete($this, $conditions);
		$this->afterDelete();
	}
	
	/**
	 * params array(conditions, finderSql)
	 */
	public function deleteAll($params){
		extract($params);
		$db = $this->getDataSource();
		if(!empty($deleteSql)){
			return $db->delete($this, null, $deleteSql);
		}
		$this->beforeDelete();
		return $db->delete($this, $conditions);
		$this->afterDelete();
	}
	
	/**
	 * conditions and join
	 */
	public function exists($params){
		$params['recursive'] = -1;
		$result = $this->find('first', $params);
		if(count($result)==1){
			return true;
		}
		return false;
	}
	public function getAffectedRows(){
		return $this->_affectedRows;
	}
	public function beforeSave(){
	
	}
	public function afterSave(){
	
	}
	public function beforeDelete(){
	
	}
	public function afterDelete(){
	
	}
	public function validate(&$data = array(), $on = 'update', $skip = false, $validator = null){
		App::uses('ValidationUtility', 'Utility');
		$default = array(
			'rule'=>null,
			'message'=>'An Error Occured, Data was not saved',
			'last'=>false,
			'allowEmpty'=>true,
			'require'=>false,
			'on'=>null,
			'format'=>null, // tolower toupper ucfirst date datetime monetary numeric htmlentities =>array('otherinfo')
			
		);
		if(!$validator){
			$validator = $this->validator;
		}
		$returnError = false;
		foreach ($this->schema as $colName=>$colInfo){
			$error = false;
			if(isset($validator[$colName])){
				if(is_string($validator[$colName])){
					$validator[$colName] = array(array_merge($default, array('rule'=>$validator[$colName])));
				}elseif(is_array($validator[$colName])){
					$keys1 = array_keys($validator[$colName]);
					$keys2 = array_keys($default);
					$condition = false;
					foreach ($keys1 as $k){
						$condition = $condition && in_array($k, $keys2);
					}
					if($condition){
						$validator[$colName] = array(array_merge($default, $validator[$colName]));
					}else{
						$vldt = array();
						foreach ($validator[$colName] as $v){
							$vldt[] = array_merge($default, $v);
						}
						$validator[$colName] = $vldt;
					}
				}
				/*Do the validation*/
				foreach ($validator[$colName] as $v){
					if($on==$v['on'] || $v['on']===null){
						if(isset($data[$this->alias][$colName])){
							if(is_string($v['format'])){
								$format = explode('|', $v['format']);
								foreach ($format as $f){
									$data[$this->alias][$colName] = $f($data[$this->alias][$colName]);
								}
							}elseif(is_array($v['format'])){
								if(!empty($v['format'][0]) && is_object($v['format'][0])){
									$obj = $v['format'][0];
									array_shift($v['format']);
									$func = !empty($v['format'][0])?$v['format'][0]:'index';
									array_shift($v['format']);
									array_unshift($v['format'], $data[$this->alias][$colName]);
									$data[$this->alias][$colName] = call_user_func_array(array($obj, $func), $v['format']);
								}else{
									$func = !empty($v['format'][0])?$v['format'][0]:'index';
									array_shift($v['format']);
									array_unshift($v['format'], $data[$this->alias][$colName]);
									$data[$this->alias][$colName] = call_user_func_array($func, $v['format']);								
								}
							}
							if(is_string($v['rule'])){
								//it is either a function of a regex
							}elseif(is_array($v['rule'])){
								//it is a function with argument
							}			
							if($v['allowEmpty']==false && (($colInfo['type']=='integer' && (int)$data[$this->alias][$colName]==0) || empty($data[$this->alias][$colName]))){
								$error = true;
							}
						}else{
							if($v['require']==true){
								$error = true;
							}
						}
						
						//validation
						
						if($error){
							if(!$skip) $this->validateErrors[$this->alias][$colName][] = $v['message'];
							if($v['last']==true) break;
						}
					}
				}
			}
			if($error)$returnError=true;
		}
		return !$returnError;
	}

	public function validateDeep(&$data = array(), $on = 'update', $skip = false){
		return $this->validate($data, $on);
	}
	
	public function validateMany(&$data = array(), $on = 'update', $skip = false, $deep = false){
		$error = false;
		$v = 'validate' . ($deep)?'Deep':'';
		foreach ($data as $pos=>$d){
			if(!$this->{$v}($data, $on, $skip)){
				if($skip){
					unset($data[$pos]);
				}else{
					$error = true;
				}
			}else{
				$data[$pos] = $d;
			}
		}
		return $error;
	}
	
	protected function _getName() {
		return ucfirst(get_class($this));
	}
	protected function _getTable($name = null) {
		if($name===null)$name = $this->name;
		return strtolower($name . 's');
	}
	public function loadModel($model, $options = false, $forceLoad = false){
		if($forceLoad){
			include_once(MVC_APP . 'Model' . DS . $model . '.php');
			$this->{$model} = new $model($options);
			$this->_loadedModels[] = $model;
		}elseif(!in_array($model, $this->_loadedModels) && !$forceLoad){
			include_once(MVC_APP . 'Model' . DS . $model . '.php');
			$this->{$model} = new $model($options);
			$this->_loadedModels[] = $model;
		}
	}
	public function loadPlugin($plugin, $options = array()){
		include_once(MVC_APP . 'Lib' . DS. 'Plugin' . DS . $plugin . '.php');
		$this->{$plugin} = new $plugin($options);
	}
	
	public function isVirtualField($key) {
		if(!in_array($key, array_keys($this->schema))){
			return true;
		}
		return false;
	}
	/**
	 * 
	 * DATABASE RELATED FUNCTIONS
	 * 
	 */
	protected function _describe() {		
		$db = $this->getDataSource();
		$key = $db->fullTableName($this, false);
		$table = $db->fullTableName($this);

		$fields = false;
		$cols = $db->execute('SHOW FULL COLUMNS FROM ' . $table);
		
		if (!$cols) {
			return false;
		}

		while ($column = $cols->fetch(PDO::FETCH_OBJ)) {
			$fields[$column->Field] = array(
				'type' => $db->column($column->Type),
				'null' => ($column->Null === 'YES' ? true : false),
				'default' => $column->Default,
				'length' => $db->length($column->Type),
			);
		}
		$cols->closeCursor();
		$this->schema = $fields;
	}
}
/**
 *$model = new Model();

$params = array(
	'fields'=>array('id', 'title', 'body', 'created'),
	'limit'=>'3',
	'offset'=>'3'
	'conditions'=>array(
		'AND'=>array('(condition1=1)', 'condition2 like 123'),
		'OR'=>array('condition1=1', 'condition2 like 123'),
		'NOT'=>array('condition1=1', 'condition2 like 123'),
	),
	'joins'=>array(
		array(
			'table'=>'categories',
			'type'=>'LEFT',
			'condition'=>'posts.categoryID=categories.id'
		),
		array(
			'table'=>'categories',
			'type'=>'RIGHT',
			'condition'=>'posts.categoryID=categories.id'
		),
	)
);

$model->find('all', $params);*/
?>