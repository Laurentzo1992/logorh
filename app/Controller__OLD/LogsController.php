<?php 
/**
 * 
 * 
 * 
 */

class LogsController extends AppController{
	var $paginate = array(
		'Log'=>array(
			'model'=>'Log','sort'=>'id', 'direction'=>'DESC', 'order'=>'id DESC',
			'page'=>1, 'limit'=>100,
			'recursive'=>1
		)
	);
	
	var $uses = array('User');
	
	public function index() {
		$this->requestAction('Users' ,'loggedIn');		
		$accessLevel = $this->requestAction('Users' ,'access', array('Logs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}		
		$this->set('pageTitle', APP_DEFAULT_NAME. SEP . 'HISTORIQUE DES CONNEXIONS');
		$this->set('subMenu', array());
		$this->set('logs', $this->paginate('Log'));

		$this->set('users',$this->User->find('list',array('list'=>array('id','username'))));
		$this->set('names',$this->User->find('list',array('list'=>array('id','name'))));
			
	}
	
	public function record($statement){
		$log = array(
			'Log'=>array(
				'user_id'=>$this->Session->read('id'),
				'statement'=>$statement,
				'created'=>date('Y-m-d H:i:s', strtotime('now'))
			)			
		);
		$this->Log->save($log);
	}
}
?>