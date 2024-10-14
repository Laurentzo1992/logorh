<?php 
/**
 * 
 * 
 * 
 */

class AgaffectmutationsController extends AppController{	
	var $paginate = array(
		'Agaffectmutation'=>array(
			'model'=>'Agaffectmutation','sort'=>'id', 'agaffectmutation'=>'ASC',
			'page'=>1, 'recursive'=>0, 'limit'=>18
		),
		
	
	);	
	
	var $uses = array('User','Agdossier','Paramclassification','Paramechelon','Paramnatcontrat','Agcontrat','Paramstructurecotsocial','Paramservice','Paramdirection','Parambanque','Parammodepaie','Paramfonction','Paramstatactivation','Paramtypemvt','Paramtypefonction','Paramtypemvt');
	
	
	
	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agaffectmutations'));
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
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index2', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$bar['Retour'] = array(
				'url'=>array('controller'=>'Agcontrats', 'view'=>'index2', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		 $this->set('toolbar', $bar);

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'AFFECTATIONS / MUTATIONS <span class="pageTitle">'.$name . SEP . $username.'</span>');

		$this->paginate['Agaffectmutation']['conditions'][] = array('Agaffectmutation.agcontrat_id'=>$contrat);
		$this->set('agaffectmutations', $this->paginate('Agaffectmutation'));
		
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

        $this->set('paramtypemvts', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
        
        

	}
	
	public function edit() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agaffectmutations'));
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
        /********************************************************************/

