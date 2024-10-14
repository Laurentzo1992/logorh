<?php
class SQLManager {
	public $alias = 'AS ';
	public $affected = null;
	public $numRows = null;
	public $took = null;
	protected $_result = null;
	protected $_connection = null;
	public $startQuote = null;
	public $endQuote = null;
	public $_baseConfig = array();
	protected $_sqlOps = array('like', 'ilike', 'or', 'not', 'in', 'between', 'regexp', 'similar to');
	protected $_queryDefaults = array(
		'conditions' => array(),
		'fields' => null,
		'table' => null,
		'alias' => null,
		'order' => null,
		'limit' => null,
		'joins' => array(),
		'group' => null,
		'offset' => null
	);	
	public function __construct($config = null, $autoConnect = true) {
		$this->setConfig($config);
		if (!isset($config['prefix'])) {
			$config['prefix'] = '';
		}
		if ($autoConnect) {
			$this->connect();
		}
	}
	
	public function setConfig($config = array()) {
		$this->config = array_merge($this->_baseConfig, $this->config, $config);
	}
	
	public function getConnection() {
		return $this->_connection;
	}
	
	public function getVersion() {
		return $this->_connection->getAttribute(PDO::ATTR_SERVER_VERSION);
	}
	
	public function value($data, $column = null) {
		
		switch ($column) {
			case 'binary':
				return $this->_connection->quote($data, PDO::PARAM_LOB);
			case 'boolean':
				return $this->_connection->quote($this->boolean($data, true), PDO::PARAM_BOOL);
			case 'string':
			case 'text':
				return $this->_connection->quote($data, PDO::PARAM_STR);
			default:
				if ($data === '') {
					return 'NULL';
				}
				if (is_float($data)) {
					return str_replace(',', '.', strval($data));
				}
				if ((is_int($data) || $data === '0') || (
					is_numeric($data) && strpos($data, ',') === false &&
					$data[0] != '0' && strpos($data, 'e') === false)
				) {
					return $data;
				}
				return $this->_connection->quote($data);
		}
	}
	
	public function execute($sql, $options = array(), $params = array()) {
		//echo $sql;
		$this->_result = $this->_execute($sql, $params);
		return $this->_result;
	}
	
	protected function _execute($sql, $params = array(), $prepareOptions = array()) {
		//echo $sql . "<br/>\n";
		if (preg_match('/^(?:CREATE|ALTER|DROP)\s+(?:TABLE|INDEX)/i', $sql)) {
			$statements = array_filter(explode(';', $sql));
			if (count($statements) > 1) {
				$result = array_map(array($this, '_execute'), $statements);
				return array_search(false, $result) === false;
			}
		}

		try {
			$query = $this->_connection->prepare($sql, $prepareOptions);
			$query->setFetchMode(PDO::FETCH_LAZY);
			if (!$query->execute($params)) {
				$this->_results = $query;
				$query->closeCursor();
				return false;
			}
			//print_r($query->errorInfo());
			if (!$query->columnCount()) {
				$query->closeCursor();
				if (!$query->rowCount()) {
					return true;
				}
			}
			return $query;
		} catch (PDOException $e) {
			if (isset($query->queryString)) {
				$e->queryString = $query->queryString;
			} else {
				$e->queryString = $sql;
			}
			//throw $e;
			global $DATABASE_ERROR;
			$DATABASE_ERROR = $query->errorInfo();
		}
	}
	
		public function isConnected() {
		return $this->connected;
	}
	
