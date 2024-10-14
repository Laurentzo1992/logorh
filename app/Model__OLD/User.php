<?php
class User extends AppModel{
	

	var $belongsTo = array(
		'Usergroup',
	);

    var $hasMany = array(		
		
	);

	var $validator = array(
		'usergroup_id'=>array(
			array(
			'rule'=>'numeric',
			'message'=>'Groupe d\'utilisateur invalide',
			'allowEmpty'=>false,
			'require'=>true,
			)
		),
		'name'=>array(
			array(
			'rule'=>array('minLength', 1),
			'message'=>'Nom invalide',
			'allowEmpty'=>false,
			'require'=>true,
			'format'=>'trim|ucwords'
			)
		),
		'username'=>array(
			array(
			'rule'=>array('minLength', 3),
			'message'=>'Le nom d\'utilisateur doit contenir au moin 3 caracteres',
			'allowEmpty'=>false,
			'require'=>true,
			'format'=>'trim'
			)
		),
		'password'=>array(
			array(
			'rule'=>array('minLength', 3),
			'message'=>'Le mot de passe doit contenir au moin 3 caracteres',
			'allowEmpty'=>false,
			'require'=>true,
			'on'=>'create'
			)
		),
		'hPassword'=>array(
			array(
			'rule'=>array('minLength', 40),
			'message'=>'Une erreur est survenue: Hash_Password_Failed_To_Validate',
			'allowEmpty'=>false,
			'require'=>true,
			'on'=>'create'
			)
		),
		'token'=>array(
			array(
			'message'=>'Une erreur est survenue: Token_Failed_To_Validate',
			'allowEmpty'=>false,
			'require'=>true,
			'format'=>'sha1',
			'on'=>'create'
			)
		),
	);
}
?>