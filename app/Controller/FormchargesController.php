<?php 

class FormchargesController extends AppController{	
	var $paginate = array(
		'Formcharge'=>array(
			'model'=>'Formcharge','sort'=>'id', 'direction'=>'ASC',
			'page'=>1, 'recursive'=>0, 'limit'=>1000
		),
		
	);
	/**/
    var $uses = array('User','Formcharge','Agdossier','Formation','Formparticipant');

	public function index() {
		$this->requestAction('Users' ,'loggedIn');		
		$accessLevel = $this->requestAction('Users' ,'access', array('Formcharges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		$formationid = $this->getGetParam('formationid');

		/********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/

	    $toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Formations', 'view'=>'index2', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Formations', 'view'=>'index2', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'COUT DE REALISATION <span class="pageTitle">'.$name . SEP . $username.'</span>');

        $this->set('toolbar', $toolbar);

		$this->paginate['Formcharge']['conditions'][] = array('Formcharge.formation_id'=>$formationid);
		$this->set('fromcharges', $this->paginate('Formcharge'));
		
		$count = count($this->paginate('Formcharge'));
        $this->set('count',$count);

		$tmp = $this->Formation->find('all', array('conditions'=>array('Formation.id='.$formationid), 'recursive'=>0));
		$theme = $tmp[0]['Formation']['theme'];
        $this->set('theme', $theme);
        $this->set('formationid', $formationid);

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name'), 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name')));
		
	}

    public function index2() {
		$this->requestAction('Users' ,'loggedIn');		
		$accessLevel = $this->requestAction('Users' ,'access', array('Formcharges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		$formationid = $this->getGetParam('formationid');

		/********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/

	    $toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Formations', 'view'=>'index2', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Formations', 'view'=>'index2', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'LES CHARGES DE FORMATION <span class="pageTitle">'.$name . SEP . $username.'</span>');
		
        $this->set('toolbar', $toolbar);

	$this->paginate['Formcharge']['conditions'][] = array('Formcharge.formation_id'=>$formationid);
		$this->set('formcharges', $this->paginate('Formcharge'));
		
		$count = count($this->paginate('Formcharge'));
        $this->set('count',$count);

		$tmp = $this->Formation->find('all', array('conditions'=>array('Formation.id='.$formationid), 'recursive'=>0));
		$theme = $tmp[0]['Formation']['theme'];
		$cout = $tmp[0]['Formation']['cout'];
		/*$location = $tmp[0]['Formation']['location'];
		$fraisgeneraux = $tmp[0]['Formation']['frais_generaux'];
		$honoraireformateur = $tmp[0]['Formation']['honoraire_formateur'];*/

		
		$this->set('theme', $theme);
		$this->set('cout', $cout);
		/*$this->set('location', $location);
		$this->set('fraisgeneraux', $fraisgeneraux);
		$this->set('honoraireformateur', $honoraireformateur);*/
        $this->set('theme', $theme);
        $this->set('formationid', $formationid);

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name'), 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name')));

	  	
	}


    /************************************************************************/

    /************************************************************************/
	public function edit() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Formcharges'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}

		$postData = $this->postData();
        $nbLines = 1;
		$formationid = $this->getGetParam('formationid');
		
		$ids = explode('|', (string)$this->getGetParam('id'));
		
		$nbLines = 1;
		if(isset($postData['Formcharge']['submit'])){
			$saveData = array();
			$log = ($this->getGetParam('id')?'Modification':'Creation') . ' comptabilité id:';
			foreach ($postData as $index=>$formcharge){
                
				
				if(isset($formcharge['Formcharge'])){
					if((!empty($formcharge['Formcharge']['formpartid'])) &&
					   (!empty($formcharge['Formcharge']['formation_id'])) &&
				       (!empty($formcharge['Formcharge']['agdossier_id'])) && 
				       (!empty($formcharge['Formcharge']['frais_restauration'])) &&
				       (!empty($formcharge['Formcharge']['frais_inscription'])))
					{
						$saveData[$index] = $formcharge;
						
						$formpart = array('Formparticipant'=>array(
						       'id' =>$formcharge['Formcharge']['formpartid'],
						       'compta' => 1
								    )
							    );
						$this->Formparticipant->save($formpart);
					}
						

				}
				
			}
			

				if($accessLevel['view'] && $accessLevel['edit']){				
					$saveIds = $this->Formcharge->saveMany($saveData);
					$saveIds = true;
					if($saveIds){
						$log .= implode(',', $saveIds);
						$this->requestAction('Logs' ,'record', $log);
						$this->Session->setFlash('Enregistre avec succes');
						if($this->Session->check('return')){
							$this->redirect(array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)));
						}else{
							$this->redirect(array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)));
						}
					}else {
					    //Display Errors
					    $this->data = $postData;
						$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
					}
				}
			
		}
		/*----------------------------------------------*/
		$Formcharges = $this->Formcharge->find('all', array('conditions'=>array('Formcharge.formation_id='.$formationid), 'recursive'=>0));
        $nbLines = count($Formcharges);
        /*-------*/
		$tmp = $this->Formation->find('all', array('conditions'=>array('Formation.id='.$formationid), 'recursive'=>0));
		$theme = $tmp[0]['Formation']['theme'];
		/*----------------------------------------------*/	

		$formparticipants = $this->Formparticipant->find('all', array('conditions'=>array("Formparticipant.formation_id='{$formationid}'","Formparticipant.compta='0'"), 'recursive'=>0));		
		/*if(isset($postData['Formcharge']['addRows'])){
			$nbLines = (int)$postData['Formcharge']['n'] + (int)$postData['Formcharge']['rows'];
			if($nbLines<0) $nbLines=0;
			$this->data = $postData;
		}*/		
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
				$nbLines = count($ids);
			}else{
				$this->data = $this->Formcharge->find('all', array('conditions'=>array(array($this->Formcharge->primaryKey=>$ids)), 'recursive'=>0));
				$nbLines = count($this->data);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toobar = array();
		if($this->Session->check('return')){
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}

		

		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CREATION -   COUT DE REALISATION':'MODIFICATION - COUT DE REALISATION'));
		$this->set('n', $nbLines);

		//$this->set('js', array('tinymce/tinymce.min'));
		$this->set('toolbar', $toobar);
		$this->set('theme', $theme);
		$this->set('formationid', $formationid);
        $this->set('Formcharges',$Formcharges);
        $this->set('formparticipants',$formparticipants);
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name'), 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name')));

	}
	/*----------------------------------------------------------------*/
	public function edit2() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Formcharges'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

		
		$id = $this->getGetParam('id');
		

		$charge = $this->Formcharge->find('all', array('conditions'=>array('Formcharge.id ='.$id), 'recursive'=>0));
		
		$formationid = $charge[0]['Formcharge']['formation_id'];
		$partid = $charge[0]['Formcharge']['agdossier_id'];
       
		
		if(isset($postData['Formcharge']['submit']) && isset($postData['Formcharge'])){
			$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Formcharge ' . 'id: ';
			if($postData['Formcharge']['id']<>'' && 
				$postData['Formcharge']['agdossier_id']<>'' && 
				$postData['Formcharge']['formation_id']<>'' )
            {
				
				/*********************************************************/
				if($accessLevel['view'] && $accessLevel['edit']){				
					$saveId = $this->Formcharge->save($postData);
					if($saveId){
						$log .= $saveId;
						$this->requestAction('Logs' ,'record', $log);
						$this->Session->setFlash('Enregistre avec succes');
						if($this->Session->check('return')){
							$this->redirect(array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)));
						}else{
							$this->redirect(array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)));
						}
					}else {
					//Display Errors
					}
				}
				/****************************************************************/
			
		    }
		    else
		    {
			$this->data = $postData;
			$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
	        }
		}
		/*-------------------*/
		$ses = $this->Formation->find('all', array('conditions'=>array('Formation.id='.$formationid), 'recursive'=>0));
		$theme = $ses[0]['Formation']['theme'];

		
		$pa = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$partid), 'recursive'=>0));
		$matricule = $pa[0]['Agdossier']['ag_matricule'];
		$nom =       $pa[0]['Agdossier']['ag_nom'];
		$prenom =    $pa[0]['Agdossier']['ag_prenom'];
		
		/*-------------------*/
			
		if($id){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->charge = $this->Formcharge->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION Formcharge':'MODIFICATION Formcharge'));
		$this->set('toolbar', $toolbar);
        $this->set('formationid', $formationid);
        $this->set('charge',$charge);
         $this->set('theme', $theme);
         $this->set('agdossier', $matricule.' '.$nom.' '.$prenom);
		
		$this->set('js', 'tinymce/tinymce.min');
		
	}
	/*----------------------------------------------------------------*/
    
	/*public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Formcharges'));
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
				'url'=>array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHERCHE Formcharge'));
		$this->set('toolbar', $toobar);

		$this->set('formations',$this->Formation->find('list',array('list'=>array('id','intitule'))));
		
	}*/
	
	public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Formcharges'));
		$ids = explode('|', (string)$this->getGetParam('id'));
		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Formcharge->find('all', array('conditions'=>array(array($this->Formcharge->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression de formcharge';

            $formationid = $data[0]['Formcharge']['formation_id'];

			$dataList = array();
			foreach ($data as $d){
				$dataList[] = ' "' . $d['Formcharge']['formation_id'] . '" id:' . $d['Formcharge']['id'];
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Formcharge->delete($ids);
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect(array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)));
		}else{
			$this->redirect(array('controller'=>'Formcharges', 'view'=>'index', 'params'=>array('formationid:'.$formationid)));
		}
	}


}
?>