		$postData = $this->postData();
		if(isset($postData['Agaffectmutation']['submit']) && isset($postData['Agaffectmutation'])){
            if($postData['Agaffectmutation']['agcontrat_id']<>'' &&
			   $postData['Agaffectmutation']['paramfonction_id']<>'' &&
			   $postData['Agaffectmutation']['paramdirection_id']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Agaffectmutation ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){
					/*-----------------------------------*/
                    $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Agaffectmutation']['agdossier_id']), 'recursive'=>0));
		            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
                    /*-----------------------------------*/	
                   // print_r($matricule);
                   $postData['Agaffectmutation']['matricule']  = $matricule;			
					$saveId = $this->Agaffectmutation->save($postData);
						
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
					$this->redirect(array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
							}else{
								$this->redirect(array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
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
				$this->data = $this->Agaffectmutation->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION AFFECTATION/MUTATION':'MODIFICATION AFFECTATION/MUTATION'));
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

        $this->set('paramtypemvts', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
	}

    
	public function edit2() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agaffectmutations'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
         $tmp = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.id='.$id), 'recursive'=>0));
		$varcontrat = $tmp[0]['Agaffectmutation']['agcontrat_id'];
		$vardossier = $tmp[0]['Agaffectmutation']['agdossier_id'];
		$varmat = $tmp[0]['Agaffectmutation']['matricule'];
		/*---------------------*/
		$postData = $this->postData();
		//////////////////////////UPLOAD DECISION///////////////////////////
        if(isset($postData['Agaffectmutation'])){
		    $error = false;
			if(isset($_FILES['fichier']['tmp_name']) && is_uploaded_file($_FILES['fichier']['tmp_name'])){
				$newName = $_FILES['fichier']['name'];
				if(move_uploaded_file($_FILES['fichier']['tmp_name'], 'fichier_numeriques/decisions/' . $newName)){
					 $postData['Agaffectmutation']['fichier'] = $newName;
				}
				else
				{
				    $error = true;
				}
		   }
		
		}
		/////////////////////////////////////////////////////////////////////
		if(isset($postData['Agaffectmutation']['submit']) && isset($postData['Agaffectmutation'])){
            if($postData['Agaffectmutation']['agcontrat_id']<>'' &&
			   $postData['Agaffectmutation']['paramfonction_id']<>'' &&
			   $postData['Agaffectmutation']['paramdirection_id']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Agaffectmutation ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){
					/*-----------------------------------*/
                    $tmp = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Agaffectmutation']['agdossier_id']), 'recursive'=>0));
		            $matricule = $tmp[0]['Agdossier']['ag_matricule'];
                    /*-----------------------------------*/	
                   // print_r($matricule);
                    $postData['Agaffectmutation']['matricule']  = $matricule;			
					$saveId = $this->Agaffectmutation->save($postData);
						
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
					$this->redirect(array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
							}else{
								$this->redirect(array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
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
				$this->data = $this->Agaffectmutation->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION AFFECTATION/MUTATION':'MODIFICATION AFFECTATION/MUTATION'));
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
        $this->set('paramtypemvts', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));
	}



	public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agaffectmutations'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Agaffectmutations', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE AFFECTATION / MUTATION'));
		$this->set('toolbar', $toolbar);
	}
	
	
	public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Agaffectmutations'));

		$ids = explode('|', (string)$this->getGetParam('id'));
      

		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Agaffectmutation->find('all', array('conditions'=>array(array($this->Agaffectmutation->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Agaffectmutations';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Agaffectmutation']['id'];
				
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Agaffectmutation->delete($ids);
			
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect($this->Session->read('return'));
		}else{
			$this->redirect(array('controller'=>'Agaffectmutations', 'view'=>'index'));
		}
	}

    
    public function decision() {

		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Agaffectmutations'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$affectmutid = $this->getGetParam('affectmutid');

		/****************SESSION DE FORMATION******************************/
        $data = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.id='.$affectmutid), 'recursive'=>0));
		$dossierID = $data[0]['Agaffectmutation']['agdossier_id'];
		$matricule = $data[0]['Agaffectmutation']['matricule'];
		$date = $data[0]['Agaffectmutation']['date_affectmut'];
		$fonction = $data[0]['Agaffectmutation']['paramfonction_id'];
		$typefct = $data[0]['Agaffectmutation']['paramtypefonction_id'];
		$service = $data[0]['Agaffectmutation']['paramservice_id'];
		$direction = $data[0]['Agaffectmutation']['paramdirection_id'];
		$typemvt = $data[0]['Agaffectmutation']['paramtypemvt_id'];
		
		
		
        $this->set('dossierID',$dossierID);
        $this->set('matricule',$matricule);
        $this->set('date',$date);
        $this->set('fonction',$fonction);
        $this->set('typefct',$typefct);
        $this->set('service',$service);
        $this->set('direction',$direction);
        $this->set('typemvt',$typemvt);
       
        $this->set('services', $this->Paramservice->find('list', array('list'=>array('id','nom_service'), 'order'=>'id ASC')));

		$this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','nom_direction'), 'order'=>'id ASC')));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        //agcontrats
        $this->set('agcontrats', $this->Agcontrat->find('list', array('list'=>array('id','num_contrat'), 'order'=>'id ASC')));

        $this->set('nivrespons', $this->Paramtypefonction->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('paramtypemvts', $this->Paramtypemvt->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

       $this->layout = 'blank';	
		$this->set('style', '
			.breakAfter{
				page-break-after: always;
				
			}

			.cannevas{
				padding-top:10px;
				width:680px;	
				margin: 0 auto;
				color: #4c4c4c;
			}
			body{ 
			    font-size: 14px;
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

	
    public function etat() {
		$this->requestAction('Users' ,'loggedIn');				
		include_once '../boot/params.php';			
		$accessLevel = $this->requestAction('Users' ,'access', array('Agaffectmutations'));
		if($accessLevel['etat']){
			$this->set('accessLevel', $accessLevel);
		}
       $postData = $this->postData();
        

       
        $dateDebut ='%';
        $dateFin ='%';
        
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Agaffectmutation']['submit'])){
			
			$dateDebut = date("Y-m-d",strtotime($postData['Agaffectmutation']['dateDebut']));
			$dateFin = date("Y-m-d",strtotime($postData['Agaffectmutation']['dateFin']));
			$this->data = $postData;

		}
		if(isset($postData['Agaffectmutation']['reinit']))$postData = array();
	     $this->data[0]=$postData;


	       /******************/
	    $curDate = date("Y-m-d");
       
       /* $this->set('js', 'tinymce/tinymce.min');*/
		$this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));
		
		
       $w = 'Offres de Agaffectmutations';
	   $this->set('pageTitle', $w);
	   //$this->set('sessionAgaffectmutations',$SessionAgaffectmutations);
      $this->set('dateDebut', $dateDebut);
      $this->set('dateFin', $dateFin);
     
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