	public function read(Model $model, $queryData = array(), $recursive = null) {
		if(empty($queryData)){
			return false;
		}
		if ($recursive === null && isset($queryData['recursive'])) {
			$recursive = $queryData['recursive'];
		}else {
			$recursive = $model->recursive;
		}
		if (!empty($queryData['fields'])) {
			$queryData['fields'] = $this->fields($model, null, $queryData['fields']);
		} else {
			$queryData['fields'] = $this->fields($model);
		}
		if (empty($queryData['table'])) {
			$queryData['table'] = $this->fullTableName($model);
		}
		/**
		 * ????????????????????
		 */
		$queryData['alias'] = $model->alias;
		$sql = (!empty($queryData['finderSql']))? $queryData['finderSql'] : $this->buildStatement($queryData, $model);
		//echo $sql . '<br/>';
		$data = $this->fetchAll($sql);
		if($recursive>-1){
			$excludedAssoc = $model->alias;
			foreach ($model->associations as $type){
				foreach ($model->{$type} as $assoc =>$assocData){
					$cond = !(isset($model->excludeModel) && $assoc == $model->excludeModel);
					if($cond){
						$model->loadModel($assocData['className'], $assocData, true);
						$linkModel = $model->{$assocData['className']};
						$linkModel->recursive = $recursive-1;
						$linkModel->excludeModel = $excludedAssoc;
						switch ($type) {
							case 'belongsTo':
								foreach ((array)$data as $pos=>$d){
									if(!empty($d[$model->alias][$assocData['foreignKey']])){
										$conditions = array_merge_recursive(
											$assocData, array('conditions'=>array('AND'=>array($linkModel->primaryKey => $d[$model->alias][$assocData['foreignKey']])))
										);
										$linkModelData = $linkModel->find('first', $conditions);
										if(!empty($linkModelData[$linkModel->alias]))$data[$pos][$model->alias][$assoc] = $linkModelData[$linkModel->alias];
									}
								}
							break;
							case 'hasOne':
								foreach ((array)$data as $pos=>$d){
									if(!empty($d[$model->alias][$model->primaryKey])){
										$conditions = array_merge_recursive(
											$assocData, array('conditions'=>array('AND'=>array($assocData['foreignKey'] => $d[$model->alias][$model->primaryKey])))
										);
										$linkModelData = $linkModel->find('first', $conditions);
										if(!empty($linkModelData))$data[$pos][$model->alias][$assoc] = $linkModelData[$linkModel->alias];
									}
								}
							break;
							case 'hasMany':
								$ids = null;
								foreach ((array)$data as $pos=>$d){
									if(!empty($d[$model->alias][$model->primaryKey]))
									$ids[$pos] = $d[$model->alias][$model->primaryKey];
								}
								if(!empty($d[$model->alias]) && $ids!==null){
									$conditions = array_merge_recursive(
										$assocData, array('conditions'=>array('AND'=>array($assocData['foreignKey'] => $ids)))
									);
									$linkModelData = $linkModel->find('all', $conditions);
									foreach ($linkModelData as $lmd){
										if(!empty($lmd[$linkModel->alias])){
											$pos = array_search($lmd[$linkModel->alias][$assocData['foreignKey']], $ids);
											$data[$pos][$model->alias][$assoc][] = $lmd[$linkModel->alias];
										}
									}
								}
								
							break;
						}
					}
				}				
			}			
		}
		return $data;
	}
	
	public function fetchAll($sql, $params = array()) {
		$result = $this->execute($sql, array(), $params);
		$out = array();
		if ($result) {
			$first = $this->fetchRow($result);
			if(!empty($first)){
				$out[] = $first;
				while ($item = $this->fetchResult($result)) {
					$out[] = $item;
				}
			}
			return $out;
		}
		return false;
	}
	
	public function fetchRow($sql = null) {
		if (is_string($sql) && strlen($sql) > 5 && !$this->execute($sql)) {
			return null;
		}	
		$this->mapResult($this->_result);
		$resultRow = $this->fetchResult($this->_result);
		return $resultRow;
	}

	public function fetchResult($result) {
		if ($row = $result->fetch(PDO::FETCH_NUM)) {
			$resultRow = array();
			foreach ($this->map as $col => $meta) {
				list($table, $column, $type) = $meta;
				$resultRow[$table][$column] = $row[$col];
				if ($type === 'boolean' && $row[$col] !== null) {
					$resultRow[$table][$column] = $this->boolean($resultRow[$table][$column]);
				}
			}
			return $resultRow;
		}
		$this->_result->closeCursor();
		return false;
	}
	
