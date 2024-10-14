<?php 
/**
 * 
 * 
 * 
 */

class AffcongesController extends AppController{	
	var $paginate = array(
		'Affconge'=>array(
			'model'=>'Affconge','sort'=>'id', 'affconge'=>'ASC',
			'page'=>1, 'recursive'=>0, 'limit'=>18
		),

	
	);	
	
	var $uses = array('User','Paramclassification','Agcontrat','Agavencement','Paramsociopro','Formation','Formparticipant','Signataire','Agdossier','Rembulletin','Rembulitem','Parambanque','Parammodepaie','Paramdirection','Paramfonction','Agavencement','Paramclassification','Paramechelon','Signataire','Agaffectmutation');
	
	
	
	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        
        /********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ALLOCATION CONGE <span class="pageTitle">'.$name . SEP . $username.'</span>');
		$this->set('affconges', $this->paginate('Affconge'));
		
		$this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','qualification'), 'order'=>'id ASC')));

		$this->set('matrimoniales', array('1'=>'Célibataire','2'=>'Marié(e)','3'=>'Divorcé(e)','4'=>'Veuf/Veuve'));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

	    
	    
	}


	public function index2() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Affconge->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Affconge']['date_debut']))?$tmp[0]['Affconge']['date_debut']:'2022-01-01';
		$datefin = (isset($tmp[0]['Affconge']['date_fin']))?$tmp[0]['Affconge']['date_fin']:'2022-12-31'; 
        /******************************************************/
		if(isset($postData['Affconge']['valider'])){
			$datedebut = $postData['Affconge']['datedebut'];
			$datefin = $postData['Affconge']['datefin'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT GENERAL DES ALLOCATIONS DE CONGE<span class="pageTitle">'.$name . SEP . $username.'</span>');
	
       // $this->paginate['Affconge']['conditions'][] = array('Affconge.date_debut'=>$datedebut);
        //$this->paginate['Affconge']['conditions'][] = array('Affconge.date_fin'=>$datefin);
        $this->set('affconges', $this->paginate('Affconge'));
		
        #$this->paginate['Affconge']['conditions'][] = array('Affconge.paramtypesalaire_id'=>$typesal);
        
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
	}
	


	public function edit() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
		$postData = $this->postData();
		if(isset($postData['Affconge']['submit']) && isset($postData['Affconge'])){
            if($postData['Affconge']['agdossier_id']<>'' &&
			   $postData['Affconge']['date_debut']<>'' &&
			   $postData['Affconge']['date_fin']<>'' &&
			   $postData['Affconge']['periode_debut']<>'' &&
			   $postData['Affconge']['periode_fin']<>'')
            {
					/*********************************************************************/
					$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Affconge ' . 'id: ';
					if($accessLevel['view'] && $accessLevel['edit']){
					    /*--------------------------------*/
                        $dossier = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$postData['Affconge']['agdossier_id']), 'recursive'=>0));
                        
			            $matricule = $dossier[0]['Agdossier']['ag_matricule'];
			            /*=====================*/
	                    $postData['Affconge']['matricule']  = $matricule;	
	                    /*========================*/
	                    $datediff = strtotime($postData['Affconge']['date_fin']) - strtotime($postData['Affconge']['date_debut']);

						$duree = round($datediff / (60 * 60 * 24)) + 1;

                       $postData['Affconge']['duree']  = $duree;	
                     
					    /*+++++++++++Recuperation de tout les bulletins++++*/
					    $list_bull = array();

					    $salaire_brute = 0;
					    $somme_retenue = 0;

	     $alpha = $this->Rembulletin->find('all', array('conditions'=>array(
	     	   "Rembulletin.ag_dossier='".$postData['Affconge']['agdossier_id']."'",
	     	   "Rembulletin.date_debut between '".$postData['Affconge']['periode_debut']."' and '".$postData['Affconge']['periode_fin']."'"), 'order'=>'Rembulletin.id DESC', 'recursive'=>0));


			            $count_alpha = count($alpha);
				        for($i=0;$i<$count_alpha;$i++)
				        {
				           $list_bull[] = $alpha[$i]['Rembulletin']['id'];
				          
				        }
				        $count_bull = count($list_bull);

				        
				       
				        if($count_bull == 14)
				        {
                            /*+++++++++++Somme des avoirs de tout les bulletins++++*/
	                        
	                        for($j=0;$j<$count_bull;$j++)
					        {
					           $beta = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id = '$list_bull[$j]'","Rembulitem.code != 500","Rembulitem.avoir_ret = 1"), 'recursive'=>0));
				               $cpt = count($beta);
				               $salbrut = 0;
				               for($a=0;$a<$cpt;$a++)
				               {
		                              $salbrut = $salbrut + $beta[$a]['Rembulitem']['montant'];
				               } 
				               $salaire_brute = $salaire_brute + $salbrut;
					        }
					        $postData['Affconge']['sal_brut']  = $salaire_brute;
					        /*+++++++++++Somme des retenues de tout les bulletins++++*/
					        
					        for($k=0;$k<$count_bull;$k++)
					        {
					           $sigma = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id = '$list_bull[$k]'","Rembulitem.avoir_ret = '2'","Rembulitem.code IN (10,23,29,98,401,402,403,1008)","Rembulitem.montant > 0"), 'recursive'=>0));
				               $count_ret = count($sigma);
				               $retenue = 0;
				               for($b=0;$b<$count_ret;$b++)
				               {
		                              $retenue = $retenue + $sigma[$b]['Rembulitem']['montant'];
				               }
				               $somme_retenue =  $somme_retenue + $retenue;
					        }
					        $postData['Affconge']['retenue']  = $somme_retenue;
	                        /*===============================================*/
				        }
				        else
				        {
                           $this->Session->setFlash('Tout les bulletins de salaire de la période d\'allocation des congés ne sont pas générer');
				        }
                       
					   /* $data = $this->Rembulletin->find('all', array('conditions'=>array('Rembulletin.ag_dossier='.$postData['Affconge']['agdossier_id']), 'order'=>'Rembulletin.id DESC', 'recursive'=>0));

		               $bull = $data[0]['Rembulletin']['id'];

		                /*============*/
		              /* $postData['Affconge']['bull']  = $bull;
                       
                       $beta = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id = '$bull'","Rembulitem.code != 500","Rembulitem.avoir_ret = 1"), 'recursive'=>0));
		               $count = count($beta);
		               $salbrut = 0;
		               for($i=0;$i<$count;$i++)
		               {
                              $salbrut = $salbrut + $beta[$i]['Rembulitem']['montant'];
		               }
		               $postData['Affconge']['sal_brut']  = $salbrut;
                       /*============*/
		               /*$sigma = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id = '$bull'","Rembulitem.avoir_ret = '2'","Rembulitem.code IN (10,23,29,98,401,402,403,1008)","Rembulitem.montant > 0"), 'recursive'=>0));
		               $count_ret = count($sigma);
		               $retenue = 0;
		               for($j=0;$j<$count_ret;$j++)
		               {
                              $retenue = $retenue + $sigma[$j]['Rembulitem']['montant'];
		               }

		               $postData['Affconge']['retenue']  = $retenue;
                       /*===============================================*/
                       //$salaire_moyen = $salbrut - $retenue;

                       $salaire_moyen = ($salaire_brute - $somme_retenue) / 12;

                       $sal_journalier = $salaire_moyen / 30;

                       $montant_alloc = round($sal_journalier * $duree);

                        $postData['Affconge']['montant_alloc']  = $montant_alloc;	
                        
                        $saveId = false;
					    /*--------------------------------*/
					    if($count_bull == 14)
				        {				
							$saveId = $this->Affconge->save($postData);
							$saveId = true;
						}
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
								$this->redirect(array('controller'=>'Affconges', 'view'=>'index'));
							}else{
								$this->redirect(array('controller'=>'Affconges', 'view'=>'index'));
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
				$this->data = $this->Affconge->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affconges', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affconges', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION ALLOCATION CONGE':'MODIFICATION ALLOCATION CONGE'));
		$this->set('toolbar', $toolbar);

		$this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','qualification'), 'order'=>'id ASC')));

		//$this->set('bull',$bull);

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));



	}



	public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affconges', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Affconges', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE ALLOCATION CONGE'));
		$this->set('toolbar', $toolbar);
	}
	
	
	public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));

		$ids = explode('|', (string)$this->getGetParam('id'));
      

		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Affconge->find('all', array('conditions'=>array(array($this->Affconge->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Affconges';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Affconge']['id'];
				
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Affconge->delete($ids);
			
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect($this->Session->read('return'));
		}else{
			$this->redirect(array('controller'=>'Affconges', 'view'=>'index'));
		}
	}


	public function index3() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        
        /********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'DOSSIERS AGENTS <span class="pageTitle">'.$name . SEP . $username.'</span>');
		$this->set('affconges', $this->paginate('Affconge'));
		
		$this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','qualification'), 'order'=>'id ASC')));

		$this->set('matrimoniales', array('1'=>'Célibataire','2'=>'Marié(e)','3'=>'Divorcé(e)','4'=>'Veuf/Veuve'));
	    
	    
	}


	/*--------------------------------------*/
    public function bulletin() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		$ret_pret = 0;
		$cegeci = 0;
		$pharmacie = 0;
		$precompte = 0;
		$salImp = 0;
		$congeid = $this->getGetParam('congeid');

		/****************BULLETIN******************************/
        $data = $this->Affconge->find('all', array('conditions'=>array('Affconge.id='.$congeid), 'recursive'=>0));
		$agdossier_id = $data[0]['Affconge']['agdossier_id'];
		$matricule = $data[0]['Affconge']['matricule'];
		$date_fin = $data[0]['Affconge']['date_fin'];
		$date_debut  = $data[0]['Affconge']['date_debut'];
		$duree = $data[0]['Affconge']['duree'];
		$sal_brut = $data[0]['Affconge']['sal_brut'];
		$retenue = $data[0]['Affconge']['retenue'];
		$montant_alloc = $data[0]['Affconge']['montant_alloc'];
		$destination = $data[0]['Affconge']['destination'];
		
	    $this->set('agdossier_id', $agdossier_id);
	    $this->set('matricule', $matricule);
	    $this->set('date_debut', $date_debut);
	    $this->set('date_fin', $date_fin);
	    $this->set('duree', $duree);
	    $this->set('sal_brut', $sal_brut);
	    $this->set('retenue', $retenue);
	    $this->set('montant_alloc', $montant_alloc);
	    $this->set('destination', $destination);

	     /****************Contrat******************************/
        $cont = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$agdossier_id), 'recursive'=>0));
        
        $agcontrat_id = $cont[0]['Agcontrat']['id'];
        $num_contrat = $cont[0]['Agcontrat']['num_contrat'];
        $num_cotisation = $cont[0]['Agcontrat']['num_cotisation'];
        $modepaie_id = $cont[0]['Agcontrat']['parammodepaie_id'];
        $banque_id = $cont[0]['Agcontrat']['parambanque_id'];
        $num_comptebanq = $cont[0]['Agcontrat']['num_comptebanq'];
		$cotisation = $cont[0]['Agcontrat']['paramstructurecotsocial_id'];

		$this->set('agcontrat_id', $agcontrat_id);
	    $this->set('num_contrat', $num_contrat);
        $this->set('num_cotisation', $num_cotisation);
        $this->set('modepaieid', $modepaie_id);
        $this->set('banqueid', $banque_id);
        $this->set('comptebanq', $num_comptebanq);
        $this->set('cotisation', $cotisation);
       
	   
	    $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
       $this->set('charge',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
       

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

	
		/****************Affectation******************************/
        $aff = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$agcontrat_id), 'recursive'=>0));

        $fonction_id = $aff[0]['Agaffectmutation']['paramfonction_id'];
		$direction_id = $aff[0]['Agaffectmutation']['paramdirection_id'];

        $this->set('fonctionid', $fonction_id);
	    $this->set('directionid', $direction_id);

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));
		
       /****************Avancement******************************/
        $av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$agcontrat_id), 'recursive'=>0));

        $classification_id = $av[0]['Agavencement']['paramclassification_id'];
		$paramechelon_id = $av[0]['Agavencement']['paramechelon_id'];

		$this->set('classificationid', $classification_id);
	    $this->set('paramechelonid', $paramechelon_id);

	   $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

       /****************Signataire DAFC******************************/
       $sign = $this->Signataire->find('all', array('conditions'=>array("Signataire.signature='18'","Signataire.statut='1'"), 'recursive'=>0));

        $signature = $sign[0]['Signataire']['agdossier_id'];
        $this->set('signature', $signature);
		/*$this->set('style', '
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
			
		');**/
			
	}

	
	/////////////////////////////////////////////////////////////////////////////////////////
	
	
	public function etatconge() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Affconges'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		

      
   
		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
       
        
        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        

		/****************Signataire DG******************************/
        $signat = $this->Signataire->find('all', array('conditions'=>array("Signataire.signature='28'","Signataire.statut='1'"), 'recursive'=>0));

        $sign_dg = $signat[0]['Signataire']['agdossier_id'];
        $distinct_dg = $signat[0]['Signataire']['distinction'];
        $this->set('sign_dg', $sign_dg);
        $this->set('distinct_dg', $distinct_dg);

        /****************Signataire DAFC******************************/
        $sign = $this->Signataire->find('all', array('conditions'=>array("Signataire.signature='18'","Signataire.statut='1'"), 'recursive'=>0));

        $sign_dafc = $sign[0]['Signataire']['agdossier_id'];
        $distinct_dafc = $sign[0]['Signataire']['distinction'];
        $this->set('sign_dafc', $sign_dafc);
        $this->set('distinct_dafc', $distinct_dafc);

		$this->set('style', '
			.breakAfter{
				page-break-after: always;
				
			}

			
			
		');
			
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	
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