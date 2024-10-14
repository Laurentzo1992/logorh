<?php 
/**
 * 
 * 
 * 
 */

class FormparticipantsController extends AppController{	
	var $paginate = array(
		'Formparticipant'=>array(
			'model'=>'Formparticipant','sort'=>'id', 'direction'=>'ASC',
			'page'=>1, 'recursive'=>0
		)
	);
	/**/
    var $uses = array('User','Agdossier','Formation');


	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Formparticipants'));
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
				'url'=>array('controller'=>'Formations', 'view'=>'index4', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Formations', 'view'=>'index4', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'LISTE DE PRESENCE DES AgdossierS<span class="pageTitle">'.$name . SEP . $username.'</span>');
		
		$this->paginate['Formparticipant']['conditions'][] = array('Formparticipant.sessionformation_id'=>$formationid);
		$this->set('Formparticipants', $this->paginate('Formparticipant'));
		
		/****************SESSION DE FORMATION******************************/
        $data = $this->Formation->find('all', array('conditions'=>array('Formation.id='.$formationid), 'recursive'=>0));
		$themeID = $data[0]['Formation']['theme_id'];
        /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule'];
        $this->set('theme', $theme);
        /******************************************************************/
        $this->set('toolbar', $toolbar);
        $this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    
	}

	
    public function getAgdossier() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Formparticipants'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		if(isset($_POST['s'])){
			$search = $_POST['s'];
		}else{
			$search = '';
		}
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_matricule," - ",ag_nom," ",ag_prenom) as name', 'conditions'=>array("Agdossier.ag_nom LIKE '%$search%'"), 'order'=>'Agdossier.ag_nom ASC')));
		$this->layout = 'ajax';	


		
	}

	

	public function edit() {
       // print_r($_POST);

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Formparticipants'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		$debut = ''; 
		$fin = ''; 
		$nb = 0; 
		$resIds = '';
		$formationid = $this->getGetParam('formationid');

		$username = $this->Session->read('username');
        $usergroups = $this->Session->read('usergroups');
        $userid = $this->Session->read('id');
		
		$ids = explode('|', (string)$this->getGetParam('id'));
		$postData = $this->postData();
		$nbLines = 5;

        /*-----------------------Session formation--------------------------------*/
       // $data = $this->Formation->find('all', array('conditions'=>array('Formation.id='.$formationid), 'recursive'=>0));
		
        $Agdossiers = array();

		if(isset($postData['Formparticipant']['submit']) && isset($postData['Formparticipant'])){
			$saveData = array();
			$log = ($this->getGetParam('id')?'Modification':'Creation') . ' session formation id:';
            if($accessLevel['view'] && $accessLevel['edit']){
                $formateuids = array();

				foreach ($postData['Formparticipant'] as $index=>$Formparticipant)
				{

					if(isset($Formparticipant['participant_id'])  &&
							 $Formparticipant['participant_id'] <> '' &&
							 $Formparticipant['participant_id'] <> 0 )
					{

						if(!in_array($Formparticipant['participant_id'], $formateuids))
						{
						    $session = array('Formparticipant'=>array(
						       'agdossier_id' => $Formparticipant['participant_id'],
						       'formation_id' => $formationid
								    )
							    );
							$this->Formparticipant->save($session);
						    $formateuids[] = $Formparticipant['participant_id'];
							
					    }
					}
				}
            
                /*================================================*/
				$log = 'Ajout des Agdossiers a une session de formation';
				$this->requestAction('Logs' ,'record', $log);
				$this->Session->setFlash('Enregistre avec succes');
                $this->redirect(array('controller'=>'Formations', 'view'=>'index'));
				/*================================================*/
			}

        }
		
					
			
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
				$nbLines = count($ids);
			}else{
				$this->data = $this->Formparticipant->find('all', array('conditions'=>array(array($this->Formparticipant->primaryKey=>$ids)), 'recursive'=>0));
				$nbLines = count($this->data);
			}
		}
      
        /*----------------------------------------------------------------------------*/
         //$Formations = $this->Formparticipant->find('all', array('conditions'=>array("Formparticipant.formation_id = '".$formationid."'"), 'recursive'=>0));
	  /* $Formations = $this->Formparticipant->find('all', array(
							'list'=>array('Formparticipant.id',
								          'Formation.id',
							              'Formation.theme',
							              'Formation.objectif',
							              'Formation.poste',
							              'Formation.nbr_participant',
							              'Formation.duree',
							              'Formation.lieu',
							              'Formation.annee',
							              'Formation.statut',
							              'Agdossier.id',
							              'Agdossier.ag_matricule',
							              'Agdossier.ag_nom',
							              'Agdossier.ag_prenom',
							            ),
							'fields'=>array('Formparticipant.id',
								          'Formation.id',
							              'Formation.theme',
							              'Formation.objectif',
							              'Formation.poste',
							              'Formation.nbr_participant',
							              'Formation.duree',
							              'Formation.lieu',
							              'Formation.annee',
							              'Formation.statut',
							              'Agdossier.id',
							              'Agdossier.ag_matricule',
							              'Agdossier.ag_nom',
							              'Agdossier.ag_prenom',
							            ),
							'joins'=>array(
								array(
								'type' => 'LEFT',
								'alias' => 'Formation',
								'table' => 'formations',
							    'conditions' => array('Formation.id = Formparticipant.formation_id'),
					             ),
								array(
								'type' => 'LEFT',
								'alias' => 'Agdossier',
								'table' => 'agdossiers',
								'conditions' => array('Agdossier.id = Formparticipant.agdossier_id'),
					
								),
							),
							'conditions'=>array(
								"Formparticipant.formation_id = '".$formationid."'"

						    ),
						    
						));*/
		
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toobar = array();
		if($this->Session->check('return')){
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Formations', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Formations', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		//$this->set('script', 'var ajaxurl='.$this->requestHandler->getUrlRoot());
		$this->set('js', array('autoCompleteAgdossier'));
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME  . SEP . 'AJOUT PARTICIPANT A UNE FORMATION':'AJOUT PARTICIPANT A UNE FORMATION'));
		$this->set('n', $nbLines);
		$this->set('toolbar', $toobar);  
		$this->set('formationid', $formationid);
		$this->set('Agdossiers', $this->Agdossier->find('list', array('list'=>array('id','ag_nom'))));

		$this->set('formation',$this->Formation->find('first',array('recursive'=>2, 'conditions'=>array("Formation.id = $formationid"))));

		//$this->set('Formations', $Formations);
		//$this->set('theme',$theme);
		//$this->set('nb',$nb);
		//$this->set('resIds',$resIds);
		
	}



    public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Formparticipants'));
		$ids = explode('|', (string)$this->getGetParam('id'));
		$formationid = $this->getGetParam('formationid');
		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Formparticipant->find('all', array('conditions'=>array(array($this->Formparticipant->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Formparticipant';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Formparticipant']['id'];
				
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Formparticipant->delete($ids);
			
			$this->Session->setFlash($log);			
		}

		$this->redirect(array('controller'=>'Formparticipants', 'view'=>'edit', 'params'=>array('formationid:'.$formationid)));

	}
  



}
?>