	public function mapResult($results) {
		$this->map = array();
		$numFields = $results->columnCount();
		$index = 0;

		while ($numFields-- > 0) {
			$column = $results->getColumnMeta($index);
			if ($column['len'] === 1 && (empty($column['native_type']) || $column['native_type'] === 'TINY')) {
				$type = 'boolean';
			} else {
				$type = empty($column['native_type']) ? 'string' : $column['native_type'];
			}
			if (!empty($column['table'])) {
				$this->map[$index++] = array($column['table'], $column['name'], $type);
			} else {
				$this->map[$index++] = array('VirtualFields', $column['name'], $type);
			}
			//$this->map[$index++] = array($column['table'], $column['name'], $type);
		}
	}	

/**
 * Builds and generates a JOIN statement from an array. Handles final clean-up before conversion.
 *
 * @param array $join An array defining a JOIN statement in a query
 * @return string An SQL JOIN statement to be used in a query
 * @see DboSource::renderJoinStatement()
 * @see DboSource::buildStatement()
 */
	public function buildJoinStatement($join) {
		$data = array_merge(array(
			'type' => null,
			'alias' => null,
			'table' => 'join_table',
			'conditions' => array()
		), $join);

		if (!empty($data['alias'])) {
			$data['alias'] = $this->alias . $data['alias'];
		}
		if (!empty($data['conditions'])) {
			$data['conditions'] = trim($this->conditions($data['conditions'], true, false));
		}
		if (!empty($data['table']) && (!is_string($data['table']) || strpos($data['table'], '(') !== 0)) {
			$data['table'] = $this->fullTableName($data['table']);
		}
		return $this->renderJoinStatement($data);
	}

/**
 * Builds and generates an SQL statement from an array. Handles final clean-up before conversion.
 *
 * @param array $query An array defining an SQL query
 * @param Model $model The model object which initiated the query
 * @return string An executable SQL statement
 * @see DboSource::renderStatement()
 */
	public function buildStatement($query, $model) {
		$query = array_merge($this->_queryDefaults, $query);
		if (!empty($query['joins'])) {
			$count = count($query['joins']);
			for ($i = 0; $i < $count; $i++) {
				if (is_array($query['joins'][$i])) {
					$query['joins'][$i] = $this->buildJoinStatement($query['joins'][$i]);
				}
			}
		}
		return $this->renderStatement('select', array(
			'conditions' => $this->conditions($query['conditions'], true, true, $model),
			'fields' => implode(', ', $query['fields']),
			'table' => $query['table'],
			'alias' => $this->alias . $query['alias'],
			'order' => $this->order($query['order'], 'ASC', $model),
			'limit' => $this->limit($query['limit'], $query['offset']),
			'joins' => implode(' ', $query['joins']),
			'group' => $this->group($query['group'], $model)
		));
	}

/**
 * Renders a final SQL JOIN statement
 *
 * @param array $data
 * @return string
 */
	public function renderJoinStatement($data) {
		if (strtoupper($data['type']) === 'CROSS') {
			return "{$data['type']} JOIN {$data['table']} {$data['alias']}";
		}
		return trim("{$data['type']} JOIN {$data['table']} {$data['alias']} ON ({$data['conditions']})");
	}

/**
 * Renders a final SQL statement by putting together the component parts in the correct order
 *
 * @param string $type type of query being run. e.g select, create, update, delete, schema, alter.
 * @param array $data Array of data to insert into the query.
 * @return string Rendered SQL expression to be run.
 */
	public function renderStatement($type, $data) {
		extract($data);
		$aliases = null;

		switch (strtolower($type)) {
			case 'select':
				return trim("SELECT {$fields} FROM {$table} {$alias} {$joins} {$conditions} {$group} {$order} {$limit}");
			case 'create':
				return "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
			case 'update':
				if (!empty($alias)) {
					$aliases = "{$this->alias}{$alias} {$joins} ";
				}
				return trim("UPDATE {$table} {$aliases}SET {$fields} {$conditions}");
			case 'delete':
				if (!empty($alias)) {
					$aliases = "{$this->alias}{$alias} {$joins} ";
				}
				return trim("DELETE {$alias} FROM {$table} {$aliases}{$conditions}");
			case 'schema':
				foreach (array('columns', 'indexes', 'tableParameters') as $var) {
					if (is_array(${$var})) {
						${$var} = "\t" . implode(",\n\t", array_filter(${$var}));
					} else {
						${$var} = '';
					}
				}
				if (trim($indexes) !== '') {
					$columns .= ',';
				}
				return "CREATE TABLE {$table} (\n{$columns}{$indexes}) {$tableParameters};";
			case 'alter':
				return;
		}
	}

