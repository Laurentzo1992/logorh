<?php
class Mysql extends SQLManager {
	public $index = array('PRI' => 'primary', 'MUL' => 'index', 'UNI' => 'unique');
	public $alias = 'AS ';
	public $affected = null;
	public $numRows = null;
	public $took = null;
	protected $_result = null;
	public $configKeyName = null;
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
	public $connected = false;
	protected $_sources = null;
	public $config = array();
	public $_baseConfig = array(
		'persistent' => true,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => 'cake',
		'port' => '3306'
	);
	protected $_connection = null;
	public $startQuote = "`";
	public $endQuote = "`";
	protected $_useAlias = true;
	public $columns = array(
		'primary_key' => array('name' => 'NOT NULL AUTO_INCREMENT'),
		'string' => array('name' => 'varchar', 'limit' => '255'),
		'text' => array('name' => 'text'),
		'biginteger' => array('name' => 'bigint', 'limit' => '20'),
		'integer' => array('name' => 'int', 'limit' => '11', 'formatter' => 'intval'),
		'float' => array('name' => 'float', 'formatter' => 'floatval'),
		'datetime' => array('name' => 'datetime', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'timestamp' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'time' => array('name' => 'time', 'format' => 'H:i:s', 'formatter' => 'date'),
		'date' => array('name' => 'date', 'format' => 'Y-m-d', 'formatter' => 'date'),
		'binary' => array('name' => 'blob'),
		'boolean' => array('name' => 'tinyint', 'limit' => '1')
	);

	public function connect() {
		$config = $this->config;
		$this->connected = false;

		$flags = array(
			PDO::ATTR_PERSISTENT => $config['persistent'],
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		if (!empty($config['encoding'])) {
			$flags[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $config['encoding'];
		}
		if (!empty($config['ssl_key']) && !empty($config['ssl_cert'])) {
			$flags[PDO::MYSQL_ATTR_SSL_KEY] = $config['ssl_key'];
			$flags[PDO::MYSQL_ATTR_SSL_CERT] = $config['ssl_cert'];
		}
		if (!empty($config['ssl_ca'])) {
			$flags[PDO::MYSQL_ATTR_SSL_CA] = $config['ssl_ca'];
		}
		if (empty($config['unix_socket'])) {
			$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
		} else {
			$dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
		}

		try {
			$this->_connection = new PDO(
				$dsn,
				$config['login'],
				$config['password'],
				$flags
			);
			$this->connected = true;
			if (!empty($config['settings'])) {
				foreach ($config['settings'] as $key => $value) {
					$this->_execute("SET $key=$value");
				}
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		$this->_useAlias = (bool)version_compare($this->getVersion(), "4.1", ">=");

		return $this->connected;
	}
	
	
}

