<?php
/*
 * 
 * This is the core config file where is stored all connection information
 * 
 */
class DATABASE_CONFIG {

	public $default = array(
	      'datasource' => 'Database/Mysql',
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'logorhbd',
            'prefix' => 'rh_',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
	);
	
	public $test = array(
	      'datasource' => 'Database/Mysql',
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'logorhbd',
            'prefix' => 'rh_',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
	);
}