	public function createStatement(Model $model, $fields = null, $values = null) {
		$id = null;
		
		if (!$fields) {
			unset($fields, $values);
			$fields = array_keys($model->data[$model->alias]);
			$values = array_values($model->data[$model->alias]);
		}
		$count = count($fields);
		for ($i = 0; $i < $count; $i++) {
			$valueInsert[] = $this->value($values[$i], $model->getColumnType($fields[$i]));
			$fieldInsert[] = $this->name($fields[$i]);
			if ($fields[$i] == $model->primaryKey) {
				$id = $values[$i];
			}
		}
		$query = array(
			'table' => $this->fullTableName($model),
			'fields' => implode(', ', $fieldInsert),
			'values' => implode(', ', $valueInsert)
		);
		return $this->renderStatement('create', $query);
	}
	
	public function updateStatement(Model $model, $fields = array(), $values = null, $conditions = null) {
		if (!$fields) {
			unset($fields, $values);
			$fields = array_keys($model->data[$model->alias]);
			$values = array_values($model->data[$model->alias]);
		}
		if (!$values) {
			$combined = $fields;
		} else {
			$combined = array_combine($fields, $values);
		}

		$fields = implode(', ', $this->_prepareUpdateFields($model, $combined, empty($conditions)));
		
		$alias = $joins = null;
		$table = $this->fullTableName($model);		
		$alias = ($model->alias)?$model->alias:$this->fullTableName($model, true, false);
		$conditions = ($conditions)?$this->conditions($conditions, true, true, $alias):$this->conditions(array("{$model->primaryKey}" => $model->data[$model->alias][$model->primaryKey]), true, true, $alias);
		
		if ($conditions === false) {
			return false;
		}
		$query = compact('table', 'alias', 'joins', 'fields', 'conditions');
		return $this->renderStatement('update', $query);
	}
	
	protected function _prepareUpdateFields(Model $model, $fields, $quoteValues = true, $alias = false) {
		$quotedAlias = $this->startQuote . $model->alias . $this->endQuote;

		$updates = array();
		foreach ($fields as $field => $value) {
			if ($alias && strpos($field, '.') === false) {
				$quoted = $model->escapeField($field);
			} elseif (!$alias && strpos($field, '.') !== false) {
				$quoted = $this->name(str_replace($quotedAlias . '.', '', str_replace(
					$model->alias . '.', '', $field
				)));
			} else {
				$quoted = $this->name($field);
			}

			if ($value === null) {
				$updates[] = $quoted . ' = NULL';
				continue;
			}
			$update = $quoted . ' = ';

			if ($quoteValues) {
				$update .= $this->value($value, $model->getColumnType($field));
			} elseif ($model->getColumnType($field) === 'boolean' && (is_int($value) || is_bool($value))) {
				$update .= $this->boolean($value, true);
			} elseif (!$alias) {
				$update .= str_replace($quotedAlias . '.', '', str_replace(
					$model->alias . '.', '', $value
				));
			} else {
				$update .= $value;
			}
			$updates[] = $update;
		}
		return $updates;
	}
	
