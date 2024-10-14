<?php 
/**
 * 
 * 
 * 
 */

class AgavencementsController extends AppController{	
	var $paginate = array(
		'Agavencement'=>array(
			'model'=>'Agavencement','sort'=>'id', 'agavencement'=>'ASC',
			'page'=>1, 'recursive'=>0, 'limit'=>18
		),
		
	
	);	
	
	var $uses = array('User','Agdossier','Paramclassification','Paramechelon','Paramnatcontrat','Agcontrat','Paramstructurecotsocial','Paramservice','Paramdirection','Parambanque','Parammodepaie','Paramfonction','Paramstatactivation','Paramtypemvt','Paramtypefonction','Paramsociopro');
	
	
	
	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agavencements'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        //id
        $contrat = $this->getGetParam('contratid');
        /******************************************************************/
        $tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contrat), 'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
        /********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/
	    $bar = array();
		if($this->Session->check('return')){
			$bar['Retour'] = array(
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index3', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$bar['Retour'] = array(
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index3', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		 $this->set('toolbar', $bar);

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'AVANCEMENTS <span class="pageTitle">'.$name . SEP . $username.'</span>');

		$this->paginate['Agavencement']['conditions'][] = array('Agavencement.agcontrat_id'=>$contrat);
		$this->set('agavencements', $this->paginate('Agavencement'));
		
        $this->set('varcontrat', $contrat);
		$this->set('dossier', $dossier);
        
		$this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramnatcontrats', $this->Paramnatcontrat->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramstructurecotsocials', $this->Paramstructurecotsocial->find('list', array('list'=>array('id','nom_structure'), 'order'=>'id ASC')));

		$this->set('services', $this->Paramservice->find('list', array('list'=>array('id','nom_service'), 'order'=>'id ASC')));

		$this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','nom_direction'), 'order'=>'id ASC')));

		$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('statuts', $this->Paramstatactivation->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
		$this->set('mouvements', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('nivrespons', $this->Paramtypefonction->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
        
        

	}

	public function index2() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agavencements'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /*****************************************************************/
	    $dossiers = $this->Agavencement->find('list', array(
								'list'=>array('Agavencement.id', 
											  'Agavencement.matricule', 
											  
								          ),
								'fields'=>array('Agavencement.id', 
											    'Agavencement.matricule',
											    
								               ),
								'joins'=>array(
									array(
									'type' => 'LEFT',
									'alias' => 'Paramclassification',
									'table' => 'paramclassifications',
									'conditions' => array('Agavencement.paramclassification_id = Paramclassification.id'),
						
									),
								),
								
							));
	    /****************************************************************/


		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'AVANCEMENTS <span class="pageTitle">'.$name . SEP . $username.'</span>');

		$this->set('dossiers', $dossiers);
		$this->set('agavencements', $this->paginate('Agavencement'));
		
      
        
		$this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramnatcontrats', $this->Paramnatcontrat->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramstructurecotsocials', $this->Paramstructurecotsocial->find('list', array('list'=>array('id','nom_structure'), 'order'=>'id ASC')));

		$this->set('services', $this->Paramservice->find('list', array('list'=>array('id','nom_service'), 'order'=>'id ASC')));

		$this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','nom_direction'), 'order'=>'id ASC')));

		$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('statuts', $this->Paramstatactivation->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
		$this->set('mouvements', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('nivrespons', $this->Paramtypefonction->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
        
        

	}
	
	public function edit() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agavencements'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		/*----------------------------------------------*/
		$varcontrat = $this->getGetParam('varcontrat');
		$tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$varcontrat), 'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
		$matricule = $tmp[0]['Agcontrat']['matricule'];
		$numcontrat = $tmp[0]['Agcontrat']['num_contrat'];
		$naturecontrat = $tmp[0]['Agcontrat']['paramnatcontrat_id'];
		$dateDebut = $tmp[0]['Agcontrat']['date_debut'];
		$dateFin =   $tmp[0]['Agcontrat']['date_fin'];
        /********************************************************************/
        $yearbirth = 0;
        $monthbirth = 0;
        $datebirth = 0;
        
		$postData = $this->postData();
		if(isset($postData['Agavencement']['submit']) && isset($postData['Agavencement'])){
            if($postData['Agavencement']['agcontrat_id']<>'' &&
			   $postData['Agavencement']['paramclassification_id']<>'' &&
			   $postData['Agavencement']['paramechelon_id']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Agavencement ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){
					/*-----------------------------------*/
                    $dossier = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Agavencement']['agdossier_id']), 'recursive'=>0));
		            $matricule = $dossier[0]['Agdossier']['ag_matricule'];
		            $datenaiss = $dossier[0]['Agdossier']['ag_date_naiss'];
                    /*-----------------------------------*/	
                   // print_r($matricule);
                    $postData['Agavencement']['matricule']  = $matricule;	
                    /*======================================================*/
                    if($naturecontrat == 2 || $naturecontrat == 4 || $naturecontrat == 3)
                    {
                        $yearbirth = date("Y",strtotime($datenaiss));
				        $monthbirth = date("m",strtotime($datenaiss));
				        $datebirth =  date("d",strtotime($datenaiss));
				        /*--------------------------------------------------*/
				        $clas = $this->Paramclassification->find('all', array('conditions'=>array('Paramclassification.id='.$postData['Agavencement']['paramclassification_id']), 'recursive'=>0));
				        $socioproid = $clas[0]['Paramclassification']['paramsociopro_id'];
				        $classid = $clas[0]['Paramclassification']['id'];

				         if($classid > 4)
				         {
					        /*--------------------------------------------------*/
					        $socio = $this->Paramsociopro->find('all', array('conditions'=>array('Paramsociopro.id='.$socioproid), 'recursive'=>0));
					        $ageretraite = $socio[0]['Paramsociopro']['age_retraite'];

					        $year = $yearbirth + $ageretraite;
					        $annee = $year - 1;

					        $dateRetraite = $year.'-'.$monthbirth.'-'.$datebirth;
					        $dateAlerte = $annee.'-'.$monthbirth.'-'.$datebirth;

					        $postData['Agavencement']['date_retraite']  = $dateRetraite;
					        $postData['Agavencement']['date_alerte']  = $dateAlerte;
				        }
				        else
				        {
				        	$str = $matricule;
							$pattern = "/C/i";
							if(preg_match($pattern, $str) == 1)
							{
                              #--------------------------
							   $socio = $this->Paramsociopro->find('all', array('conditions'=>array("Paramsociopro.id='4'"), 'recursive'=>0));
						        $ageretraite = $socio[0]['Paramsociopro']['age_retraite'];

						        $year = $yearbirth + $ageretraite;
						        $annee = $year - 1;

						        $dateRetraite = $year.'-'.$monthbirth.'-'.$datebirth;
						        $dateAlerte = $annee.'-'.$monthbirth.'-'.$datebirth;

						        $postData['Agavencement']['date_retraite']  = $dateRetraite;
						        $postData['Agavencement']['date_alerte']  = $dateAlerte;
							}
							else
							{
                              #--------------------------
							   $socio = $this->Paramsociopro->find('all', array('conditions'=>array("Paramsociopro.id='1'"), 'recursive'=>0));
						        $ageretraite = $socio[0]['Paramsociopro']['age_retraite'];

						        $year = $yearbirth + $ageretraite;
						        $annee = $year - 1;

						        $dateRetraite = $year.'-'.$monthbirth.'-'.$datebirth;
						        $dateAlerte = $annee.'-'.$monthbirth.'-'.$datebirth;

						        $postData['Agavencement']['date_retraite']  = $dateRetraite;
						        $postData['Agavencement']['date_alerte']  = $dateAlerte;
							}
				        }

                    }
                    elseif($naturecontrat == 1)
                    {
                         //$duree = strtotime($dateFin) - strtotime($dateDebut);
                    	 $datAlert =0;
                         $duree = date("m",strtotime(date_diff($dateFin,$dateDebut)));
                         if($duree >= 12)
                         {
                              $datAlert = strtotime($dateFin) - (60 * 60 * 24 * 30 * 3);
                              $postData['Agavencement']['date_alerte']  = date("Y-m-d",$datAlert);
                              $postData['Agavencement']['date_retraite']  = $dateFin;
                         }
                         else
                         {
                              $datAlert = strtotime($dateFin) - (60 * 60 * 24 * 30);
                              $postData['Agavencement']['date_alerte']  = date("Y-m-d",$datAlert);
                              $postData['Agavencement']['date_retraite']  = $dateFin;
                         }
                    }
                    else
                    {

                    }
                    
                    /*======================================================*/		
					$saveId = $this->Agavencement->save($postData);
						
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
					$this->redirect(array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
							}else{
								$this->redirect(array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
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
				$this->data = $this->Agavencement->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION AVANCEMENT':'MODIFICATION AVANCEMENT'));
		$this->set('toolbar', $toolbar);

		$this->set('varcontrat', $varcontrat);
		$this->set('dossier', $dossier);
		$this->set('matricule', $matricule);
        $this->set('numcontrat', $numcontrat);

        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramnatcontrats', $this->Paramnatcontrat->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramstructurecotsocials', $this->Paramstructurecotsocial->find('list', array('list'=>array('id','nom_structure'), 'order'=>'id ASC')));

		$this->set('services', $this->Paramservice->find('list', array('list'=>array('id','nom_service'), 'order'=>'id ASC')));

		$this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','nom_direction'), 'order'=>'id ASC')));

		$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('statuts', $this->Paramstatactivation->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
		$this->set('mouvements', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('nivrespons', $this->Paramtypefonction->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
	}

    
	public function edit2() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agavencements'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
         $tmp = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.id='.$id), 'recursive'=>0));
		$varcontrat = $tmp[0]['Agavencement']['agcontrat_id'];
		$vardossier = $tmp[0]['Agavencement']['agdossier_id'];
		$varmat = $tmp[0]['Agavencement']['matricule'];
		/*---------------------*/
		$postData = $this->postData();
		if(isset($postData['Agavencement']['submit']) && isset($postData['Agavencement'])){
            if($postData['Agavencement']['agcontrat_id']<>'' &&
			   $postData['Agavencement']['paramclassification_id']<>'' &&
			   $postData['Agavencement']['paramechelon_id']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Agavencement ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){
					/*-----------------------------------*/
                    $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Agavencement']['agdossier_id']), 'recursive'=>0));
		            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
                    /*-----------------------------------*/	
                   // print_r($matricule);
                   $postData['Agavencement']['matricule']  = $matricule;			
					$saveId = $this->Agavencement->save($postData);
						
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
					$this->redirect(array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
							}else{
								$this->redirect(array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
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
				$this->data = $this->Agavencement->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION AVANCEMENT':'MODIFICATION AVANCEMENT'));
		$this->set('toolbar', $toolbar);

		$this->set('varcontrat', $varcontrat);
		$this->set('vardossier', $vardossier);
		$this->set('varmat', $varmat);

        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

		$this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramnatcontrats', $this->Paramnatcontrat->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramstructurecotsocials', $this->Paramstructurecotsocial->find('list', array('list'=>array('id','nom_structure'), 'order'=>'id ASC')));

		$this->set('services', $this->Paramservice->find('list', array('list'=>array('id','nom_service'), 'order'=>'id ASC')));

		$this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','nom_direction'), 'order'=>'id ASC')));

		$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('statuts', $this->Paramstatactivation->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
		$this->set('mouvements', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('nivrespons', $this->Paramtypefonction->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
	}



	public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agavencements'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agavencements', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE AVANCEMENT'));
		$this->set('toolbar', $toolbar);
	}
	
	
	public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agavencements'));

		$ids = explode('|', (string)$this->getGetParam('id'));
      

		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Agavencement->find('all', array('conditions'=>array(array($this->Agavencement->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Agavencements';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Agavencement']['id'];
				
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Agavencement->delete($ids);
			
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect($this->Session->read('return'));
		}else{
			$this->redirect(array('controller'=>'Agavencements', 'view'=>'index'));
		}
	}


	public function etat() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agavencements'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
		
		
		$this->set('pageTitle', ('LISTE DES AFFECTATIONS/MUTATIONS'));
		//$this->set('toolbar', $toolbar);
		$this->set('Agavencements', $this->paginate('Agavencement'));
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