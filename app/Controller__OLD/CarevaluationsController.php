<?php 
/**
 * 
 * 
 * 
 */

class CarevaluationsController extends AppController{	
	var $paginate = array(
		'Carevaluation'=>array(
			'model'=>'Carevaluation','sort'=>'id', 'direction'=>'ASC',
			'page'=>1, 'recursive'=>0, 'limit'=>18
		),
		
	
	);	
	
	var $uses = array('User','Agdossier','Afftraite','Agcontrat','Agavencement','Agaffectmutation','Carcritere','Paramsociopro','Paramclassification','Carevalitem','Paramdirection','Paramtypefonction','Paramfonction','Paramechelon');
	
	
	
	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $contratid = $this->getGetParam('contratid');
		/*-----------------------CONTRAT-----------------------*/
		$tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contratid), 'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
		$matricule = $tmp[0]['Agcontrat']['matricule'];
		$numcontrat = $tmp[0]['Agcontrat']['num_contrat'];
	    //print_r($dossier);
        /*----------------------------------------------------*/
        /********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/
         $bar = array();
		if($this->Session->check('return')){
			$bar['Retour'] = array(
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index5', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$bar['Retour'] = array(
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index5', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		 $this->set('toolbar', $bar);
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'EVALUATION AGENT / CHEF DE SERVICE & CELLULE / DIRECTEUR <span class="pageTitle">'.$name . SEP . $username.'</span>');
        //$this->paginate['Carevaluation']['conditions'][] = array('Carevaluation.date_eval'=>'2019');
		
	    $this->paginate['Carevaluation']['conditions'][] = array('Carevaluation.agdossier_id'=>$dossier);
	    $this->set('carevaluations', $this->paginate('Carevaluation'));

		$this->set('contratid', $contratid);
		$this->set('dossier', $dossier);
		$this->set('matricule', $matricule);
        $this->set('numcontrat', $numcontrat);

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
		
	    $this->set('statuts', array('1'=>'En cours','2'=>'Echu'));
	}


	public function index2() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $periode = '2019';
       
		/******************************************************/
		if(isset($postData['Carevaluation']['valider'])){
			$periode = $postData['Carevaluation']['periode'];
			$this->data = $postData;
		}
		/******************************************************/

        /********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/
       

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT DES NOTATIONS <span class="pageTitle">'.$name . SEP . $username.'</span>');
        $this->paginate['Carevaluation']['conditions'][] = array('Carevaluation.date_eval'=>$periode);
		$this->set('carevaluations', $this->paginate('Carevaluation'));

		$this->set('periode', $periode);
		
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
	    
	}


	public function edit() {
		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		$postData = $this->postData();
		
		$id = $this->getGetParam('id');

		
		$contratid = $this->getGetParam('contratid');
		/*-----------------------CONTRAT-----------------------*/
		$tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contratid), 'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
		$matricule = $tmp[0]['Agcontrat']['matricule'];
		$numcontrat = $tmp[0]['Agcontrat']['num_contrat'];
       /*-----------------------Agavencement-----------------------*/
		$av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contratid), 'recursive'=>0));
		$classID = $av[0]['Agavencement']['paramclassification_id'];
		/*-----------------------Classification-----------------------*/
		$cla = $this->Paramclassification->find('all', array('conditions'=>array('Paramclassification.id='.$classID), 'recursive'=>0));
		$catsocproID = $cla[0]['Paramclassification']['paramsociopro_id'];
		/*-----------------------Agaffectmutation-----------------------*/
		$aff = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contratid), 'recursive'=>0));
		$typfoncID = $aff[0]['Agaffectmutation']['paramtypefonction_id'];
		//Paramtypefonction
		/*-----------------------Paramtypefonction-----------------------*/
		$typ = $this->Paramtypefonction->find('all', array('conditions'=>array('Paramtypefonction.id='.$typfoncID), 'recursive'=>0));
		$typeval = $typ[0]['Paramtypefonction']['paramtypevaluation_id'];
        /********************************************************************/
        /*---------------------Critére appréciation-------------------------*/
       /* $carcriteres = $this->Carcritere->find('all', array('conditions'=>array("Carcritere.paramsociopro_id='{$classID}'","Carcritere.paramtypefonction_id='{$typfoncID}'"), 'recursive'=>0));*/

        $carcriteres = $this->Carcritere->find('all', array('conditions'=>array("Carcritere. 	paramtypevaluation_id='{$typeval}'"), 'recursive'=>0));
		
		
       
		if(isset($postData['Carevaluation']['submit']) && isset($postData['Carevaluation'])){
            if($postData['Carevaluation']['date_eval']<>'' &&
               $postData['Carevaluation']['agcontrat_id']<>'' &&
               $postData['Carevaluation']['agdossier_id']<>'' &&
               $postData['Carevaluation']['matricule']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' carevaluation ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){

						$saveId = $this->Carevaluation->save($postData);
                        $carevaluationid = $this->Carevaluation->id;

					 
						$sum = 0;
						$moy = 0;
						
						/*-------------------------------------------------*/
                        foreach ($postData['Carevaluation'] as $index => $page)
		                {
						    
						    if(isset($page['critere_id']) &&   $page['critere_id']<>'' &&
						       isset($page['souscritere_id']) &&   $page['souscritere_id']<>'' &&
					           isset($page['note']) &&   $page['note']<>'' &&
				               isset($page['bareme']) &&   $page['bareme']<>'')
							{   
								$count++;
						        
						        $sum = $sum + $page['note'];
                                
                                $pg = array('Carevalitem'=>array(
							        'carevaluation_id' => $carevaluationid,
						            'carcritere_id' => $page['critere_id'],
						            'carsouscritere_id' => $page['souscritere_id'],
						            'note' => $page['note'],
						            'bareme' => $page['bareme']
						            
									   )
						            );
						        $this->Carevalitem->save($pg);	
							
							}
						}
                            

                            $crit = array('Carevaluation'=>array(
								        'id' => $carevaluationid,
							            'moyenne' => $sum
							            )
							        );
							        $this->Carevaluation->save($crit);
                        /*-------------------------------------------------*/
						
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
								$this->redirect(array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)));
							}else{
								$this->redirect(array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)));
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
				
		if($id){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Carevaluation->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION EVALUATION AGENT / CHEF DE SERVICE & CELLULE / DIRECTEUR':'MODIFICATION EVALUATION AGENT / CHEF DE SERVICE & CELLULE / DIRECTEUR'));

		$this->set('toolbar', $toolbar);
		
		$this->set('varcontrat', $contratid);
		$this->set('dossier', $dossier);
		$this->set('matricule', $matricule);
        $this->set('numcontrat', $numcontrat);

        $this->set('carcriteres',$carcriteres);

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

		
	}
    
    
	public function modif() {
		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');

		$carevaluations = $this->Carevaluation->find('all', array('conditions'=>array('Carevaluation.id='.$id), 'recursive'=>0));
		$contratid = $carevaluations[0]['Carevaluation']['agcontrat_id'];
		
		/*-----------------------CONTRAT-----------------------*/
		$tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contratid), 'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
		$matricule = $tmp[0]['Agcontrat']['matricule'];
		$numcontrat = $tmp[0]['Agcontrat']['num_contrat'];
        /*-----------------------Agavencement-----------------------*/
		$av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contratid), 'recursive'=>0));
		$classID = $av[0]['Agavencement']['paramclassification_id'];

		 /*-----------------------Classification-----------------------*/
		$cla = $this->Paramclassification->find('all', array('conditions'=>array('Paramclassification.id='.$classID), 'recursive'=>0));
		$catsocproID = $cla[0]['Paramclassification']['paramsociopro_id'];
		
        /*-----------------------Agaffectmutation-----------------------*/
		$aff = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contratid), 'recursive'=>0));
		$typfoncID = $aff[0]['Agaffectmutation']['paramtypefonction_id'];
		/*-----------------------Paramtypefonction-----------------------*/
		$typ = $this->Paramtypefonction->find('all', array('conditions'=>array('Paramtypefonction.id='.$typfoncID), 'recursive'=>0));
		$typeval = $typ[0]['Paramtypefonction']['paramtypevaluation_id'];
        /********************************************************************/
        /*---------------------Critére appréciation-------------------------*/
       /* $carcriteres = $this->Carcritere->find('all', array('conditions'=>array("Carcritere.paramsociopro_id='{$classID}'","Carcritere.paramtypefonction_id='{$typfoncID}'"), 'recursive'=>0));*/

        $criteres = $this->Carcritere->find('all', array('conditions'=>array("Carcritere.paramtypevaluation_id='{$typeval}'"), 'recursive'=>0));
		
		$postData = $this->postData();
       
		if(isset($postData['Carevaluation']['submit']) && isset($postData['Carevaluation'])){
            if($postData['Carevaluation']['evaluateur']<>'' &&
               $postData['Carevaluation']['id']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' carevaluation ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){

						$saveId = $this->Carevaluation->save($postData);
						$sum = 0;
						$moy = 0;
						
						/*-------------------------------------------------*/
                        foreach ($postData['Carevaluation'] as $index => $page)
		                {
						    
						    if(isset($page['item_id']) &&   $page['item_id']<>'' &&
						       isset($page['critere_id']) &&   $page['critere_id']<>'' &&
						       isset($page['souscritere_id']) &&   $page['souscritere_id']<>'' &&
					           isset($page['note']) &&   $page['note']<>'' &&
				               isset($page['bareme']) &&   $page['bareme']<>'')
							{   
								$count++;
						        
						        $sum = $sum + $page['note'];
                                
                                $pg = array('Carevalitem'=>array(
							        'id' => $page['item_id'],
						            'note' => $page['note']
									   )
						            );
						        $this->Carevalitem->save($pg);	
							}
						}
                            
                            $eval = array('Carevaluation'=>array(
								        'id' => $id,
							            'moyenne' => $sum
							            )
							        );
							        $this->Carevaluation->save($eval);
                        /*-------------------------------------------------*/
						/*----------------------------------*/
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
								$this->redirect(array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)));
							}else{
								$this->redirect(array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)));
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
				
		if($id){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Carevaluation->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'VALIDATION EVALUATION AGENT / CHEF DE SERVICE & CELLULE / DIRECTEUR':'VALIDATION EVALUATION AGENT / CHEF DE SERVICE & CELLULE / DIRECTEUR'));

		$carevalitems = $this->Carevalitem->find('all', array('conditions'=>array('Carevalitem. 	carevaluation_id='.$id), 'recursive'=>0));

		$this->set('toolbar', $toolbar);
		
		
		$this->set('varcontrat', $contratid);
		$this->set('dossier', $dossier);
		$this->set('matricule', $matricule);
        $this->set('numcontrat', $numcontrat);

        $this->set('criteres',$criteres);
        $this->set('carevalitems',$carevalitems);
         $this->set('carevaluationid',$id);

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('carcriteres', $this->Carcritere->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('statuts', array('Proposition'=>'Proposition','Valider'=>'Valider',));
		
	}


	public function validation() {
		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');

		$carevaluations = $this->Carevaluation->find('all', array('conditions'=>array('Carevaluation.id='.$id), 'recursive'=>0));
		$contratid = $carevaluations[0]['Carevaluation']['agcontrat_id'];
		
		/*-----------------------CONTRAT-----------------------*/
		$tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contratid), 'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
		$matricule = $tmp[0]['Agcontrat']['matricule'];
		$numcontrat = $tmp[0]['Agcontrat']['num_contrat'];
        /*-----------------------Agavencement-----------------------*/
		$av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contratid), 'recursive'=>0));
		$classID = $av[0]['Agavencement']['paramclassification_id'];

		 /*-----------------------Classification-----------------------*/
		$cla = $this->Paramclassification->find('all', array('conditions'=>array('Paramclassification.id='.$classID), 'recursive'=>0));
		$catsocproID = $cla[0]['Paramclassification']['paramsociopro_id'];
		
        /*-----------------------Agaffectmutation-----------------------*/
		$aff = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contratid), 'recursive'=>0));
		$typfoncID = $aff[0]['Agaffectmutation']['paramtypefonction_id'];
		/*-----------------------Paramtypefonction-----------------------*/
		$typ = $this->Paramtypefonction->find('all', array('conditions'=>array('Paramtypefonction.id='.$typfoncID), 'recursive'=>0));
		$typeval = $typ[0]['Paramtypefonction']['paramtypevaluation_id'];
        /********************************************************************/
        /*---------------------Critére appréciation-------------------------*/
       /* $carcriteres = $this->Carcritere->find('all', array('conditions'=>array("Carcritere.paramsociopro_id='{$classID}'","Carcritere.paramtypefonction_id='{$typfoncID}'"), 'recursive'=>0));*/

        $criteres = $this->Carcritere->find('all', array('conditions'=>array("Carcritere.paramtypevaluation_id='{$typeval}'"), 'recursive'=>0));
		
		/*---------------------*/
		$postData = $this->postData();
		//////////////////////////UPLOAD DECISION///////////////////////////
        if(isset($postData['Carevaluation'])){
		    $error = false;
			if(isset($_FILES['fichier']['tmp_name']) && is_uploaded_file($_FILES['fichier']['tmp_name'])){
				$newName = $_FILES['fichier']['name'];
				if(move_uploaded_file($_FILES['fichier']['tmp_name'], 'fichier_numeriques/evaluations/' . $newName)){
					 $postData['Carevaluation']['fichier'] = $newName;
				}
				else
				{
				    $error = true;
				}
		   }
		
		}
		/////////////////////////////////////////////////////////////////////
       
		if(isset($postData['Carevaluation']['submit']) && isset($postData['Carevaluation'])){
            if($postData['Carevaluation']['statut']<>'' &&
               $postData['Carevaluation']['id']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' carevaluation ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){

						$saveId = $this->Carevaluation->save($postData);
						$sum = 0;
						$moy = 0;
						
						/*-------------------------------------------------*/
                        foreach ($postData['Carevaluation'] as $index => $page)
		                {
						    
						    if(isset($page['item_id']) &&   $page['item_id']<>'' &&
						       isset($page['critere_id']) &&   $page['critere_id']<>'' &&
						       isset($page['souscritere_id']) &&   $page['souscritere_id']<>'' &&
					           isset($page['note']) &&   $page['note']<>'' &&
				               isset($page['bareme']) &&   $page['bareme']<>'')
							{   
								$count++;
						        
						        $sum = $sum + $page['note'];
                                
                                $pg = array('Carevalitem'=>array(
							        'id' => $page['item_id'],
						            'note' => $page['note']
									   )
						            );
						        $this->Carevalitem->save($pg);	
							}
						}
                            
                            $eval = array('Carevaluation'=>array(
								        'id' => $id,
							            'moyenne' => $sum
							            )
							        );
							        $this->Carevaluation->save($eval);
                        /*-------------------------------------------------*/
						/*----------------------------------*/
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
								$this->redirect(array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)));
							}else{
								$this->redirect(array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)));
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
				
		if($id){
			if(!empty($postData)){
				$this->data = $postData;
			}else{
				$this->data = $this->Carevaluation->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array('contratid:'.$contratid)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'VALIDATION EVALUATION AGENT':'VALIDATION EVALUATION AGENT'));

		$carevalitems = $this->Carevalitem->find('all', array('conditions'=>array('Carevalitem.carevaluation_id='.$id), 'recursive'=>0));

		$this->set('toolbar', $toolbar);
		
		
		$this->set('varcontrat', $contratid);
		$this->set('dossier', $dossier);
		$this->set('matricule', $matricule);
        $this->set('numcontrat', $numcontrat);

        $this->set('criteres',$criteres);
        $this->set('carevalitems',$carevalitems);
        //$id
         $this->set('carevaluationid',$id);
        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('carcriteres', $this->Carcritere->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('statuts', array('Proposition'=>'Proposition','Valider'=>'Valider',));
		
	}
   

	public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE D\'UN JOURNAL'));
		$this->set('toolbar', $toolbar);
	}

	public function search2() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index2', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Carevaluations', 'view'=>'index2', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE D\'UN JOURNAL'));
		$this->set('toolbar', $toolbar);
	}
	
	
	public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		$ids = explode('|', (string)$this->getGetParam('id'));
		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Carevaluation->find('all', array('conditions'=>array(array($this->Carevaluation->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Carevaluations';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Carevaluation']['id'];
				
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Carevaluation->delete($ids);
			
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect($this->Session->read('return'));
		}else{
			$this->redirect(array('controller'=>'Carevaluations', 'view'=>'index'));
		}
	}


	
	public function etat() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		$periode = $this->getGetParam('periode');
		
		
		$this->layout = 'blank';
		
		$this->set('periode',$periode);
		

		$this->set('nomprenoms',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('matricules',$this->Agdossier->find('list',array('list'=>array('id','ag_matricule')
		,  'order'=>'Agdossier.ag_matricule ASC')));

		 $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
		
		$this->set('style', '
			.cannevas{
				padding-top:10px;
				width:680px;	
				margin: 0 auto;
				color: #4c4c4c;
			}
			body{ 
			    font-size: 15px;
			    color: #4c4c4c;
			}
			.ulData ul{
				margin:0 0 0 40px;
				font-size:12px
			}
			.table{
				border:0;
				width:100%;
			}
			.table td{
				
				padding:8px;
				font-size:15px
			}
			.table th{
				
				padding:8px;
				font-size:18px
			}
			.table th h2{
				margin:0;
			}
			.table table{
				
				white-space:nowrap
			}
			.table table td{
				padding:3px;
			}
			.table table td{
				border:none;
			}
			
			.center{
				text-align:center;
			}
			.big{
				font-size:20px
			}
			
		');	
			
	}

	
	public function etat2() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		$periode = $this->getGetParam('periode');
		
		
		$this->layout = 'blank';
		
		$this->set('periode',$periode);

		$this->set('nomprenoms',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('matricules',$this->Agdossier->find('list',array('list'=>array('id','ag_matricule')
		,  'order'=>'Agdossier.ag_matricule ASC')));

		 $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
		
		$this->set('style', '
			.cannevas{
				padding-top:10px;
				width:680px;	
				margin: 0 auto;
				color: #4c4c4c;
			}
			body{ 
			    font-size: 15px;
			    color: #4c4c4c;
			}
			.ulData ul{
				margin:0 0 0 40px;
				font-size:12px
			}
			.table{
				border:0;
				width:100%;
			}
			.table td{
				
				padding:8px;
				font-size:15px
			}
			.table th{
				
				padding:8px;
				font-size:18px
			}
			.table th h2{
				margin:0;
			}
			.table table{
				
				white-space:nowrap
			}
			.table table td{
				padding:3px;
			}
			.table table td{
				border:none;
			}
			
			.center{
				text-align:center;
			}
			.big{
				font-size:20px
			}
			
		');	
			
	}

    
	public function etat3() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		$periode = $this->getGetParam('periode');
		
		
		$this->layout = 'blank';
		
		$this->set('periode',$periode);

		$this->set('nomprenoms',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('matricules',$this->Agdossier->find('list',array('list'=>array('id','ag_matricule')
		,  'order'=>'Agdossier.ag_matricule ASC')));

		 $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
		
		$this->set('style', '
			.cannevas{
				padding-top:10px;
				width:680px;	
				margin: 0 auto;
				color: #4c4c4c;
			}
			body{ 
			    font-size: 15px;
			    color: #4c4c4c;
			}
			.ulData ul{
				margin:0 0 0 40px;
				font-size:12px
			}
			.table{
				border:0;
				width:100%;
			}
			.table td{
				
				padding:8px;
				font-size:15px
			}
			.table th{
				
				padding:8px;
				font-size:18px
			}
			.table th h2{
				margin:0;
			}
			.table table{
				
				white-space:nowrap
			}
			.table table td{
				padding:3px;
			}
			.table table td{
				border:none;
			}
			
			.center{
				text-align:center;
			}
			.big{
				font-size:20px
			}
			
		');	
			
	}


    /*--------------------------------------*/
    public function fiche() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$carevaluationid = $this->getGetParam('carevaluationid');

		/****************Carevaluation******************************/
        $data = $this->Carevaluation->find('all', array('conditions'=>array('Carevaluation.id='.$carevaluationid), 'recursive'=>0));
        $contratid = $data[0]['Carevaluation']['agcontrat_id'];
		$dossierid = $data[0]['Carevaluation']['agdossier_id'];
		$matricule = $data[0]['Carevaluation']['matricule'];
		$date_eval = $data[0]['Carevaluation']['date_eval'];
		$moyenne = $data[0]['Carevaluation']['moyenne'];
		$evaluateur = $data[0]['Carevaluation']['evaluateur'];
		
		
	    $this->set('contratid', $contratid);
	    $this->set('dossierid', $dossierid);
	    $this->set('matricule', $matricule);
	    $this->set('date_eval', $date_eval);
	    $this->set('moyenne', $moyenne);
	    $this->set('evaluateur', $evaluateur);
	    $this->set('carevaluationid', $carevaluationid);
         /****************Carevalitem******************************/
        /*$criteres = $this->Carevalitem->find('all', array('conditions'=>array('Carevalitem.carevaluation_id='.$carevaluationid), 'recursive'=>0));
        $this->set('criteres', $criteres);*/
	    
        /*-----------------------Agaffectmutation-----------------------*/
		$aff = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contratid), 'recursive'=>0));
		$fonctionID = $aff[0]['Agaffectmutation']['paramfonction_id'];
		$direction_id = $aff[0]['Agaffectmutation']['paramdirection_id'];
		$typfoncID = $aff[0]['Agaffectmutation']['paramtypefonction_id'];
        
        $this->set('fonctionID', $fonctionID);
        $this->set('direction_id', $direction_id);
        /*-----------------------Paramtypefonction-----------------------*/
		$typ = $this->Paramtypefonction->find('all', array('conditions'=>array('Paramtypefonction.id='.$typfoncID), 'recursive'=>0));
		$typeval = $typ[0]['Paramtypefonction']['paramtypevaluation_id'];
		$this->set('typeval', $typeval);
        /*---------------------Critére appréciation-------------------------*/
        $criteres = $this->Carcritere->find('all', array('conditions'=>array("Carcritere.paramtypevaluation_id='{$typeval}'"), 'recursive'=>0));
        
        $this->set('criteres', $criteres);
        /*-----------------------Agavencement-----------------------*/
		$av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contratid), 'recursive'=>0));
		$classification_id = $av[0]['Agavencement']['paramclassification_id'];
		$echelon_id = $av[0]['Agavencement']['paramechelon_id'];
        
        $this->set('classification_id', $classification_id);
        $this->set('echelon_id', $echelon_id);

         /*-----------------------Affectation/Mutation-----------------------*/
		/*$affec = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contratid), 'recursive'=>0));
		$direction_id = $affec[0]['Agaffectmutation']['paramdirection_id'];*/

	    

	    $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('paramfonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

         $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','nom_direction'), 'order'=>'id ASC')));
		/*-------------------------------------------*/
        /*-----------------------CONTRAT-----------------------*/
		$alpha = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$evaluateur), 'recursive'=>0));
		$eval_contrat = $alpha[0]['Agcontrat']['id'];
        $eval_contrat = $alpha[0]['Agcontrat']['id'];
		 /*-----------------------Agavencement-----------------------*/
		$beta = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$eval_contrat), 'recursive'=>0));
		$eval_class = $beta[0]['Agavencement']['paramclassification_id'];
		$eval_ech = $beta[0]['Agavencement']['paramechelon_id'];
        
        $this->set('eval_class', $eval_class);
        $this->set('eval_ech', $eval_ech);

         /*-----------------------Affectation/Mutation-----------------------*/
		$gama = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$eval_contrat), 'recursive'=>0));
		$eval_fonct = $gama[0]['Agaffectmutation']['paramfonction_id'];

	     $this->set('eval_fonct', $eval_fonct);
		$this->set('style', '
			
			table tr{
				font-size:15px;
				font-family:Arial;
			}
			table tr td,
			table tr th,{
				padding:10px 5px;
				
			}
		');
			
	}



	public function statistique() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Carevaluations'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       
		$this->set('pageTitle', 'Nombre de journaux par type');
			
        
        $this->set('css', array('amcharts/plugins/export/export'));
		$this->set('js', array('jsexport/canvasjs.min',
			                   'jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
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