	public function delete(Model $model, $conditions = null, $sql = null) {
		$alias = $joins = null;
		$table = $this->fullTableName($model);
		$model->alias = $model->table;
		$conditions = $this->conditions($conditions, true, true, $model);
		if (!$conditions && !$sql) {
			return false;
		}
		$sql = (!empty($sql))? $sql : $this->renderStatement('delete', compact('alias', 'table', 'joins', 'conditions'));
		if ($this->execute($sql) === false) {
			return false;
		}
		return true;
	}
/**
 * Deletes all the records in a table and resets the count of the auto-incrementing
 * primary key, where applicable.
 *
 * @param Model|string $table A string or model class representing the table to be truncated
 * @return boolean SQL TRUNCATE TABLE statement, false if not applicable.
 */
	public function truncate($table) {
		return $this->execute('TRUNCATE TABLE ' . $this->fullTableName($table));
	}


/**
 * Generates the fields list of an SQL query.
 *
 * @param Model $model
 * @param string $alias Alias table name
 * @param mixed $fields
 * @param boolean $quote If false, returns fields array unquoted
 * @return array
 */
	public function fields(Model $model, $alias = null, $fields = array(), $quote = true) {
		if (empty($alias)) {
			$alias = $model->alias;
		}
		if (empty($fields)) {
			$fields = array_keys((array)$model->schema);
		}
		$fields = (array)$fields;
		$allFields = in_array('*', $fields) || in_array($model->alias . '.*', $fields);
		$count = count($fields);
		if ($count >= 1 && !in_array($fields[0], array('*', 'COUNT(*)'))) {
			for ($i = 0; $i < $count; $i++) {
				$check = (
					strpos($fields[$i], ' ') !== false ||
					strpos($fields[$i], '(') !== false
				);
				if(!$check){
					$dot = strpos($fields[$i], '.');
					if($dot){
						$build = explode('.', $fields[$i]);
						$fields[$i] = implode('.', array_map(array(&$this, 'name'), $build));
					}else{
						$fields[$i] = $this->name($alias). '.' . $this->name($fields[$i]);
					}
				}
			}
		}
		return (array)$fields;
	}

/**
 * Creates a WHERE clause by parsing given conditions data. If an array or string
 * conditions are provided those conditions will be parsed and quoted. If a boolean
 * is given it will be integer cast as condition. Null will return 1 = 1.
 *
 * Results of this method are stored in a memory cache. This improves performance, but
 * because the method uses a hashing algorithm it can have collisions.
 * Setting DboSource::$cacheMethods to false will disable the memory cache.
 *
 * @param mixed $conditions Array or string of conditions, or any value.
 * @param boolean $quoteValues If true, values should be quoted
 * @param boolean $where If true, "WHERE " will be prepended to the return value
 * @param Model $model A reference to the Model instance making the query
 * @return string SQL fragment
 */
	function conditions($conditions, $quoteValues = true, $where = true, $model = null) {
		$clause = '';
		$out = array();
		$keys = array('and', 'or', 'not', 'and not', 'or not', 'xor', '||', '&&');
		$glue = $key = 'AND';
		if ($where) {
			$clause = 'WHERE ';
		}
		
		if(empty($conditions)){
			$out[] = ' 1 = 1';
		}elseif(is_string($conditions)){
			$out = $this->conditionToString($conditions, $model, $quoteValues);
		}elseif(is_array($conditions)){
			$conditionSet = array();
			foreach ($conditions as $key=>$condition){
				if(is_numeric($key)){
					$key = $glue;
				}elseif(is_string($key) && !is_array($condition) && !in_array(strtolower($key), $keys)){
					$condition = array($key=>$condition);
					$key = $glue;
				}elseif(!in_array(strtolower($key), $keys)){
					$key = ' ' . strtoupper($key) . ' ';
				}
				$result = array($key=>$this->conditionToString($condition, $model, $quoteValues));
				$conditionSet = array_merge_recursive($conditionSet, $result);
			}
			foreach($conditionSet as $key=>$condition){
				$result = implode(" {$key} ", $condition);
				if(count($condition)>1){
					$result = '(' . $result . ')';
				}
				$out[] = $result;
			}
		}elseif (is_bool($conditions)) {
			return $clause . (int)$conditions . ' = 1';
		}
		$clauses = '/^WHERE\\x20|^GROUP\\x20BY\\x20|^HAVING\\x20|^ORDER\\x20BY\\x20/i';
		$conditions = implode(" {$glue} ", $out);
		
		if (preg_match($clauses, $conditions)) {
			$clause = '';
		}
		
		$out = $clause . $conditions;
		return $out;
	}
	
	function conditionToString($conditions, $model = null, $quoteValues = true) {
		$ops = array_merge(array('<', '>', '>=', '<=', '=', '!=', '<>', 'like', 'not like'), $this->_sqlOps);
		$operators = '/' . implode('|', $ops) . '/';
		if(is_array($conditions)){
			foreach ($conditions as $fieldName=>$val){
				$val = array_map(array(&$this, 'value'), (array)$val);
				$combine = true;
				if(count($val)==1) $val = $val[0];
				if(is_numeric($fieldName)){
					$value = $val;
					if (is_array($value)) {
						$value = implode(', ', $value);
					}
					$result = $this->conditionToString($value);
					$out[] = $result[0];
				}
				if (strpos($fieldName, ' ') === false) {
					$operator = '=';
				}else{
					list($fieldName, $operator) = explode(' ', trim($fieldName), 2);
				}
				if (is_array($val)) {	
					switch ($operator) {
						case '=':
							$operator = 'IN';
							break;
						case '!=':
						case '<>':
							$operator = 'NOT IN';
							break;
						case 'like':
						case 'notlike':
						case 'LIKE':
						case 'NOTLIKE':
							$combine = false;
							break;
					}
					$value = implode(', ', $val);
					$value = "({$value})";
				}elseif ($val === 'NULL') {
					$value = $val;
					switch ($operator) {
						case '=':
							$operator = 'IS';
							break;
						case '!=':
						case '<>':
							$operator = 'IS NOT';
							break;
					}
				}else{
					$value = $val;
				}
				$dot = strpos($fieldName, '.');
				if($dot){
					$build = explode('.', $fieldName);
				}else{
					$build = (isset($model->alias))?array($model->alias, $fieldName):array($fieldName);
				}
				if($quoteValues){
					$fieldName = implode('.', array_map(array(&$this, 'name'), $build));
				}else{
					$fieldName = implode('.', $build);
				}
				
				if($combine){
					$out[] = "{$fieldName} {$operator} {$value}";
				}else{
					foreach ($val as $v){
						$out[] = "{$fieldName} {$operator} {$v}";
					}
				}
			}	
		}elseif(is_string($conditions)){
			$out[] = $conditions;
		}
		return $out;
	}

