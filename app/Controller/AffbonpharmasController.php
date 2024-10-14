<?php 
/**
 * 
 * 
 * 
 */

class AffbonpharmasController extends AppController{	
	var $paginate = array(
		'Affbonpharma'=>array(
			'model'=>'Affbonpharma','sort'=>'id', 'affbonpharma'=>'DESC',
			'page'=>1, 'recursive'=>0
		),
		
	
	);	
	
	var $uses = array('User','Agdossier','Parampharmacie');
	
	
	/*********Demande********************************************/
	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        
        /********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'BONS  PRODUITS  PHARMACEUTIQUES<span class="pageTitle">'.$name . SEP . $username.'</span>');
		
		$this->set('affbonpharmas', $this->paginate('Affbonpharma'));
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('parampharmacies', $this->Parampharmacie->find('list', array('list'=>array('id','nom_pharma'), 'order'=>'id ASC')));

	}
	/*----------------------------------------------------------*/
	public function add() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
		$postData = $this->postData();
		if(isset($postData['Affbonpharma']['submit']) && isset($postData['Affbonpharma'])){
            if($postData['Affbonpharma']['agdossier_id']<>'' &&
               $postData['Affbonpharma']['numero_bon']<>'' &&
               $postData['Affbonpharma']['date_bon']<>'')
            {
				/*********************************************************************/
				$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Affbonpharma ' . 'id: ';
				if($accessLevel['view'] && $accessLevel['edit'])
				{	
					/*-----------------------------------*/
	                $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Affbonpharma']['agdossier_id']), 'recursive'=>0));
		            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
	               
		            $postData['Affbonpharma']['matricule']  = $matricule;	
	                			
				    $saveId = $this->Affbonpharma->save($postData);
					
					if($saveId){
						$log .= $saveId;
						$this->requestAction('Logs' ,'record', $log);
						$this->Session->setFlash('Enregistré avec succès');
						if($this->Session->check('return')){
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}else{
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}
					}else {
					//Display Errors
					}
			    }
					/*********************************************************************/
            }
		    else
		    {
				$this->data = $postData;
				$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
	        }
		}
				
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Affbonpharma->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION BON  PRODUITS  PHARMACEUTIQUES':'MODIFICATION BON  PRODUITS  PHARMACEUTIQUES'));
		$this->set('toolbar', $toolbar);
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
		
	}

	public function modif() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
		$postData = $this->postData();
		if(isset($postData['Affbonpharma']['submit']) && isset($postData['Affbonpharma'])){
            if($postData['Affbonpharma']['agdossier_id']<>'' &&
               $postData['Affbonpharma']['numero_bon']<>'' &&
               $postData['Affbonpharma']['date_bon']<>'')
            {
				/*********************************************************************/
				$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Affbonpharma ' . 'id: ';
				if($accessLevel['view'] && $accessLevel['edit'])
				{	
					/*-----------------------------------*/
	                $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Affbonpharma']['agdossier_id']), 'recursive'=>0));
		            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
	               
		            $postData['Affbonpharma']['matricule']  = $matricule;	
	                			
				    $saveId = $this->Affbonpharma->save($postData);
					
					if($saveId){
						$log .= $saveId;
						$this->requestAction('Logs' ,'record', $log);
						$this->Session->setFlash('Enregistré avec succès');
						if($this->Session->check('return')){
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}else{
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}
					}else {
					//Display Errors
					}
			    }
					/*********************************************************************/
            }
		    else
		    {
				$this->data = $postData;
				$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
	        }
		}
				
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Affbonpharma->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION BON  PRODUITS  PHARMACEUTIQUES':'MODIFICATION BON  PRODUITS  PHARMACEUTIQUES'));
		$this->set('toolbar', $toolbar);
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
		
	}

	public function montant() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
		$postData = $this->postData();
		if(isset($postData['Affbonpharma']['submit']) && isset($postData['Affbonpharma'])){
            if($postData['Affbonpharma']['agdossier_id']<>'' &&
               $postData['Affbonpharma']['numero_bon']<>'' &&
               $postData['Affbonpharma']['date_bon']<>'' &&
               $postData['Affbonpharma']['parampharmacie_id']<>'' &&
               $postData['Affbonpharma']['montant_bon']<>'' )
            {
				/*********************************************************************/
				$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Affbonpharma ' . 'id: ';
				if($accessLevel['view'] && $accessLevel['edit'])
				{	
					/*-----------------------------------*/
	                $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Affbonpharma']['agdossier_id']), 'recursive'=>0));
		            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
	               
		            $postData['Affbonpharma']['matricule']  = $matricule;	
	                			
				    $saveId = $this->Affbonpharma->save($postData);
					
					if($saveId){
						$log .= $saveId;
						$this->requestAction('Logs' ,'record', $log);
						$this->Session->setFlash('Enregistré avec succès');
						if($this->Session->check('return')){
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}else{
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}
					}else {
					//Display Errors
					}
			    }
					/*********************************************************************/
            }
		    else
		    {
				$this->data = $postData;
				$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
	        }
		}
				
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Affbonpharma->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION BON  PRODUITS  PHARMACEUTIQUES':'MODIFICATION BON  PRODUITS  PHARMACEUTIQUES'));
		$this->set('toolbar', $toolbar);
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		
		$this->set('parampharmacies', $this->Parampharmacie->find('list', array('list'=>array('id','nom_pharma'), 'order'=>'id ASC')));

		
	}


	public function traitement() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
		$postData = $this->postData();


		$datedebut = '2020-06-01';
		$datefin = '2020-06-30';
		$agdossier = 2;
		 //montant_pret
        /******************************************************/
		if(isset($postData['Affbonpharma']['valider'])){
			$datedebut = $postData['Affbonpharma']['date_debut'];
			$datefin = $postData['Affbonpharma']['date_fin'];
			$agdossier = $postData['Affbonpharma']['agdossier_id'];
		    $this->data = $postData;
		}
		/******************************************************/
	   $affbonpharmas = $this->Affbonpharma->find('all', array('conditions'=>array("Affbonpharma.agdossier_id='{$agdossier}'","Affbonpharma.date_bon between '{$datedebut}' and '{$datefin}'"), 'recursive'=>0));
         /*$affbonpharmas = $this->Affbonpharma->find('all', array(
								'list'=>array('Affbonpharma.id',
									          'Affbonpharma.numero_bon',
									          'Affbonpharma.date_bon',
									          'Affbonpharma.montant_bon',
									          'Affbonpharma.parampharmacie_id',
									          'Affbonpharma.statut'
								               ),
								'fields'=>array('Affbonpharma.id',
									          'Affbonpharma.numero_bon',
									          'Affbonpharma.date_bon',
									          'Affbonpharma.montant_bon',
									          'Affbonpharma.parampharmacie_id',
									          'Affbonpharma.statut'
								               ),
								'conditions'=>array(
									"Affbonpharma.agdossier_id = '".$agdossier."'",
									"Affbonpharma.date_bon = '".$datedebut."' BETWEEN '".$datefin."'"
							    ),
							    
							));*/
       // print_r($data);
		/*if(isset($postData['Affpret']['submit']) && isset($postData['Affpret'])){
            if($postData['Affpret']['agdossier_id']<>'' &&
               $postData['Affpret']['date_pret']<>'' &&
               $postData['Affpret']['montant_pret']<>'')
            {
					/*********************************************************************
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' affpret ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){				
						/*-----------------------------------*
	                    $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Affpret']['agdossier_id']), 'recursive'=>0));
			            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
	                    /*-----------------------------------*	
	                  
	                   $postData['Affpret']['matricule']  = $matricule;

						$saveId = $this->Affpret->save($postData);
                        $affpretid = $this->Affpret->id;

						
						/*-------------------------------------------------*
                            foreach ($postData['Affpret'] as $index => $page)
			                {
							    if(isset($page['montant']) &&   $page['montant']<>'')
								{
                                    
                                    $pg = array('Afftraite'=>array(
								        'affpret_id' => $affpretid,
							            'agdossier_id' => $postData['Affpret']['agdossier_id'],
							            'date_traite' => $page['rembousementDate'],
							            'montant_traite' => $page['montant'],
							            'statut' => 'Encours'
										   )
							            );
							        $this->Afftraite->save($pg);	
								
								}
							}
                        /*-------------------------------------------------*
						
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
								$this->redirect(array('controller'=>'Affprets', 'view'=>'index'));
							}else{
								$this->redirect(array('controller'=>'Affprets', 'view'=>'index'));
							}
						}else {
						//Display Errors
						}
					}
					/*********************************************************************
            }
		    else
		    {
				$this->data = $postData;
				$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
	        }
		}*/
				
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Affpret->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affprets', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affprets', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION D\'UN PRET':'MODIFICATION D\'UN PRET'));
          //jquery.ui.datepicker.monthyearpicker
		$this->set('js', array('jquery.ui.datepicker.monthyearpicker'));
		$this->set('css', array('jquery.ui.datepicker.monthyearpicker'));
		$this->set('toolbar', $toolbar);
		
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_matricule," ",ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('affbonpharmas', $affbonpharmas);

		$this->set('parampharmacies', $this->Parampharmacie->find('list', array('list'=>array('id','nom_pharma'), 'order'=>'id ASC')));
       
	}
	/*----------------------------------------------------------*/
	public function edit() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
		$postData = $this->postData();
		if(isset($postData['Affbonpharma']['submit']) && isset($postData['Affbonpharma'])){
            if($postData['Affbonpharma']['agdossier_id']<>'' &&
               $postData['Affbonpharma']['numero_bon']<>'' &&
               $postData['Affbonpharma']['date_bon']<>'')
            {
				/*********************************************************************/
				$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Affbonpharma ' . 'id: ';
				if($accessLevel['view'] && $accessLevel['edit'])
				{	
					/*-----------------------------------*/
	                $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Affbonpharma']['agdossier_id']), 'recursive'=>0));
		            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
	               
		            $postData['Affbonpharma']['matricule']  = $matricule;	
	                			
				    $saveId = $this->Affbonpharma->save($postData);
					
					if($saveId){
						$log .= $saveId;
						$this->requestAction('Logs' ,'record', $log);
						$this->Session->setFlash('Enregistré avec succès');
						if($this->Session->check('return')){
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}else{
							$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
						}
					}else {
					//Display Errors
					}
			    }
					/*********************************************************************/
            }
		    else
		    {
				$this->data = $postData;
				$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
	        }
		}
				
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Affbonpharma->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION BON  PRODUITS  PHARMACEUTIQUES':'MODIFICATION BON  PRODUITS  PHARMACEUTIQUES'));
		$this->set('toolbar', $toolbar);
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('parampharmacies', $this->Parampharmacie->find('list', array('list'=>array('nom_pharma','nom_pharma'), 'order'=>'id ASC')));
		
	}



	public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE BONS  PRODUITS  PHARMACEUTIQUES'));
		$this->set('toolbar', $toolbar);
	}
	
	
	public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));

		$ids = explode('|', (string)$this->getGetParam('id'));
      

		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Affbonpharma->find('all', array('conditions'=>array(array($this->Affbonpharma->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Affbonpharmas';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Affbonpharma']['id'];
				
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Affbonpharma->delete($ids);
			
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect($this->Session->read('return'));
		}else{
			$this->redirect(array('controller'=>'Affbonpharmas', 'view'=>'index'));
		}
	}
    
	public function etat() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affbonpharmas'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
		
		
		$this->set('pageTitle', ('LISTE DES BONS PRODUITS PHARMACEUTIQUES'));
		//$this->set('toolbar', $toolbar);
		$this->set('Affbonpharmas', $this->paginate('Affbonpharma'));
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		$this->set('style', '
			.tab th
			{
				background-color:#8ECBB9;
			}
			');	
	}
	
	public function countries(){
		return $this->countries;
	}
	
	var $countries = array(
		'Afghanistan'=> 'Afghanistan', 
		'Afrique du Sud'=> 'Afrique du Sud', 
		'Aland'=> 'Aland', 
		'Albanie'=> 'Albanie', 
		'Alg&eacuterie'=> 'Alg&eacuterie', 
		'Allemagne'=> 'Allemagne', 
		'Andorre'=> 'Andorre', 
		'Angola'=> 'Angola', 
		'Anguilla'=> 'Anguilla', 
		'Antarctique'=> 'Antarctique', 
		'Antigua-et-Barbuda'=> 'Antigua-et-Barbuda', 
		'Arabie saoudite'=> 'Arabie saoudite', 
		'Argentine'=> 'Argentine', 
		'Arm&eacutenie'=> 'Arm&eacutenie', 
		'Aruba'=> 'Aruba', 
		'Australie'=> 'Australie', 
		'Autriche'=> 'Autriche', 
		'Azerba&iuml;djan'=> 'Azerba&iuml;djan', 
		'Bahamas'=> 'Bahamas', 
		'Bahre&iuml;n'=> 'Bahre&iuml;n', 
		'Bangladesh'=> 'Bangladesh', 
		'Barbade'=> 'Barbade', 
		'Bi&eacutelorussie'=> 'Bi&eacutelorussie', 
		'Belgique'=> 'Belgique', 
		'Belize'=> 'Belize', 
		'B&eacutenin'=> 'B&eacutenin', 
		'Bermudes'=> 'Bermudes', 
		'Bhoutan'=> 'Bhoutan', 
		'Bolivie'=> 'Bolivie', 
		'Bonaire'=>'Bonaire',
		'Saint-Eustache et Saba'=> 'Saint-Eustache et Saba', 
		'Bosnie-Herz&eacutegovine'=> 'Bosnie-Herz&eacutegovine', 
		'Botswana'=> 'Botswana', 
		'&Icirc;le Bouvet'=> '&Icirc;le Bouvet', 
		'Br&eacutesil'=> 'Br&eacutesil', 
		'Brunei'=> 'Brunei', 
		'Bulgarie'=> 'Bulgarie', 
		'Burkina Faso'=> 'Burkina Faso', 
		'Burundi'=> 'Burundi', 
		'&Icirc;les Ca&iuml;mans'=> '&Icirc;les Ca&iuml;mans', 
		'Cambodge'=> 'Cambodge', 
		'Cameroun'=> 'Cameroun', 
		'Canada'=> 'Canada', 
		'Cap-Vert'=> 'Cap-Vert', 
		'R&eacutepublique centrafricaine'=> 'R&eacutepublique centrafricaine', 
		'Chili'=> 'Chili', 
		'Chine'=> 'Chine', 
		'&Icirc;le Christmas'=> '&Icirc;le Christmas', 
		'Chypre'=> 'Chypre', 
		'&Icirc;les Cocos'=> '&Icirc;les Cocos', 
		'Colombie'=> 'Colombie', 
		'Comores'=> 'Comores', 
		'R&eacutepublique du Congo'=> 'R&eacutepublique du Congo', 
		'R&eacutepublique d&eacutemocratique du Congo'=> 'R&eacutepublique d&eacutemocratique du Congo', 
		'&Icirc;les Cook'=> '&Icirc;les Cook', 
		'Cor&eacutee du Sud'=> 'Cor&eacutee du Sud', 
		'Cor&eacutee du Nord'=> 'Cor&eacutee du Nord', 
		'Costa Rica'=> 'Costa Rica', 
		'C&ocirc;te d\'Ivoire'=> 'C&ocirc;te d\'Ivoire', 
		'Croatie'=> 'Croatie', 
		'Cuba'=> 'Cuba', 
		'Cura&ccedil;ao'=> 'Cura&ccedil;ao', 
		'Danemark'=> 'Danemark', 
		'Djibouti'=> 'Djibouti', 
		'R&eacutepublique dominicaine'=> 'R&eacutepublique dominicaine', 
		'Dominique'=> 'Dominique', 
		'&Eacute;gypte'=> '&Eacute;gypte', 
		'Salvador'=> 'Salvador', 
		'&Eacute;mirats arabes unis'=> '&Eacute;mirats arabes unis', 
		'&Eacute;quateur'=> '&Eacute;quateur', 
		'&Eacute;rythr&eacutee'=> '&Eacute;rythr&eacutee', 
		'Espagne'=> 'Espagne', 
		'Estonie'=> 'Estonie', 
		'&Eacute;tats-Unis'=> '&Eacute;tats-Unis', 
		'&Eacute;thiopie'=> '&Eacute;thiopie', 
		'&Icirc;les Malouines'=> '&Icirc;les Malouines', 
		'&Icirc;les F&eacutero&eacute'=> '&Icirc;les F&eacutero&eacute', 
		'Fidji'=> 'Fidji', 
		'Finlande'=> 'Finlande', 
		'France'=> 'France', 
		'Gabon'=> 'Gabon', 
		'Gambie'=> 'Gambie', 
		'G&eacuteorgie'=> 'G&eacuteorgie', 
		'G&eacuteorgie du Sud-et-les &Icirc;les Sandwich du Sud'=> 'G&eacuteorgie du Sud-et-les &Icirc;les Sandwich du Sud', 
		'Ghana'=> 'Ghana', 
		'Gibraltar'=> 'Gibraltar', 
		'Gr&egravece'=> 'Gr&egravece', 
		'Grenade'=> 'Grenade', 
		'Groenland'=> 'Groenland', 
		'Guadeloupe'=> 'Guadeloupe', 
		'Guam'=> 'Guam', 
		'Guatemala'=> 'Guatemala', 
		'Guernesey'=> 'Guernesey', 
		'Guin&eacutee'=> 'Guin&eacutee', 
		'Guin&eacutee-Bissau'=> 'Guin&eacutee-Bissau', 
		'Guin&eacutee &eacutequatoriale'=> 'Guin&eacutee &eacutequatoriale', 
		'Guyana'=> 'Guyana', 
		'Guyane'=> 'Guyane', 
		'Ha&iuml;ti'=> 'Ha&iuml;ti', 
		'&Icirc;les Heard-et-MacDonald'=> '&Icirc;les Heard-et-MacDonald', 
		'Honduras'=> 'Honduras', 
		'Hong Kong'=> 'Hong Kong', 
		'Hongrie'=> 'Hongrie', 
		'&Icirc;le de Man'=> '&Icirc;le de Man', 
		'&Icirc;les mineures &eacuteloign&eacutees des &Eacute;tats-Unis'=> '&Icirc;les mineures &eacuteloign&eacutees des &Eacute;tats-Unis', 
		'&Icirc;les Vierges britanniques'=> '&Icirc;les Vierges britanniques', 
		'&Icirc;les Vierges des &Eacute;tats-Unis'=> '&Icirc;les Vierges des &Eacute;tats-Unis', 
		'Inde'=> 'Inde', 
		'Indon&eacutesie'=> 'Indon&eacutesie', 
		'Iran'=> 'Iran', 
		'Irak'=> 'Irak', 
		'Irlande'=> 'Irlande', 
		'Islande'=> 'Islande', 
		'Isra&euml;l'=> 'Isra&euml;l', 
		'Italie'=> 'Italie', 
		'Jama&iuml;que'=> 'Jama&iuml;que', 
		'Japon'=> 'Japon', 
		'Jersey'=> 'Jersey', 
		'Jordanie'=> 'Jordanie', 
		'Kazakhstan'=> 'Kazakhstan', 
		'Kenya'=> 'Kenya', 
		'Kirghizistan'=> 'Kirghizistan', 
		'Kiribati'=> 'Kiribati', 
		'Kowe&iuml;t'=> 'Kowe&iuml;t', 
		'Laos'=> 'Laos', 
		'Lesotho'=> 'Lesotho', 
		'Lettonie'=> 'Lettonie', 
		'Liban'=> 'Liban', 
		'Liberia'=> 'Liberia', 
		'Libye'=> 'Libye', 
		'Liechtenstein'=> 'Liechtenstein', 
		'Lituanie'=> 'Lituanie', 
		'Luxembourg'=> 'Luxembourg', 
		'Macao'=> 'Macao', 
		'Mac&eacutedoine'=> 'Mac&eacutedoine', 
		'Madagascar'=> 'Madagascar', 
		'Malaisie'=> 'Malaisie', 
		'Malawi'=> 'Malawi', 
		'Maldives'=> 'Maldives', 
		'Mali'=> 'Mali', 
		'Malte'=> 'Malte', 
		'&Icirc;les Mariannes du Nord'=> '&Icirc;les Mariannes du Nord', 
		'Maroc'=> 'Maroc', 
		'Marshall'=> 'Marshall', 
		'Martinique'=> 'Martinique', 
		'Maurice'=> 'Maurice', 
		'Mauritanie'=> 'Mauritanie', 
		'Mayotte'=> 'Mayotte', 
		'Mexique'=> 'Mexique', 
		'Micron&eacutesie'=> 'Micron&eacutesie', 
		'Moldavie'=> 'Moldavie', 
		'Monaco'=> 'Monaco', 
		'Mongolie'=> 'Mongolie', 
		'Mont&eacuten&eacutegro'=> 'Mont&eacuten&eacutegro', 
		'Montserrat'=> 'Montserrat', 
		'Mozambique'=> 'Mozambique', 
		'Birmanie'=> 'Birmanie', 
		'Namibie'=> 'Namibie', 
		'Nauru'=> 'Nauru', 
		'N&eacutepal'=> 'N&eacutepal', 
		'Nicaragua'=> 'Nicaragua', 
		'Niger'=> 'Niger', 
		'Nigeria'=> 'Nigeria', 
		'Niue'=> 'Niue', 
		'&Icirc;le Norfolk'=> '&Icirc;le Norfolk', 
		'Norv&egravege'=> 'Norv&egravege', 
		'Nouvelle-Cal&eacutedonie'=> 'Nouvelle-Cal&eacutedonie', 
		'Nouvelle-Z&eacutelande'=> 'Nouvelle-Z&eacutelande', 
		'Territoire britannique de l\'oc&eacutean Indien'=> 'Territoire britannique de l\'oc&eacutean Indien', 
		'Oman'=> 'Oman', 
		'Ouganda'=> 'Ouganda', 
		'Ouzb&eacutekistan'=> 'Ouzb&eacutekistan', 
		'Pakistan'=> 'Pakistan', 
		'Palaos'=> 'Palaos', 
		'Autorit&eacute Palestinienne'=> 'Autorit&eacute Palestinienne', 
		'Panama'=> 'Panama', 
		'Papouasie-Nouvelle-Guin&eacutee'=> 'Papouasie-Nouvelle-Guin&eacutee', 
		'Paraguay'=> 'Paraguay', 
		'Pays-Bas'=> 'Pays-Bas', 
		'P&eacuterou'=> 'P&eacuterou', 
		'Philippines'=> 'Philippines', 
		'&Icirc;les Pitcairn'=> '&Icirc;les Pitcairn', 
		'Pologne'=> 'Pologne', 
		'Polyn&eacutesie fran&ccedil;aise'=> 'Polyn&eacutesie fran&ccedil;aise', 
		'Porto Rico'=> 'Porto Rico', 
		'Portugal'=> 'Portugal', 
		'Qatar'=> 'Qatar', 
		'La R&eacuteunion'=> 'La R&eacuteunion', 
		'Roumanie'=> 'Roumanie', 
		'Royaume-Uni'=> 'Royaume-Uni', 
		'Russie'=> 'Russie', 
		'Rwanda'=> 'Rwanda', 
		'Sahara occidental'=> 'Sahara occidental', 
		'Saint-Barth&eacutelemy'=> 'Saint-Barth&eacutelemy', 
		'Saint-Christophe-et-Ni&eacutev&egraves'=> 'Saint-Christophe-et-Ni&eacutev&egraves', 
		'Saint-Marin'=> 'Saint-Marin', 
		'Saint-Martin (Antilles fran&ccedil;aises)'=> 'Saint-Martin (Antilles fran&ccedil;aises)', 
		'Saint-Martin'=> 'Saint-Martin', 
		'Saint-Pierre-et-Miquelon'=> 'Saint-Pierre-et-Miquelon', 
		'Saint-Si&egravege (&Eacute;tat de la Cit&eacute du Vatican)'=> 'Saint-Si&egravege (&Eacute;tat de la Cit&eacute du Vatican)', 
		'Saint-Vincent-et-les-Grenadines'=> 'Saint-Vincent-et-les-Grenadines', 
		'Sainte-H&eacutel&egravene'=>'Sainte-H&eacutel&egravene',
		'Ascension et Tristan da Cunha'=> 'Ascension et Tristan da Cunha', 
		'Sainte-Lucie'=> 'Sainte-Lucie', 
		'Salomon'=> 'Salomon', 
		'Samoa'=> 'Samoa', 
		'Samoa am&eacutericaines'=> 'Samoa am&eacutericaines', 
		'Sao Tom&eacute-et-Principe'=> 'Sao Tom&eacute-et-Principe', 
		'S&eacuten&eacutegal'=> 'S&eacuten&eacutegal', 
		'Serbie'=> 'Serbie', 
		'Seychelles'=> 'Seychelles', 
		'Sierra Leone'=> 'Sierra Leone', 
		'Singapour'=> 'Singapour', 
		'Slovaquie'=> 'Slovaquie', 
		'Slov&eacutenie'=> 'Slov&eacutenie', 
		'Somalie'=> 'Somalie', 
		'Soudan'=> 'Soudan', 
		'Soudan du Sud'=> 'Soudan du Sud', 
		'Sri Lanka'=> 'Sri Lanka', 
		'Su&egravede'=> 'Su&egravede', 
		'Suisse'=> 'Suisse', 
		'Suriname'=> 'Suriname', 
		'Svalbard et &Icirc;le Jan Mayen'=> 'Svalbard et &Icirc;le Jan Mayen', 
		'Swaziland'=> 'Swaziland', 
		'Syrie'=> 'Syrie', 
		'Tadjikistan'=> 'Tadjikistan', 
		'Ta&iuml;wan / (R&eacutepublique de Chine (Ta&iuml;wan))'=> 'Ta&iuml;wan / (R&eacutepublique de Chine (Ta&iuml;wan))', 
		'Tanzanie'=> 'Tanzanie', 
		'Tchad'=> 'Tchad', 
		'R&eacutepublique tch&egraveque'=> 'R&eacutepublique tch&egraveque', 
		'Terres australes et antarctiques fran&ccedil;aises'=> 'Terres australes et antarctiques fran&ccedil;aises', 
		'Tha&iuml;lande'=> 'Tha&iuml;lande', 
		'Timor oriental'=> 'Timor oriental', 
		'Togo'=> 'Togo', 
		'Tokelau'=> 'Tokelau', 
		'Tonga'=> 'Tonga', 
		'Trinit&eacute-et-Tobago'=> 'Trinit&eacute-et-Tobago', 
		'Tunisie'=> 'Tunisie', 
		'Turkm&eacutenistan'=> 'Turkm&eacutenistan', 
		'&Icirc;les Turques-et-Ca&iuml;ques'=> '&Icirc;les Turques-et-Ca&iuml;ques', 
		'Turquie'=> 'Turquie', 
		'Tuvalu'=> 'Tuvalu', 
		'Ukraine'=> 'Ukraine', 
		'Uruguay'=> 'Uruguay', 
		'Vanuatu'=> 'Vanuatu', 
		'Venezuela'=> 'Venezuela', 
		'Vi&ecirc;t Nam'=> 'Vi&ecirc;t Nam', 
		'Wallis-et-Futuna'=> 'Wallis-et-Futuna', 
		'Y&eacutemen'=> 'Y&eacutemen', 
		'Zambie'=> 'Zambie', 
		'Zimbabwe'=> 'Zimbabwe'
);
}
?>