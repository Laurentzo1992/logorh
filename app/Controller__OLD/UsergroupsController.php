<?php 
/**
 * 
 * 
 * 
 */

class UsergroupsController extends AppController{
public $accessCheck = array(
	    /*                               1       */
	   'Activation du compte'=>array('Activer'),
	   'Users'=>array('Voir', 'Ajouter', 'Modifier', 'Supprimer',  'Etat',  'Stat'),
        /***************38******/
		'Logs'=>array('Voir'),
	);
		
	public function edit() {
		$this->requestAction('Users' ,'loggedIn');		
		$accessLevel = $this->requestAction('Users' ,'access', array('Users'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		$ids = explode('|', (string)$this->getGetParam('id'));
		$postData = $this->postData();
		///////200
		$defaultAccess = '00000000000000000000000000000000000000';
		                  /*

                          11111111111111111111111111111111111111111111111111
		                  */
		$nbLines = 1;
		if(isset($postData['Usergroup']['submit']) && isset($postData[0]['Usergroup']) && $postData['otherData']['accessLevel'][0]){
			$saveData = array();
			$log = ($this->getGetParam('id')?'Modification':'Creation') . ' de groupe d\'utilisateurs id:';
			$defaultAccess = array();
			foreach ($postData as $index=>$usergroup){
				if(isset($usergroup['Usergroup'])){
					if(!empty($usergroup['Usergroup']['name'])){
						$saveData[$index] = $usergroup;
						$defaultAccess[$index] = $saveData[$index]['Usergroup']['accessLevel'] = implode('', $postData['otherData']['accessLevel'][$index]);
					}
				}
			}
			if($accessLevel['view'] && $accessLevel['edit']){				
				$saveIds = $this->Usergroup->saveMany($saveData);
				if($saveIds){
					$log .= implode(',', $saveIds);
					$this->requestAction('Logs' ,'record', $log);
					$this->Session->setFlash('Enregistre avec succes');
					if($this->Session->check('return')){
						$this->redirect($this->Session->read('return'));
					}else{
						$this->redirect(array('controller'=>'Users', 'view'=>'users'));
					}
				}else {
				//Display Errors
				}
			}
		}			
		if(isset($postData['Usergroup']['addRows'])){
			$nbLines = (int)$postData['Usergroup']['n'] + (int)$postData['Usergroup']['rows'];
			if($nbLines<0) $nbLines=0;
			$this->data = $postData;
		}		
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
				$nbLines = count($ids);
			}else{
				$defaultAccess = array();
				$this->data = $this->Usergroup->find('all', array('conditions'=>array(array($this->Usergroup->primaryKey=>$ids)), 'recursive'=>-1));
				foreach ($this->data as $index=>$d){
					$defaultAccess[$index] = $d['Usergroup']['accessLevel'];
				}
				$nbLines = count($this->data);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toobar = array();
		if($this->Session->check('return')){
			$toobar['Retour'] = array(
				'url'=>$this->requestHandler->getUrlRoot(true).$this->Session->read('return'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Users', 'view'=>'users', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')?'CR&Eacute;ATION GROUPE UTILISATEUR':'MODIFICATION GROUPE UTILISATEUR') . SEP . APP_DEFAULT_NAME);
		$this->set('defaultAccess', $defaultAccess);
		$this->set('accessCheck', $this->accessCheck);
		$this->set('n', $nbLines);
		$this->set('toolbar', $toobar);
	}
	
	public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Users'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		$toobar = array();
		if($this->Session->check('return')){
			$toobar['Retour'] = array(
				'url'=>$this->requestHandler->getUrlRoot(true).$this->Session->read('return'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Users', 'view'=>'users', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', ('Rechercher un groupe d\'utilisateurs') . SEP . APP_DEFAULT_NAME);
		$this->set('toolbar', $toobar);
	}
	
	public function del (){
		$this->requestAction('Users' ,'loggedIn');		
		$accessLevel = $this->requestAction('Users' ,'access', array('Users'));
		$ids = explode('|', (string)$this->getGetParam('id'));
		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Usergroup->find('all', array('conditions'=>array(array($this->Usergroup->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression de groupe d\'utilisateurs';
			$dataList = array();
			foreach ($data as $d){
				$dataList[] = ' "' . $d['Usergroup']['name'] . '" id:' . $d['Usergroup']['id'];
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Usergroup->delete($ids);
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect($this->Session->read('return'));
		}else{
			$this->redirect(array('controller'=>'Users', 'view'=>'users'));
		}
	}
}
?>