	protected function _quoteFields($conditions) {
		$start = $end = null;
		$original = $conditions;

		if (!empty($this->startQuote)) {
			$start = preg_quote($this->startQuote);
		}
		if (!empty($this->endQuote)) {
			$end = preg_quote($this->endQuote);
		}
		$conditions = str_replace(array($start, $end), '', $conditions);
		$conditions = preg_replace_callback(
			'/(?:[\'\"][^\'\"\\\]*(?:\\\.[^\'\"\\\]*)*[\'\"])|([a-z0-9_][a-z0-9\\-_]*\\.[a-z0-9_][a-z0-9_\\-]*)/i',
			array(&$this, '_quoteMatchedField'),
			$conditions
		);
		if ($conditions !== null) {
			return $conditions;
		}
		return $original;
	}
	
	protected function _quoteMatchedField($match) {
		if (is_numeric($match[0])) {
			return $match[0];
		}
		return $this->name($match[0]);
	}
/**
 * Returns a quoted name of $data for use in an SQL statement.
 * Strips fields out of SQL functions before quoting.
 *
 * Results of this method are stored in a memory cache. This improves performance, but
 * because the method uses a hashing algorithm it can have collisions.
 * Setting DboSource::$cacheMethods to false will disable the memory cache.
 *
 * @param mixed $data Either a string with a column to quote. An array of columns to quote or an
 *   object from DboSource::expression() or DboSource::identifier()
 * @return string SQL field
 */
	public function name($data) {
		if ($data === '*') {
			return '*';
		}
		if (is_array($data)) {
			foreach ($data as $i => $dataItem) {
				$data[$i] = $this->name($dataItem);
			}
			return $data;
		}
		return $this->startQuote . $data . $this->endQuote;
	}

/**
 * Returns a limit statement in the correct format for the particular database.
 *
 * @param integer $limit Limit of results returned
 * @param integer $offset Offset from which to start results
 * @return string SQL limit/offset statement
 */
	public function limit($limit, $offset = null) {
		if ($limit) {
			$rt = ' LIMIT';

			if ($offset) {
				$rt .= sprintf(' %u,', $offset);
			}

			$rt .= sprintf(' %u', $limit);
			return $rt;
		}
		return null;
	}

/**
 * Returns an ORDER BY clause as a string.
 *
 * @param array|string $keys Field reference, as a key (i.e. Post.title)
 * @param string $direction Direction (ASC or DESC)
 * @param Model $model model reference (used to look for virtual field)
 * @return string ORDER BY clause
 */
	public function order($keys, $direction = 'ASC', $model = null) {
		if (!is_array($keys)) {
			$keys = array($keys);
		}
		$keys = array_filter($keys);
		$result = array();
		while (!empty($keys)) {
			list($key, $dir) = each($keys);
			array_shift($keys);

			if (is_numeric($key)) {
				$key = $dir;
				$dir = $direction;
			}

			if (is_string($key) && strpos($key, ',') !== false && !preg_match('/\(.+\,.+\)/', $key)) {
				$key = array_map('trim', explode(',', $key));
			}
			if (is_array($key)) {
				//Flatten the array
				$key = array_reverse($key, true);
				foreach ($key as $k => $v) {
					if (is_numeric($k)) {
						array_unshift($keys, $v);
					} else {
						$keys = array($k => $v) + $keys;
					}
				}
				continue;
			} elseif (is_object($key) && isset($key->type) && $key->type === 'expression') {
				$result[] = $key->value;
				continue;
			}

			if (preg_match('/\\x20(ASC|DESC).*/i', $key, $_dir)) {
				$dir = $_dir[0];
				$key = preg_replace('/\\x20(ASC|DESC).*/i', '', $key);
			}

			$key = trim($key);
			if (!preg_match('/\s/', $key) && strpos($key, '.') === false) {
				if(is_object($model)){
					$key = "{$this->name($model->alias)}.{$this->name($key)}";
				}else{
					$key = $this->name($key);	
				}
			}elseif (strpos($key, '.')) {
				$parts = explode('.', $key,2);
				$key = "{$this->name($parts[0])}.{$this->name($parts[1])}";
			}
			$key .= ' ' . trim($dir);
			$result[] = $key;
		}
		if (!empty($result)) {
			return ' ORDER BY ' . implode(', ', $result);
		}
		return '';
	}

/**
 * Create a GROUP BY SQL clause
 *
 * @param string $group Group By Condition
 * @param Model $model
 * @return string string condition or null
 */
	public function group($group, $model = null) {
		if ($group) {
			if (!is_array($group)) {
			
				$group = array($group);
			}
			foreach ($group as $index => $key) {
				if(strpos($key, '.')){
					$parts = explode('.', $key,2);
					$key = "{$this->name($parts[0])}.{$this->name($parts[1])}";
				}
				if (is_object($model) && $model->isVirtualField($key)) {
					$group[$index] = '(' . $key . ')';
				}
			}
			$group = implode(', ', $group);
			return ' GROUP BY ' . $group;//$this->_quoteFields($group);
		}
		return null;
	}

/**
 * Disconnects database, kills the connection and says the connection is closed.
 *
 * @return void
 */
	public function close() {
		$this->disconnect();
	}

/**
 * Checks if the specified table contains any record matching specified SQL
 *
 * @param Model $Model Model to search
 * @param string $sql SQL WHERE clause (condition only, not the "WHERE" part)
 * @return boolean True if the table has a matching record, else false
 */
	public function hasAny(Model $Model, $sql) {
		$sql = $this->conditions($sql);
		$table = $this->fullTableName($Model);
		$alias = $this->alias . $this->name($Model->alias);
		$where = $sql ? "{$sql}" : ' WHERE 1 = 1';
		$id = $Model->escapeField();

		$out = $this->fetchRow("SELECT COUNT({$id}) {$this->alias}count FROM {$table} {$alias}{$where}");

		if (is_array($out)) {
			return $out[0]['count'];
		}
		return false;
	}

/**
 * Gets the length of a database-native column description, or null if no length
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return mixed An integer or string representing the length of the column, or null for unknown length.
 */
	public function length($real) {
		if (!preg_match_all('/([\w\s]+)(?:\((\d+)(?:,(\d+))?\))?(\sunsigned)?(\szerofill)?/', $real, $result)) {
			$col = str_replace(array(')', 'unsigned'), '', $real);
			$limit = null;

			if (strpos($col, '(') !== false) {
				list($col, $limit) = explode('(', $col);
			}
			if ($limit !== null) {
				return intval($limit);
			}
			return null;
		}

		$types = array(
			'int' => 1, 'tinyint' => 1, 'smallint' => 1, 'mediumint' => 1, 'integer' => 1, 'bigint' => 1
		);

		list($real, $type, $length, $offset, $sign, $zerofill) = $result;
		$typeArr = $type;
		$type = $type[0];
		$length = $length[0];
		$offset = $offset[0];

		$isFloat = in_array($type, array('dec', 'decimal', 'float', 'numeric', 'double'));
		if ($isFloat && $offset) {
			return $length . ',' . $offset;
		}

		if (($real[0] == $type) && (count($real) === 1)) {
			return null;
		}

		if (isset($types[$type])) {
			$length += $types[$type];
			if (!empty($sign)) {
				$length--;
			}
		} elseif (in_array($type, array('enum', 'set'))) {
			$length = 0;
			foreach ($typeArr as $key => $enumValue) {
				if ($key === 0) {
					continue;
				}
				$tmpLength = strlen($enumValue);
				if ($tmpLength > $length) {
					$length = $tmpLength;
				}
			}
		}
		return intval($length);
	}

/**
 * Translates between PHP boolean values and Database (faked) boolean values
 *
 * @param mixed $data Value to be translated
 * @param boolean $quote
 * @return string|boolean Converted boolean value
 */
	public function boolean($data, $quote = false) {
		if ($quote) {
			return !empty($data) ? '1' : '0';
		}
		return !empty($data);
	}

/**
 * Inserts multiple values into a table
 *
 * @param string $table The table being inserted into.
 * @param array $fields The array of field/column names being inserted.
 * @param array $values The array of values to insert. The values should
 *   be an array of rows. Each row should have values keyed by the column name.
 *   Each row must have the values in the same order as $fields.
 * @return boolean
 */
	public function insertMulti($table, $fields, $values) {
		$table = $this->fullTableName($table);
		$holder = implode(',', array_fill(0, count($fields), '?'));
		$fields = implode(', ', array_map(array(&$this, 'name'), $fields));

		$pdoMap = array(
			'integer' => PDO::PARAM_INT,
			'float' => PDO::PARAM_STR,
			'boolean' => PDO::PARAM_BOOL,
			'string' => PDO::PARAM_STR,
			'text' => PDO::PARAM_STR
		);
		$columnMap = array();

		$sql = "INSERT INTO {$table} ({$fields}) VALUES ({$holder})";
		$statement = $this->_connection->prepare($sql);
		$this->begin();

		foreach ($values[key($values)] as $key => $val) {
			$type = $this->introspectType($val);
			$columnMap[$key] = $pdoMap[$type];
		}

		foreach ($values as $value) {
			$i = 1;
			foreach ($value as $col => $val) {
				$statement->bindValue($i, $val, $columnMap[$col]);
				$i += 1;
			}
			$statement->execute();
			$statement->closeCursor();

		}
		return $this->commit();
	}
	
