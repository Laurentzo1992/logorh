<?php
class Usergroup extends AppModel{
	var $hasMany = 'User';
	var $validator = array(
		'name'=>array(
			array(
			'rule'=>array('minLength', 1),
			'message'=>'Groupe invalide',
			'allowEmpty'=>false,
			'require'=>true,
			'format'=>'trim|ucwords'
			)
		),
	);
}
?>