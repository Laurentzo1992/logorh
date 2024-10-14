<?php 
/**
 * 
 * 
 * 
 */

class AgindemnitevaleursController extends AppController{	
	var $paginate = array(
		'Agindemnitevaleur'=>array(
			'model'=>'Agindemnitevaleur','sort'=>'id', 'direction'=>'ASC',
			'page'=>1, 'recursive'=>0
		)
	);
	/**/
    var $uses = array('User','Agdossier','Paramindemnite','Agcontrat','Paramavoiret','Paramtypindprimavantage','Agindemnite');


	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $contratid = $this->getGetParam('contratid');
        print_r($contratid);
         /******************************************************************/
        $tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contratid),'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
        /********************************************************************/
        /********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/

	    $toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index7', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index7', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'Valeur élément de salaire<span class="pageTitle">'.$name . SEP . $username.'</span>');
		
        $this->paginate['Agindemnitevaleur']['conditions'][] = array('Agindemnitevaleur.agcontrat_id'=>$contratid);
		
		$this->set('agindemnitevaleurs', $this->paginate('Agindemnitevaleur'));
		$this->set('toolbar', $toolbar);
		$this->set('contratid', $contratid);
		$this->set('dossier',$dossier);

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
       
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('paramindemnites', $this->Paramindemnite->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        /*---------------*/
        $this->set('type', $this->Paramindemnite->find('list', array('list'=>array('id','paramtypindprimavantage_id'), 'order'=>'id ASC')));
        $this->set('avoiret', $this->Paramindemnite->find('list', array('list'=>array('id','paramavoiret_id'), 'order'=>'id ASC')));
        /*---------------*/
        $this->set('paramavoirets', $this->Paramavoiret->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('paramtypinds', $this->Paramtypindprimavantage->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));
	    
      
	}


	public function edit() {
       
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		$postData = $this->postData();
		/*----------------------------------------------*/
		$varcontrat = $this->getGetParam('varcontrat');
		$tmp = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$varcontrat), 'recursive'=>0));
		$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
		$matricule = $tmp[0]['Agcontrat']['matricule'];
		$numcontrat = $tmp[0]['Agcontrat']['num_contrat'];
		/*--------------------------------------------*/
		//Paramindemnite
		//$pindemnites = $this->Paramindemnite->find('all', array('recursive'=>0));
        //print_r($paramindemnites);

        $participants = array();
        $saveData = array();
	    if(isset($postData['Agindemnitevaleur']['submit']) && isset($postData['Agindemnitevaleur'])){
			$saveData = array();
			$log = ($this->getGetParam('id')?'Modification':'Creation') . ' session formation id:';
            
				if($accessLevel['view'] && $accessLevel['edit']){
					
					$saveIds = $this->Agindemnitevaleur->save($postData);

					if($saveIds){
						$log .= implode(',', $saveIds);
						$this->requestAction('Logs' ,'record', $log);
						$this->Session->setFlash('Enregistre avec succés');
						
						if($this->Session->check('return')){
							$this->redirect(array('controller'=>'Agindemnitevaleurs', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
						}else{
							$this->redirect(array('controller'=>'Agindemnitevaleurs', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
						}
					}else {
						
				}
			}			
			
		}
        /*----------------------------------------------------------------------------**/
        //Paramindemnite
		 $agindemnites = $this->Paramindemnite->find('all', array(
								'list'=>array('Agindemnite.id',
									          'Paramindemnite.libelle' 
								             ),
								'fields'=>array('Agindemnite.id',
									            'Paramindemnite.libelle'
								               ),
								'joins'=>array(
									array(
									'type' => 'LEFT',
									'alias' => 'Paramindemnite',
									'table' => 'paramindemnites',
									'conditions' => array('Agindemnite.paramindemnite_id = Paramindemnite.id'),
						            ),
								),
								'conditions'=>array(
									"Agindemnite.agcontrat_id = '".$varcontrat."'"

							    ),
							    
							));
		
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agindemnitevaleurs', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agindemnitevaleurs', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('js', array('autoCompleteIndemnite'));
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME  . SEP . 'CREATION AGENT INDEMNITE':'AGENT INDEMNITE'));
		$this->set('toolbar', $toolbar);
		$this->set('varcontrat', $varcontrat);
		$this->set('dossier', $dossier);
		$this->set('matricule', $matricule);
        $this->set('numcontrat', $numcontrat);
        
        $this->set('agindemnites', $agindemnites);
        //$this->set('pindemnites',$pindemnites);
        //$indemnites
  
        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramindemnites', $this->Paramindemnite->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

		$this->set('paramavoirets', $this->Paramavoiret->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('paramtypindprimavantages', $this->Paramtypindprimavantage->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));
	    
		
	}


    public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));

		$contratid = $this->getGetParam('contratid');

		
		$ids = explode('|', (string)$this->getGetParam('id'));
		$sessionid = $this->getGetParam('sessionid');
		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Agindemnitevaleur->find('all', array('conditions'=>array(array($this->Agindemnitevaleur->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Agindemnitevaleur';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Agindemnitevaleur']['id'];
				
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Agindemnitevaleur->delete($ids);
			
			$this->Session->setFlash($log);			
		}

		$this->redirect(array('controller'=>'Agindemnitevaleurs', 'view'=>'index', 'params'=>array('contratid:'.$contratid)));

	}

	public function attestationFormation(){
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		$sessionid = $this->getGetParam('sessionid');
		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));
	}

    
    public function attestation() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        
		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$sessionid = $this->getGetParam('sessionid');

		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));
		/****************SESSION DE FORMATION******************************/
        $data = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$sessionid), 'recursive'=>0));
		$themeID = $data[0]['Paramindemnite']['theme_id'];
		//$codesage = $data[0]['Paramindemnite']['code_sage'];
		$dateDebut = $data[0]['Paramindemnite']['date_debut'];
		$dateFin = $data[0]['Paramindemnite']['date_fin'];
		$heureDebut = $data[0]['Paramindemnite']['heure_debut'];
		$heureFin = $data[0]['Paramindemnite']['heure_fin'];
		$duree = $data[0]['Paramindemnite']['duree'];
		$lieu = $data[0]['Paramindemnite']['lieu'];
		$soustraitantID = $data[0]['Paramindemnite']['soustraitant_id'];
        $bailleur_id = $data[0]['Paramindemnite']['bailleur_id'];
        /*******************Premier responsable*********************************************/
        $presp = $this->Premieresp->find('all', array('conditions'=>array('Premieresp.statut = 1'), 'recursive'=>0));
		$pr_nom = $presp[0]['Premieresp']['pr_nom'];
		$pr_prenom = $presp[0]['Premieresp']['pr_prenom'];
		$pr_distinct = $presp[0]['Premieresp']['pr_distinct'];

		$this->set('pr_nom',$pr_nom);
		$this->set('pr_prenom',$pr_prenom);
		$this->set('pr_distinct',$pr_distinct);
	   /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule'];
		$thematiqueID = $thm[0]['Theme']['thematique_id'];

	   	
	     $thema = $this->Thematique->find('all', array('conditions'=>array('Thematique.id='.$thematiqueID), 'recursive'=>0)); 
	     $domID = $thema[0]['Thematique']['domaine_id'];
	   

		
		$doma = $this->Domaine->find('all', array('conditions'=>array('Domaine.id='.$domID), 'recursive'=>0));
		$domaine = $doma[0]['Domaine']['domaine'];
	
		/****************Responsable***************************************************/
         $resp = $this->Sessionresponsable->find('all', array('conditions'=>array('Sessionresponsable.sessionformation_id='.$sessionid), 'recursive'=>0));
		$responsableID =  $resp[0]['Sessionresponsable']['responsable_id'];
		

	    $respon = $this->Responsable->find('all', array('conditions'=>array('Responsable.id='.$responsableID), 'recursive'=>0));
	    $resp_nom =  $respon[0]['Responsable']['nom_resp'];
	    $resp_prenom =  $respon[0]['Responsable']['prenom_resp'];
	    $resp_tel =  $respon[0]['Responsable']['telephone_resp'];
	    $resp_email =  $respon[0]['Responsable']['email_resp'];
		/*******************************************************************************/
        $this->layout = 'blank';
        $this->set('Agindemnitevaleurs',$Agindemnitevaleurs);

        $this->set('theme',$theme);
        $this->set('domaine',$domaine);
        //$this->set('codesage',$codesage);
        $this->set('dateDebut',$dateDebut);
        $this->set('dateFin',$dateFin);
        $this->set('heureDebut',$heureDebut);
        $this->set('heureFin',$heureFin);
        $this->set('lieu',$lieu);
      
        $this->set('resp_nom',$resp_nom);
        $this->set('resp_prenom',$resp_prenom);
        $this->set('resp_tel',$resp_tel);
        $this->set('resp_email',$resp_email);
        $this->set('duree',$duree);
        
		$this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('titre', $this->Agdossier->find('list', array('list'=>array('id','pa_titre'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));

	    $this->set('civilites', $this->Civilite->find('list', array('list'=>array('id','civ'), 'order'=>'civ ASC')));
		
        $this->set('soustraitantID',$soustraitantID);
        $this->set('bailleur_id',$bailleur_id);

        $trait = $this->Soustraitant->find('all', array('conditions'=>array('Soustraitant.id='.$soustraitantID), 'recursive'=>0));
	    $soustraitant =  (isset($trait[0]['Soustraitant']['logo']))?$trait[0]['Soustraitant']['logo']:'';
       
	   $bail = $this->Bailleur->find('all', array('conditions'=>array('Bailleur.id='.$bailleur_id), 'recursive'=>0));
        
	    $bailleur = (isset($bail[0]['Bailleur']['logo']))?$bail[0]['Bailleur']['logo']:'';
		$this->set('soustraitant',$soustraitant);
		$this->set('bailleur',$bailleur);
		
		$this->set('style', '
			.certif{
				border:8px double #00adef;
				padding:2px;
                background:#fff url("../../img/attestationBackground.jpg") no-repeat center center;
			}

			.certif2{
				border:8px double #000;
				padding:2px;
			}
		');	
			
	}

    

    public function ficheinvitation() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$sessionid = $this->getGetParam('sessionid');

		/****************SESSION DE FORMATION******************************/
        $data = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$sessionid), 'recursive'=>0));
		$themeID = $data[0]['Paramindemnite']['theme_id'];
		$codesage = $data[0]['Paramindemnite']['code_sage'];
		$dateDebut = $data[0]['Paramindemnite']['date_debut'];
		$dateFin = $data[0]['Paramindemnite']['date_fin'];
		$heureDebut = $data[0]['Paramindemnite']['heure_debut'];
		$heureFin = $data[0]['Paramindemnite']['heure_fin'];
		$duree = $data[0]['Paramindemnite']['duree'];
		$lieu = $data[0]['Paramindemnite']['lieu'];
		$soustraitantID = $data[0]['Paramindemnite']['soustraitant_id'];
		$bailleurID = $data[0]['Paramindemnite']['bailleur_id'];
		
	   /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule'];
		$thematiqueID = $thm[0]['Theme']['thematique_id'];

	    $thema = $this->Thematique->find('all', array('conditions'=>array('Thematique.id='.$thematiqueID), 'recursive'=>0));
		$domID = $thema[0]['Thematique']['domaine_id'];

		 $doma = $this->Domaine->find('all', array('conditions'=>array('Domaine.id='.$domID), 'recursive'=>0));
		$domaine = $doma[0]['Domaine']['domaine'];
		
        $this->set('theme',$theme);
        $this->set('domaine',$domaine);
        $this->set('codesage',$codesage);
        $this->set('dateDebut',$dateDebut);
        $this->set('dateFin',$dateFin);
        $this->set('heureDebut',$heureDebut);
        $this->set('heureFin',$heureFin);
        $this->set('lieu',$lieu);
        $this->set('duree',$duree);
        $this->set('soustraitantID',$soustraitantID);
        $this->set('bailleurID',$bailleurID);
        

		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));

		
        $Sessionformateurs = $this->Sessionformateur->find('list', array('list'=>array('id','formateur_id'), 'conditions'=>'Sessionformateur.sessionformation_id='.$sessionid));
		
        $Sessionresponsables = $this->Sessionresponsable->find('list', array('list'=>array('id','responsable_id'), 'conditions'=>'Sessionresponsable.sessionformation_id ='.$sessionid));
		
		/*----------------------Agdossier------------------------------------*/
		$this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    
	    $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
        /*----------------------Formateur----------------------------------------------*/
	    $this->set('nomforma', $this->Formateur->find('list', array('list'=>array('id','nom'))));
		$this->set('prenomforma', $this->Formateur->find('list', array('list'=>array('id','prenom'))));
		/*---------------------Responsable-------------------------------------*/
		$this->set('resp_nom', $this->Responsable->find('list', array('list'=>array('id','nom_resp'))));
	    
	    $this->set('resp_prenom', $this->Responsable->find('list', array('list'=>array('id','prenom_resp'))));
		
		
		$this->layout = 'blank';
		
		$this->set('Agindemnitevaleurs',$Agindemnitevaleurs);
		$this->set('sessionformateurs',$Sessionformateurs);
		$this->set('sessionresponsables',$Sessionresponsables);

        $this->set('bailleurs', $this->Bailleur->find('list', array('list'=>array('id','nom_bailleur'), 'order'=>'Bailleur.nom_bailleur ASC')));
		
		
		
       
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

    

    public function fichevaluation() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$sessionid = $this->getGetParam('sessionid');

		/****************SESSION DE FORMATION******************************/
        $data = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$sessionid), 'recursive'=>0));
		$themeID = $data[0]['Paramindemnite']['theme_id'];
		$codesage = $data[0]['Paramindemnite']['code_sage'];
		$dateDebut = $data[0]['Paramindemnite']['date_debut'];
		$dateFin = $data[0]['Paramindemnite']['date_fin'];
		$heureDebut = $data[0]['Paramindemnite']['heure_debut'];
		$heureFin = $data[0]['Paramindemnite']['heure_fin'];
		$duree = $data[0]['Paramindemnite']['duree'];
		$lieu = $data[0]['Paramindemnite']['lieu'];
		$soustraitantID = $data[0]['Paramindemnite']['soustraitant_id'];
		$bailleurID = $data[0]['Paramindemnite']['bailleur_id'];
		
	   /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule']; // 	code
		$code = $thm[0]['Theme']['code'];
		$thematiqueID = $thm[0]['Theme']['thematique_id'];

	    $thema = $this->Thematique->find('all', array('conditions'=>array('Thematique.id='.$thematiqueID), 'recursive'=>0));
		$domID = $thema[0]['Thematique']['domaine_id'];

		 $doma = $this->Domaine->find('all', array('conditions'=>array('Domaine.id='.$domID), 'recursive'=>0));
		$domaine = $doma[0]['Domaine']['domaine'];
		/**********************Agindemnitevaleur LIEU**********************************************
        $sal = $this->Salle->find('all', array('conditions'=>array('Salle.id='.$salleID), 'recursive'=>0));
		$lieu =$sal[0]['Salle']['localisation'];
		/****************Responsable***************************************************/
         $resp = $this->Sessionresponsable->find('all', array('conditions'=>array('Sessionresponsable.sessionformation_id='.$sessionid), 'recursive'=>0));
		$responsableID =  $resp[0]['Sessionresponsable']['responsable_id'];
		

	    $respon = $this->Responsable->find('all', array('conditions'=>array('Responsable.id='.$responsableID), 'recursive'=>0));
	    $resp_nom =  $respon[0]['Responsable']['nom_resp'];
	    $resp_prenom =  $respon[0]['Responsable']['prenom_resp'];
	    $resp_tel =  $respon[0]['Responsable']['telephone_resp'];
	    $resp_email =  $respon[0]['Responsable']['email_resp'];
		/**********************Objectifs Spécifique************************************************/
		 $objs = $this->Objspecifique->find('all', array('conditions'=>array('Objspecifique.theme_id='.$themeID), 'recursive'=>0));
		$this->set('objspecifiques',$objs);
		/*****************************************************************************************/
        $this->set('code',$code);
        $this->set('theme',$theme);
        $this->set('domaine',$domaine);
        $this->set('codesage',$codesage);
        $this->set('dateDebut',$dateDebut);
        $this->set('dateFin',$dateFin);
        $this->set('heureDebut',$heureDebut);
        $this->set('heureFin',$heureFin);
        $this->set('lieu',$lieu);
        $this->set('resp_nom',$resp_nom);
        $this->set('resp_prenom',$resp_prenom);
        $this->set('resp_tel',$resp_tel);
        $this->set('resp_email',$resp_email);
        $this->set('duree',$duree);
        

		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));
		
		$this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
		//$data = $this->Marche->find('all', array('conditions'=>array('Marche.Agindemnitevaleur_id='.$Agindemnitevaleurid), 'recursive'=>0,'limit'=>500));
		
		$this->layout = 'blank';
		
		$this->set('Agindemnitevaleurs',$Agindemnitevaleurs);


		
		
       
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

	public function fichevalpostfor() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$sessionid = $this->getGetParam('sessionid');

		/****************SESSION DE FORMATION******************************/
        $data = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$sessionid), 'recursive'=>0));
		$themeID = $data[0]['Paramindemnite']['theme_id'];
		$codesage = $data[0]['Paramindemnite']['code_sage'];
		$dateDebut = $data[0]['Paramindemnite']['date_debut'];
		$dateFin = $data[0]['Paramindemnite']['date_fin'];
		$heureDebut = $data[0]['Paramindemnite']['heure_debut'];
		$heureFin = $data[0]['Paramindemnite']['heure_fin'];
		$duree = $data[0]['Paramindemnite']['duree'];
		$lieu = $data[0]['Paramindemnite']['lieu'];
		$soustraitantID = $data[0]['Paramindemnite']['soustraitant_id'];
		$bailleurID = $data[0]['Paramindemnite']['bailleur_id'];
	
	   /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule']; // 	code
		$code = $thm[0]['Theme']['code'];
		$thematiqueID = $thm[0]['Theme']['thematique_id'];

	    $thema = $this->Thematique->find('all', array('conditions'=>array('Thematique.id='.$thematiqueID), 'recursive'=>0));
		$domID = $thema[0]['Thematique']['domaine_id'];

		 $doma = $this->Domaine->find('all', array('conditions'=>array('Domaine.id='.$domID), 'recursive'=>0));
		$domaine = $doma[0]['Domaine']['domaine'];
		/*----------------OBJECTIFS SPECIFIQUES------------------------------*/	
		$objspecifiques = $this->Objspecifique->find('all', array('conditions'=>array('Objspecifique.theme_id='.$themeID), 'order'=>'id ASC', 'limit'=>'3','recursive'=>0));
		
		/*******************************************************************************/
        $this->set('code',$code);
        $this->set('theme',$theme);
        $this->set('domaine',$domaine);
        $this->set('codesage',$codesage);
        $this->set('dateDebut',$dateDebut);
        $this->set('dateFin',$dateFin);
        $this->set('heureDebut',$heureDebut);
        $this->set('heureFin',$heureFin);
        $this->set('lieu',$lieu);
        $this->set('duree',$duree);
        $this->set('objspecifiques',$objspecifiques);
        

		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));

		$Sessionformateurs = $this->Sessionformateur->find('list', array('list'=>array('id','formateur_id'), 'conditions'=>'Sessionformateur.sessionformation_id='.$sessionid));
		
		$this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
		
		 /*--------------------Responsable-----------------------------*/
	     $Sessionresponsables = $this->Sessionresponsable->find('list', array('list'=>array('id','responsable_id'), 'conditions'=>'Sessionresponsable.sessionformation_id ='.$sessionid));

	    $this->set('resp_nom', $this->Responsable->find('list', array('list'=>array('id','nom_resp'))));
	    
	    $this->set('resp_prenom', $this->Responsable->find('list', array('list'=>array('id','prenom_resp'))));
	    $this->set('resp_tel', $this->Responsable->find('list', array('list'=>array('id','telephone_resp'))));
	    
	    $this->set('resp_email', $this->Responsable->find('list', array('list'=>array('id','email_resp'))));
		/*-----------------------------------*/
		$this->layout = 'blank';
		
		$this->set('Agindemnitevaleurs',$Agindemnitevaleurs);
        $this->set('sessionformateurs',$Sessionformateurs);
        $this->set('sessionresponsables',$Sessionresponsables);

		$this->set('nomforma', $this->Formateur->find('list', array('list'=>array('id','nom'))));
		$this->set('prenomforma', $this->Formateur->find('list', array('list'=>array('id','prenom'))));
      
       
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



    public function fichecontact() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$sessionid = $this->getGetParam('sessionid');

		/****************SESSION DE FORMATION******************************/
        $data = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$sessionid), 'recursive'=>0));
		$themeID = $data[0]['Paramindemnite']['theme_id'];
		$codesage = $data[0]['Paramindemnite']['code_sage'];
		$dateDebut = $data[0]['Paramindemnite']['date_debut'];
		$dateFin = $data[0]['Paramindemnite']['date_fin'];
		$heureDebut = $data[0]['Paramindemnite']['heure_debut'];
		$heureFin = $data[0]['Paramindemnite']['heure_fin'];
		$duree = $data[0]['Paramindemnite']['duree'];
		$lieu = $data[0]['Paramindemnite']['lieu'];
		$soustraitantID = $data[0]['Paramindemnite']['soustraitant_id'];
		$bailleurID = $data[0]['Paramindemnite']['bailleur_id'];
		
	   /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule'];
		$thematiqueID = $thm[0]['Theme']['thematique_id'];

	    $thema = $this->Thematique->find('all', array('conditions'=>array('Thematique.id='.$thematiqueID), 'recursive'=>0));
		$domID = $thema[0]['Thematique']['domaine_id'];

		 $doma = $this->Domaine->find('all', array('conditions'=>array('Domaine.id='.$domID), 'recursive'=>0));
		$domaine = $doma[0]['Domaine']['domaine'];
		/**********************Agindemnitevaleur LIEU**********************************************
        $sal = $this->Salle->find('all', array('conditions'=>array('Salle.id='.$salleID), 'recursive'=>0));
		$lieu =$sal[0]['Salle']['localisation'];
		/****************Responsable***************************************************/
         $resp = $this->Sessionresponsable->find('all', array('conditions'=>array('Sessionresponsable.sessionformation_id='.$sessionid), 'recursive'=>0));
		$responsableID =  $resp[0]['Sessionresponsable']['responsable_id'];
		

	    $respon = $this->Responsable->find('all', array('conditions'=>array('Responsable.id='.$responsableID), 'recursive'=>0));
	    $resp_nom =  $respon[0]['Responsable']['nom_resp'];
	    $resp_prenom =  $respon[0]['Responsable']['prenom_resp'];
	    $resp_tel =  $respon[0]['Responsable']['telephone_resp'];
	    $resp_email =  $respon[0]['Responsable']['email_resp'];
		/*******************************************************************************/
        $this->set('theme',$theme);
        $this->set('domaine',$domaine);
        $this->set('codesage',$codesage);
        $this->set('dateDebut',$dateDebut);
        $this->set('dateFin',$dateFin);
        $this->set('heureDebut',$heureDebut);
        $this->set('heureFin',$heureFin);
        $this->set('lieu',$lieu);
        $this->set('resp_nom',$resp_nom);
        $this->set('resp_prenom',$resp_prenom);
        $this->set('resp_tel',$resp_tel);
        $this->set('resp_email',$resp_email);
        $this->set('duree',$duree);
        $this->set('soustraitantID',$soustraitantID);
        $this->set('bailleurID',$bailleurID);


		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));

		//$Sessionformateurs = $this->Sessionformateur->find('list', array('list'=>array('id','pa_matricule')), array('conditions'=>array('Sessionformateur.sessionformation_id='.$sessionid), 'recursive'=>0));

        $Sessionformateurs = $this->Sessionformateur->find('list', array('list'=>array('id','formateur_id'), 'conditions'=>'Sessionformateur.sessionformation_id='.$sessionid));
		
		
		$this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));

	    $this->set('nomforma', $this->Formateur->find('list', array('list'=>array('id','nom'))));
		$this->set('prenomforma', $this->Formateur->find('list', array('list'=>array('id','prenom'))));
		
		//$data = $this->Marche->find('all', array('conditions'=>array('Marche.Agindemnitevaleur_id='.$Agindemnitevaleurid), 'recursive'=>0,'limit'=>500));
		
		$this->layout = 'blank';
		
		$this->set('Agindemnitevaleurs',$Agindemnitevaleurs);
		$this->set('sessionformateurs',$Sessionformateurs);

        $this->set('bailleurs', $this->Bailleur->find('list', array('list'=>array('id','nom_bailleur'), 'order'=>'Bailleur.nom_bailleur ASC')));
		
		
		
       
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

    
    public function rapportsynthetique() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$sessionid = $this->getGetParam('sessionid');

		/****************SESSION DE FORMATION******************************/
        $data = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$sessionid), 'recursive'=>0));
		$themeID = $data[0]['Paramindemnite']['theme_id'];
		$codesage = $data[0]['Paramindemnite']['code_sage'];
		$dateDebut = $data[0]['Paramindemnite']['date_debut'];
		$dateFin = $data[0]['Paramindemnite']['date_fin'];
		$heureDebut = $data[0]['Paramindemnite']['heure_debut'];
		$heureFin = $data[0]['Paramindemnite']['heure_fin'];
		$duree = $data[0]['Paramindemnite']['duree'];
		$lieu = $data[0]['Paramindemnite']['lieu'];
		$soustraitantID = $data[0]['Paramindemnite']['soustraitant_id'];
		$bailleurID = $data[0]['Paramindemnite']['bailleur_id'];
		$clientID = $data[0]['Paramindemnite']['client_id'];
		//
	   /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule'];
		$ogjgen = $thm[0]['Theme']['objectif_gen'];
		$thematiqueID = $thm[0]['Theme']['thematique_id'];

	    $thema = $this->Thematique->find('all', array('conditions'=>array('Thematique.id='.$thematiqueID), 'recursive'=>0));
		$domID = $thema[0]['Thematique']['domaine_id'];

		 $doma = $this->Domaine->find('all', array('conditions'=>array('Domaine.id='.$domID), 'recursive'=>0));
		$domaine = $doma[0]['Domaine']['domaine'];
		/**********************Agindemnitevaleur Objspecifique***********************************/
       $objspecifiques = $this->Objspecifique->find('all', array('conditions'=>array('Objspecifique. 	theme_id='.$themeID), 'recursive'=>0));
        $this->set('objspecifiques',$objspecifiques);
		/****************Responsable***************************************************/
         $resp = $this->Sessionresponsable->find('all', array('conditions'=>array('Sessionresponsable.sessionformation_id='.$sessionid), 'recursive'=>0));
		$responsableID =  $resp[0]['Sessionresponsable']['responsable_id'];
		

	    $respon = $this->Responsable->find('all', array('conditions'=>array('Responsable.id='.$responsableID), 'recursive'=>0));
	    $resp_nom =  $respon[0]['Responsable']['nom_resp'];
	    $resp_prenom =  $respon[0]['Responsable']['prenom_resp'];
	    $resp_tel =  $respon[0]['Responsable']['telephone_resp'];
	    $resp_email =  $respon[0]['Responsable']['email_resp'];
		/*******************************************************************************/
        $this->set('theme',$theme);
        $this->set('domaine',$domaine);
        $this->set('codesage',$codesage);
        $this->set('dateDebut',$dateDebut);
        $this->set('dateFin',$dateFin);
        $this->set('heureDebut',$heureDebut);
        $this->set('heureFin',$heureFin);
        $this->set('lieu',$lieu);
        $this->set('resp_nom',$resp_nom);
        $this->set('resp_prenom',$resp_prenom);
        $this->set('resp_tel',$resp_tel);
        $this->set('resp_email',$resp_email);
        $this->set('duree',$duree);
        $this->set('soustraitantID',$soustraitantID);
        $this->set('bailleurID',$bailleurID);
        //ogjgen
        $this->set('ogjgen',$ogjgen);
       
        /*--------------------------------Session participant-----------------------------*/
		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));
        $nbrpart = count($Agindemnitevaleurs);

        $participantpresents = $this->Agindemnitevaleur->find('all', array('conditions'=>array("Agindemnitevaleur.sessionformation_id='{$sessionid}'","Agindemnitevaleur.presence='1'"), 'recursive'=>0));
        $partpresent = count($participantpresents);
		$this->set('nbrpart',$nbrpart);
        $this->set('partpresent',$partpresent);

        $this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    
        /*----------------------Formateur-----------------------------*/
        $Sessionformateurs = $this->Sessionformateur->find('list', array('list'=>array('id','formateur_id'), 'conditions'=>'Sessionformateur.sessionformation_id='.$sessionid));
		
		 $this->set('nomforma', $this->Formateur->find('list', array('list'=>array('id','nom'))));
		$this->set('prenomforma', $this->Formateur->find('list', array('list'=>array('id','prenom'))));
		
		$this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));

	     /*------------------------------CLient-----------------------------------------------*/
		  $this->set('client', $this->Client->find('list', array('list'=>array('id','clt_raison_social'))));
           $this->set('clientID',$clientID);
		  /*--------------------Evaluation à Chaud------------------------------------*/
		  $evaluations = $this->Evaluationachaud->find('all', array(
								'list'=>array('Paramindemnite.id',
									          'Paramindemnite.theme_id',
									          'Paramindemnite.date_debut',
									          'Paramindemnite.date_fin',
									          'Evaluationachaud.id',
									          'Evaluationachaud.sessionformation_id',
								              'Evaluationachaud.qualite_contenue',
								              'Evaluationachaud.pertinence_besoin_pro',
								              'Evaluationachaud.efficacite_methode',
								              'Evaluationachaud.qualite_prestation',
								              'Evaluationachaud.atteinte_objectif',
								              'Evaluationachaud.qualite_condition',
								             
								       
								          ),
								'fields'=>array('Paramindemnite.id',
									          'Paramindemnite.theme_id',
									          'Paramindemnite.date_debut',
									          'Paramindemnite.date_fin',
									          'Evaluationachaud.id',
									          'Evaluationachaud.sessionformation_id',
								              'Evaluationachaud.qualite_contenue',
								              'Evaluationachaud.pertinence_besoin_pro',
								              'Evaluationachaud.efficacite_methode',
								              'Evaluationachaud.qualite_prestation',
								              'Evaluationachaud.atteinte_objectif',
								              'Evaluationachaud.qualite_condition',
								             
								               ),
								'joins'=>array(
									array(
									'type' => 'LEFT',
									'alias' => 'Paramindemnite',
									'table' => 'sessionformations',
									'conditions' => array('Paramindemnite.id = Evaluationachaud. 	 	sessionformation_id'),
						
									),
								),
								'conditions'=>array(
									"Evaluationachaud.sessionformation_id = '".$sessionid."'"

							    ),
							    
							));
		   $this->set('evaluations',$evaluations);
		   /*---------------------------------------*/
		  // $this->set('comptabilites', $this->paginate('Comptabilite'));

		   $comptabilites = $this->Comptabilite->find('all');
		   $this->set('comptabilites',$comptabilites);
		   /*---------------------------------------*/

		$this->layout = 'blank';
		
		$this->set('Agindemnitevaleurs',$Agindemnitevaleurs);
		$this->set('sessionformateurs',$Sessionformateurs);

        $this->set('bailleurs', $this->Bailleur->find('list', array('list'=>array('id','nom_bailleur'), 'order'=>'Bailleur.nom_bailleur ASC')));
		
		
		
       
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



    public function fichelogistique() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$sessionid = $this->getGetParam('sessionid');

		/****************SESSION DE FORMATION******************************/
        $data = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$sessionid), 'recursive'=>0));
		$themeID = $data[0]['Paramindemnite']['theme_id'];
		$codesage = $data[0]['Paramindemnite']['code_sage'];
		$dateDebut = $data[0]['Paramindemnite']['date_debut'];
		$dateFin = $data[0]['Paramindemnite']['date_fin'];
		$heureDebut = $data[0]['Paramindemnite']['heure_debut'];
		$heureFin = $data[0]['Paramindemnite']['heure_fin'];
		$duree = $data[0]['Paramindemnite']['duree'];
		$lieu = $data[0]['Paramindemnite']['lieu'];
		$soustraitantID = $data[0]['Paramindemnite']['soustraitant_id'];
		$bailleurID = $data[0]['Paramindemnite']['bailleur_id'];
		$clientID = $data[0]['Paramindemnite']['client_id'];
		$programmeID = $data[0]['Paramindemnite']['programme_id'];
		
	   /**********DOMAINE THEMATIQUE THEME******************************/
        $thm = $this->Theme->find('all', array('conditions'=>array('Theme.id='.$themeID), 'recursive'=>0));
		$theme = $thm[0]['Theme']['intitule'];
		$thematiqueID = $thm[0]['Theme']['thematique_id'];

	    $thema = $this->Thematique->find('all', array('conditions'=>array('Thematique.id='.$thematiqueID), 'recursive'=>0));
		$domID = $thema[0]['Thematique']['domaine_id'];

		 $doma = $this->Domaine->find('all', array('conditions'=>array('Domaine.id='.$domID), 'recursive'=>0));
		$domaine = $doma[0]['Domaine']['domaine'];
		
        $this->set('theme',$theme);
        $this->set('domaine',$domaine);
        $this->set('codesage',$codesage);
        $this->set('dateDebut',$dateDebut);
        $this->set('dateFin',$dateFin);
        $this->set('heureDebut',$heureDebut);
        $this->set('heureFin',$heureFin);
        $this->set('lieu',$lieu);
        $this->set('duree',$duree);
        $this->set('soustraitantID',$soustraitantID);
        $this->set('bailleurID',$bailleurID);
        $this->set('programmeID',$programmeID);
        /*------------------------------CLient-----------------------------------------------*/
		$this->set('client', $this->Client->find('list', array('list'=>array('id','clt_raison_social'))));
        $this->set('clientID',$clientID);
        
        /***********************Agindemnitevaleur************************************/
		$Agindemnitevaleurs = $this->Agindemnitevaleur->find('all', array('conditions'=>array('Agindemnitevaleur.sessionformation_id='.$sessionid), 'recursive'=>0));
		 $nbrpart = count($Agindemnitevaleurs);

        $participantpresents = $this->Agindemnitevaleur->find('all', array('conditions'=>array("Agindemnitevaleur.sessionformation_id='{$sessionid}'","Agindemnitevaleur.presence='1'"), 'recursive'=>0));
        $partpresent = count($participantpresents);
		$this->set('nbrpart',$nbrpart);
        $this->set('partpresent',$partpresent);

		
        $Sessionformateurs = $this->Sessionformateur->find('list', array('list'=>array('id','formateur_id'), 'conditions'=>'Sessionformateur.sessionformation_id='.$sessionid));
        $nbrfor = count($Sessionformateurs);
		$this->set('nbrfor',$nbrfor);
		
        $Sessionresponsables = $this->Sessionresponsable->find('list', array('list'=>array('id','responsable_id'), 'conditions'=>'Sessionresponsable.sessionformation_id ='.$sessionid));
		/*---------------------Programme---------------------------------------*/
		$this->set('programmes', $this->Programme->find('list', array('list'=>array('id', 'code'), 'order'=>'Programme.code ASC')));
		/*----------------------Agdossier------------------------------------*/
		$this->set('matricule', $this->Agdossier->find('list', array('list'=>array('id','pa_matricule'))));
		$this->set('nom', $this->Agdossier->find('list', array('list'=>array('id','pa_nom'))));
		$this->set('prenom', $this->Agdossier->find('list', array('list'=>array('id','pa_prenom'))));
		$this->set('dir', $this->Agdossier->find('list', array('list'=>array('id','pa_direction_id'))));
		$this->set('direction', $this->Agdossier->find('list', array('list'=>array('id','pa_direction'))));
		$this->set('telephone', $this->Agdossier->find('list', array('list'=>array('id','pa_tel'))));
	    
	    $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
        /*----------------------Formateur----------------------------------------------*/
	    $this->set('nomforma', $this->Formateur->find('list', array('list'=>array('id','nom'))));
		$this->set('prenomforma', $this->Formateur->find('list', array('list'=>array('id','prenom'))));
		/*---------------------Responsable-------------------------------------*/
		$this->set('resp_nom', $this->Responsable->find('list', array('list'=>array('id','nom_resp'))));
	    
	    $this->set('resp_prenom', $this->Responsable->find('list', array('list'=>array('id','prenom_resp'))));
		
		
		$this->layout = 'blank';
		
		$this->set('Agindemnitevaleurs',$Agindemnitevaleurs);
		$this->set('sessionformateurs',$Sessionformateurs);
		$this->set('sessionresponsables',$Sessionresponsables);

        $this->set('bailleurs', $this->Bailleur->find('list', array('list'=>array('id','nom_bailleur'), 'order'=>'Bailleur.nom_bailleur ASC')));
		
		
		
       
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

    
	/******************************************************/
	public function etat___old() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       $postData = $this->postData();
        

        $matricule ='%';
        $nom ='%';
        $prenom ='%';
        $dateDebut ='%';
        $dateFin ='%';
        $theme ='%';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agindemnitevaleur']['submit'])){
			
			$matricule = $postData['Agindemnitevaleur']['matricule'];
			$nom = $postData['Agindemnitevaleur']['nom'];
			$prenom = $postData['Agindemnitevaleur']['prenom'];
			$theme = $postData['Agindemnitevaleur']['theme'];
			$dateDebut = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agindemnitevaleur']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	       /******************/
        $personnels = $this->Agindemnitevaleur->find('list', array(
								'list'=>array('Agindemnitevaleur.sessionformation_id',
								              'Agindemnitevaleur.indemnite_id',
								              'Agdossier.pa_matricule',
								              'Agdossier.pa_nom',
								              'Agdossier.pa_prenom',
								              'Paramindemnite.theme_id',
								              'Paramindemnite.date_debut',
								              'Paramindemnite.date_fin',
								             
								              
								          ),
								'fields'=>array('Agindemnitevaleur.sessionformation_id',
								              'Agindemnitevaleur.indemnite_id',
								              'Agdossier.pa_matricule',
								              'Agdossier.pa_nom',
								              'Agdossier.pa_prenom',
								              'Paramindemnite.theme_id',
								              'Paramindemnite.date_debut',
								              'Paramindemnite.date_fin',
								             
								               ),
								'joins'=>array(
									array(
									'type' => 'LEFT',
									'alias' => 'Paramindemnite',
									'table' => 'sessionformations',
									'conditions' => array('Paramindemnite.id = Agindemnitevaleur.sessionformation_id')
									),
									array(
									'type' => 'LEFT',
									'alias' => 'Agdossier',
									'table' => 'participants',
									'conditions' => array('Agdossier.id = Agindemnitevaleur.indemnite_id')
									),
									
								),
                                'conditions'=>array(
									"Paramindemnite.date_debut BETWEEN '".$dateDebut."' AND '".$dateFin."' OR
									  Agdossier.pa_matricule = '".$matricule."' OR
									  Agdossier.pa_nom = '".$nom."' OR 
									  Agdossier.pa_prenom  = '".$prenom."' OR
									  Paramindemnite.theme_id  = '".$theme."'"

							    ),
								/*'conditions'=>array(
									"Agdossier.pa_matricule LIKE '%{$matricule}%' AND 
									 Agdossier.pa_nom LIKE '%{$nom}%' AND 
									 Agdossier.pa_prenom  LIKE '%{$prenom}%' AND 
									 Paramindemnite.date_debut  BETWEEN '%{$dateDebut}%' AND '%{$dateFin}%'" 
							    ),*/
							    'order'=>'Agdossier.pa_nom DESC',
							));
        
           $count = count($personnels);
	       $this->set('personnels', $personnels);
           $this->set('count', $count);
		
       /* $this->set('js', 'tinymce/tinymce.min');*/
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Formations suivies sur une période';
	   $this->set('pageTitle', $w);
	   //$this->set('sessionformations',$Paramindemnites);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
      $this->set('themes', $this->Theme->find('list', array('list'=>array('id', 'intitule'), 'order'=>'Theme.intitule ASC')));
    }


    public function etat() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       $postData = $this->postData();
        

        $matricule ='%';
        $nom ='%';
        $prenom ='%';
        $dateDebut ='%';
        $dateFin ='%';
        $theme ='%';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agindemnitevaleur']['submit'])){
			
			$matricule = $postData['Agindemnitevaleur']['matricule'];
			$nom = $postData['Agindemnitevaleur']['nom'];
			$prenom = $postData['Agindemnitevaleur']['prenom'];
			$theme = $postData['Agindemnitevaleur']['theme'];
			$dateDebut = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agindemnitevaleur']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	       /******************/
	    $curDate = date("Y-m-d");
        $personnels = $this->Agindemnitevaleur->find('list', array(
								'list'=>array('Agindemnitevaleur.sessionformation_id',
								              'Agindemnitevaleur.indemnite_id',
								              'Agdossier.pa_matricule',
								              'Agdossier.pa_nom',
								              'Agdossier.pa_prenom',
								              'Paramindemnite.theme_id',
								              'Paramindemnite.date_debut',
								              'Paramindemnite.date_fin',
								             
								              
								          ),
								'fields'=>array('Agindemnitevaleur.sessionformation_id',
								              'Agindemnitevaleur.indemnite_id',
								              'Agdossier.pa_matricule',
								              'Agdossier.pa_nom',
								              'Agdossier.pa_prenom',
								              'Paramindemnite.theme_id',
								              'Paramindemnite.date_debut',
								              'Paramindemnite.date_fin',
								             
								               ),
								'joins'=>array(
									array(
									'type' => 'LEFT',
									'alias' => 'Paramindemnite',
									'table' => 'sessionformations',
									'conditions' => array('Paramindemnite.id = Agindemnitevaleur.sessionformation_id')
									),
									array(
									'type' => 'LEFT',
									'alias' => 'Agdossier',
									'table' => 'participants',
									'conditions' => array('Agdossier.id = Agindemnitevaleur.indemnite_id')
									),
									
								),
                              'conditions'=>array(
									"Paramindemnite.date_debut BETWEEN '".$dateDebut."' AND '".$dateFin."' OR
									  Agdossier.pa_matricule = '".$matricule."' OR
									  Agdossier.pa_nom = '".$nom."' OR 
									  Agdossier.pa_prenom  = '".$prenom."' OR
									  Paramindemnite.theme_id  = '".$theme."' AND
									  Paramindemnite.date_debut > '".$curDate."'"

							    ),
								/*'conditions'=>array(
									"Agdossier.pa_matricule LIKE '%{$matricule}%' AND 
									 Agdossier.pa_nom LIKE '%{$nom}%' AND 
									 Agdossier.pa_prenom  LIKE '%{$prenom}%' AND
									 Paramindemnite.theme_id  LIKE '%{$theme}%' AND  
									 Paramindemnite.date_debut  BETWEEN '%{$dateDebut}%' AND '%{$dateFin}%'" 
							    ),*/
							    'order'=>'Agdossier.pa_nom DESC',

							));
        
           $count = count($personnels);
	       $this->set('personnels', $personnels);
           $this->set('count', $count);
		
       /* $this->set('js', 'tinymce/tinymce.min');*/
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Formations suivies sur une période par agent';
	   $this->set('pageTitle', $w);
	   //$this->set('sessionformations',$Paramindemnites);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
      $this->set('matricule', $matricule);
      $this->set('nom', $nom);
      $this->set('prenom', $prenom);
      $this->set('theme', $theme);
      $this->set('themes', $this->Theme->find('list', array('list'=>array('id', 'intitule'), 'order'=>'Theme.intitule ASC')));
    }



    public function etat2() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       $postData = $this->postData();
        

        $matricule ='%';
        $nom ='%';
        $prenom ='%';
        $dateDebut ='%';
        $dateFin ='%';
        $theme ='%';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agindemnitevaleur']['submit'])){
			
			$matricule = $postData['Agindemnitevaleur']['matricule'];
			$nom = $postData['Agindemnitevaleur']['nom'];
			$prenom = $postData['Agindemnitevaleur']['prenom'];
			$theme = $postData['Agindemnitevaleur']['theme'];
			$dateDebut = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agindemnitevaleur']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	       /******************/
	    $curDate = date("Y-m-d");
        $personnels = $this->Agindemnitevaleur->find('list', array(
								'list'=>array('Agindemnitevaleur.sessionformation_id',
								              'Agindemnitevaleur.indemnite_id',
								              'Agdossier.pa_matricule',
								              'Agdossier.pa_nom',
								              'Agdossier.pa_prenom',
								              'Paramindemnite.theme_id',
								              'Paramindemnite.date_debut',
								              'Paramindemnite.date_fin',
								             
								              
								          ),
								'fields'=>array('Agindemnitevaleur.sessionformation_id',
								              'Agindemnitevaleur.indemnite_id',
								              'Agdossier.pa_matricule',
								              'Agdossier.pa_nom',
								              'Agdossier.pa_prenom',
								              'Paramindemnite.theme_id',
								              'Paramindemnite.date_debut',
								              'Paramindemnite.date_fin',
								             
								               ),
								'joins'=>array(
									array(
									'type' => 'LEFT',
									'alias' => 'Paramindemnite',
									'table' => 'sessionformations',
									'conditions' => array('Paramindemnite.id = Agindemnitevaleur.sessionformation_id')
									),
									array(
									'type' => 'LEFT',
									'alias' => 'Agdossier',
									'table' => 'participants',
									'conditions' => array('Agdossier.id = Agindemnitevaleur.indemnite_id')
									),
									
								),
                              'conditions'=>array(
									"Paramindemnite.date_debut BETWEEN '".$dateDebut."' AND '".$dateFin."' OR
									  Agdossier.pa_matricule = '".$matricule."' OR
									  Agdossier.pa_nom = '".$nom."' OR 
									  Agdossier.pa_prenom  = '".$prenom."' OR
									  Paramindemnite.theme_id  = '".$theme."' AND
									  Paramindemnite.date_debut > '".$curDate."'"

							    ),
								/*'conditions'=>array(
									"Agdossier.pa_matricule LIKE '%{$matricule}%' AND 
									 Agdossier.pa_nom LIKE '%{$nom}%' AND 
									 Agdossier.pa_prenom  LIKE '%{$prenom}%' AND
									 Paramindemnite.theme_id  LIKE '%{$theme}%' AND  
									 Paramindemnite.date_debut  BETWEEN '%{$dateDebut}%' AND '%{$dateFin}%'" 
							    ),*/
							    'order'=>'Agdossier.pa_nom DESC',

							));
        
           $count = count($personnels);
	       $this->set('personnels', $personnels);
           $this->set('count', $count);
		
       /* $this->set('js', 'tinymce/tinymce.min');*/
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Formations programmées sur une période par agent';
	   $this->set('pageTitle', $w);
	   //$this->set('sessionformations',$Paramindemnites);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
      $this->set('matricule', $matricule);
      $this->set('nom', $nom);
      $this->set('prenom', $prenom);
      $this->set('theme', $theme);
      $this->set('themes', $this->Theme->find('list', array('list'=>array('id', 'intitule'), 'order'=>'Theme.intitule ASC')));
      $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
		$this->set('services', $this->Service->find('list', array('list'=>array('id','nom_service'), 'order'=>'nom_service ASC')));
    }

    public function etat3() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       $postData = $this->postData();
        

        $direction ='%';
        $service ='%';
      
        $dateDebut ='%';
        $dateFin ='%';
        $theme ='%';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agindemnitevaleur']['submit'])){
			
			$direction = $postData['Agindemnitevaleur']['direction'];
			$service = $postData['Agindemnitevaleur']['service'];
		
			$theme = $postData['Agindemnitevaleur']['theme'];
			$dateDebut = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agindemnitevaleur']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	   
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Formations suivies par agents par direction sur une période';
	   $this->set('pageTitle', $w);
	   //$this->set('sessionformations',$Paramindemnites);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
      $this->set('direction', $direction);
      $this->set('service', $service);
    
      $this->set('theme', $theme);
      $this->set('themes', $this->Theme->find('list', array('list'=>array('id', 'intitule'), 'order'=>'Theme.intitule ASC')));
      $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
		$this->set('services', $this->Service->find('list', array('list'=>array('id','nom_service'), 'order'=>'nom_service ASC')));
    }

    public function etat4() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       $postData = $this->postData();
        

        $direction ='%';
        $service ='%';
      
        $dateDebut ='%';
        $dateFin ='%';
        $theme ='%';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agindemnitevaleur']['submit'])){
			
			$direction = $postData['Agindemnitevaleur']['direction'];
			$service = $postData['Agindemnitevaleur']['service'];
		
			$theme = $postData['Agindemnitevaleur']['theme'];
			$dateDebut = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agindemnitevaleur']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	   
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Formations programmées sur une période par direction';
	   $this->set('pageTitle', $w);
	   //$this->set('sessionformations',$Paramindemnites);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
      $this->set('direction', $direction);
      $this->set('service', $service);
    
      $this->set('theme', $theme);
      $this->set('themes', $this->Theme->find('list', array('list'=>array('id', 'intitule'), 'order'=>'Theme.intitule ASC')));
      $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
		$this->set('services', $this->Service->find('list', array('list'=>array('id','nom_service'), 'order'=>'nom_service ASC')));
    }

    public function etat5() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       $postData = $this->postData();
        

        $direction ='%';
        $service ='%';
      
        $dateDebut ='%';
        $dateFin ='%';
        $theme ='%';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agindemnitevaleur']['submit'])){
			
			$direction = $postData['Agindemnitevaleur']['direction'];
			$service = $postData['Agindemnitevaleur']['service'];
		
			$theme = $postData['Agindemnitevaleur']['theme'];
			$dateDebut = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agindemnitevaleur']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	   
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Formations programmées sur une période par direction';
	   $this->set('pageTitle', $w);
	   //$this->set('sessionformations',$Paramindemnites);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
      $this->set('direction', $direction);
      $this->set('service', $service);
    
      $this->set('theme', $theme);
      $this->set('themes', $this->Theme->find('list', array('list'=>array('id', 'intitule'), 'order'=>'Theme.intitule ASC')));
      $this->set('directions', $this->Direction->find('list', array('list'=>array('id','nom_direction'), 'order'=>'nom_direction ASC')));
		$this->set('services', $this->Service->find('list', array('list'=>array('id','nom_service'), 'order'=>'nom_service ASC')));
    }


    public function etat6() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agindemnitevaleurs'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();
        

        
        $dateDebut ='%';
        $dateFin ='%';
        //$theme ='%';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agindemnitevaleur']['submit'])){
			
			
			//$theme = $postData['Agindemnitevaleur']['theme'];
			$dateDebut = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agindemnitevaleur']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agindemnitevaleur']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	    
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Etat des évaluations Post-Formation';
	   $this->set('pageTitle', $w);

	    /**********************CRITERE********************************************/
        $var = $this->Critere->find('all');
		$critere = $var[0]['Critere']['critere'];
		$this->set('critere',$critere);
	   //$this->set('sessionformations',$Paramindemnites);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
     
      //$this->set('theme', $theme);
      $this->set('themes', $this->Theme->find('list', array('list'=>array('id', 'intitule'), 'order'=>'Theme.intitule ASC')));
    }

    




}
?>