	public function getSchemaName() {
		return $this->config['database'];
	}
	
	public function fullTableName($model, $quote = true, $schema = true) {
		if (is_object($model)) {
			$schemaName = $model->schemaName;
			$table = $model->tablePrefix . $model->table;
		} elseif (!empty($this->config['prefix']) && strpos($model, $this->config['prefix']) !== 0) {
			$table = $this->config['prefix'] . strval($model);
		} else {
			$table = strval($model);
		}
		if ($schema && !isset($schemaName)) {
			$schemaName = $this->getSchemaName();
		}

		if ($quote) {
			if ($schema && !empty($schemaName)) {
				if (strstr($table, '.') === false) {
					return $this->name($schemaName) . '.' . $this->name($table);
				}
			}
			return $this->name($table);
		}
		if ($schema && !empty($schemaName)) {
			if (strstr($table, '.') === false) {
				return $schemaName . '.' . $table;
			}
		}
		return $table;
	}
	/**
 * Converts database-layer column types to basic types
 *
 * @param mixed $real Either the string value of the fields type.
 *    or the Result object from Sqlserver::describe()
 * @return string Abstract column type (i.e. "string")
 */
	public function column($real) {
		$limit = null;
		$col = $real;
		if (is_object($real) && isset($real->Field)) {
			$limit = $real->Length;
			$col = $real->Type;
		}

		if ($col === 'datetime2') {
			return 'datetime';
		}
		if (in_array($col, array('date', 'time', 'datetime', 'timestamp'))) {
			return $col;
		}
		if ($col === 'bit') {
			return 'boolean';
		}
		if (strpos($col, 'bigint') !== false) {
			return 'biginteger';
		}
		if (strpos($col, 'int') !== false) {
			return 'integer';
		}
		if (strpos($col, 'char') !== false && $limit == -1) {
			return 'text';
		}
		if (strpos($col, 'char') !== false) {
			return 'string';
		}
		if (strpos($col, 'text') !== false) {
			return 'text';
		}
		if (strpos($col, 'binary') !== false || $col === 'image') {
			return 'binary';
		}
		if (in_array($col, array('float', 'real', 'decimal', 'numeric'))) {
			return 'float';
		}
		return 'text';
	}
	
	public function lastInsertId($source = null) {
		return $this->_connection->lastInsertId();
	}
	
	
}
