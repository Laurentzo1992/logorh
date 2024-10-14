<?php 
/**
 * 
 * 
 * 
 */

class RembulletinsController extends AppController{	
	var $paginate = array(
		'Rembulletin'=>array(
			'model'=>'Rembulletin','sort'=>'num_bull', 'rembulletin'=>'DESC',
			'page'=>1, 'recursive'=>0, 'limit'=>15
		),
		
	
	);	
	
   var $uses = array('User','Agdossier','Paramclassification','Paramechelon','Paramnatcontrat','Agcontrat','Paramstructurecotsocial','Paramservice','Paramdirection','Parambanque','Parammodepaie','Paramfonction','Paramstatactivation','Paramtypemvt','Paramtypefonction','Rembulitem','Agindemnite','Paramindemnite','Agaffectmutation','Agavencement','Paramdirection','Paramfonction','Paramgrillesal','Paramindemitem','Paramtypesalaire','Afftraite','Affbonpharma','Paramtauxprimebilan','Affbontraite','Paramsalbasegrat','Parambanque','Signataire');
	
	
	
	public function index() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01'; //$tmp[0]['Rembulletin']['date_debut'];
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; //$tmp[0]['Rembulletin']['date_fin'];
		$typesal = (isset($tmp[0]['Rembulletin']['paramtypesalaire_id']))?$tmp[0]['Rembulletin']['paramtypesalaire_id']:'1'; //$tmp[0]['Rembulletin']['paramtypesalaire_id'];
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'BULLETINS GROUPES<span class="pageTitle">'.$name . SEP . $username.'</span>');
	   $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
       $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
       $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
       $this->set('rembulletins', $this->paginate('Rembulletin'));

		$this->set('datedebut',$datedebut);
        $this->set('datefin',$datefin);
        $this->set('typesal',$typesal);

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));
		
	}
	
    
	public function edit() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		

		$postData = $this->postData();
		if(isset($postData['Rembulletin']['submit']) && isset($postData['Rembulletin'])){
            if($postData['Rembulletin']['date_debut']<>'' &&
			   $postData['Rembulletin']['date_fin']<>'' &&
			   $postData['Rembulletin']['paramtypesalaire_id']<>'')
            {
				/*********************************************************************/
				$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Rembulletin ' . 'id: ';
				if($accessLevel['view'] && $accessLevel['edit']){

			    $type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];
				if($type_bulletin == 1)
				{
				    /*++++++++++++++++BULLETIN NORMALE+++++++++++++++++++++++++++++++*/
	                //$contrats = $this->Agcontrat->find('all', array('order'=>'id ASC'));
	                $contrats = $this->Agcontrat->find('all', array('conditions'=>array("Agcontrat.statut='1'"), 'recursive'=>0));
	                $i = 0;
	                foreach($contrats as $index => $contrat)
	                {
	                	$hp = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.agcontrat_id='{$contrat['Agcontrat']['id']}'","Rembulletin.paramtypesalaire_id='{$postData['Rembulletin']['paramtypesalaire_id']}'","Rembulletin.date_debut ='{$postData['Rembulletin']['date_debut']}'","Rembulletin.date_fin='{$postData['Rembulletin']['date_fin']}'"), 'recursive'=>0));
			            $nbr_bul = count($hp);
	                    if($nbr_bul > 0)
	                    {
	                      /////****************/
	                    }
	                    else
	                    {
		                	$i++;
		                	/*-------------------------------------------*/
		                	$count = 0;
		                	$bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
				            $count = count($bulletins);

				            if($count > 0){$count = $count + 1;}else{$count = 1;}
		                	/*-------------------------------------------*/
		                	$agent = array('Rembulletin'=>array(
						        'num_bull' => $count,
						        'date_debut' => $postData['Rembulletin']['date_debut'],
						        'date_fin' => $postData['Rembulletin']['date_fin'],
							    'agcontrat_id' => $contrat['Agcontrat']['id'],
							    'num_contrat' => $contrat['Agcontrat']['num_contrat'],
							    'ag_dossier' => $contrat['Agcontrat']['agdossier_id'],
							    'matricule' => $contrat['Agcontrat']['matricule'],
							    'paramtypesalaire_id' => $postData['Rembulletin']['paramtypesalaire_id'],
							   
								   )
					            );
							    $this->Rembulletin->save($agent);
							    $bulID = $this->Rembulletin->id;
							    $periode_fin_bul = $postData['Rembulletin']['date_fin'];
		                        /*-----------------Contrat de l'agent-----------------------------------*/
		                        $cont = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $date_embauche = $cont[0]['Agcontrat']['date_debut'];
		                        $agdossier = $cont[0]['Agcontrat']['agdossier_id'];
		                        /*-----------------Indemnites/Avantages/Retenues de l'agent------------*/
		                        $indemnites = $this->Agindemnite->find('all', array('conditions'=>array('Agindemnite.agcontrat_id='.$contrat['Agcontrat']['id'])));
		                        
		                        $ind = $this->Paramindemnite->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC'));
		                        /*---------------Affectation ou Mutation de l'agent-------------------*/
		                         $affec = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $typefonction = $affec[0]['Agaffectmutation']['paramtypefonction_id'];
		                        /*-----------------------------------------------*/
		                        $base = 0;
		                        $montant = 0;
		                        foreach ($indemnites as $indemnite)
		                        {
		                        	
		                        	$code_paramind =  $indemnite['Agindemnite']['code_paramind'];
		                        	
		                        	$ind = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.code ='.$code_paramind), 'recursive'=>0));
		                        	$identifiant = $ind[0]['Paramindemnite']['id'];
				                    $code = $ind[0]['Paramindemnite']['code'];
				                    $libelle = $ind[0]['Paramindemnite']['libelle'];
		                            $avoiret = $ind[0]['Paramindemnite']['paramavoiret_id'];
		                            //$type = $ind[0]['Paramindemnite']['paramtypefonction_id'];
		                            /*-----------AVANCEMENT-----------------------------------*/
		                            $av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                            $classification_id = $av[0]['Agavencement']['paramclassification_id'];
				                    $paramechelon_id = $av[0]['Agavencement']['paramechelon_id'];
				                    $year_anc = $av[0]['Agavencement']['anciennete'];

				                    /*---------traitement des differents éléments-------------*/
                                    
                                    /*---------TRAITEMENT DE BASE------------------------------*/
		                            if($code == 1)
		                            {
                                        $green = $this->Paramgrillesal->find('all', array('conditions'=>array("Paramgrillesal.paramclassification_id='{$classification_id}'","Paramgrillesal.paramechelon_id='{$paramechelon_id}'"), 'recursive'=>0));
		                                  
		                                $base = $green[0]['Paramgrillesal']['salaire'];
		                                
		                                $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$base,
										    'taux' =>30,
										    'montant' => $base,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
							           
		                            }
		                           
		                            /*******************INDEMNITE FONCTION*************************/
		                            if(($code == 3 && $typefonction == 1) ||
		                        	   ($code == 3 && $typefonction == 2) ||
		                               ($code == 3 && $typefonction == 3))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE LOGEMENT*************************/
		                            if($code == 4)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE TRANSPORT*************************/
		                            if($code == 5)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE SUJETION*************************/
		                            if($code == 6)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE CAISSE*************************/
		                            if(($code == 7 && $typefonction == 5) ||
		                        	   ($code == 7 && $typefonction == 7))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************ALLOCATION FAMILLIALE*************************/
		                            if($code == 8)
		                            {
		                            	$alloc = 0;
		                            	
		                            	$montant =  $indemnite['Agindemnite']['base_montant'];

		                                $pegaz = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $pegaz[0]['Agdossier']['ag_charge'];
                                        /*
		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];*/
		                               
		                                $alloc = $nbcharge * $montant;

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$alloc,
									    'montant' => $alloc,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
                                    /*******************CONGES MATERNITE******************/
		                            if($code == 10)
		                            {
		                            	
		                            	$conge_mat =  $indemnite['Agindemnite']['base_montant'];
		                              
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$conge_mat,
									    'montant' => $conge_mat,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************ANCIENNETE*************************/
		                            if($code == 11)
		                            {
		                            	$sal = $this->Paramgrillesal->find('all', array('conditions'=>array("Paramgrillesal.paramclassification_id='$classification_id'",
		                                    "Paramgrillesal.paramechelon_id='$paramechelon_id'"), 'recursive'=>0));
		                                $base = $sal[0]['Paramgrillesal']['salaire'];
		                        	    
		    						    //$anciennete = $year_anc * ($base * 0.01);
										#==========================
										$tmp_anc = $year_anc * ($base * 0.01);
										$anciennete = round($tmp_anc);
		                                #==========================
		                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$base,
										    'taux' =>$year_anc,
										    'montant' => $anciennete,
										    'avoir_ret' => $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
								        
		                            }
		                            /*******************HEURE SUP******************/
		                            if($code == 12)
		                            {
		                            	
		                            	$heure_sup =  $indemnite['Agindemnite']['base_montant'];
		                                

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>'',
									    'montant' => $heure_sup,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
                                    /*******************CONGES PAYES******************/
		                            if($code == 23)
		                            {
		                            	
		                            	$conge_paye =  $indemnite['Agindemnite']['base_montant'];
		                              
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$conge_paye,
									    'montant' => $conge_paye,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************RETENUE PHARMACIE*************************/
		                            if($code == 29)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

		                                $affbontraite = $this->Affbontraite->find('all', array('conditions'=>array("Affbontraite.agdossier_id='$dossier'","Affbontraite.date_ret_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($affbontraite[0]['Affbontraite']['montant_ret_traite']))?$affbontraite[0]['Affbontraite']['montant_ret_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                            /*******************AVANTAGE NATURE FONCTION******************/
		                            if($code == 31)
		                            {
		                            	
		                            	$fonction =  $indemnite['Agindemnite']['base_montant'];
		                                //$avg_nat_fct = ($fonction / 240);

										#=======================
										$tmp_avg_nat_fct =  ($fonction / 240);
										$avg_nat_fct = round($tmp_avg_nat_fct);
										#=======================


		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_fct,
									    'montant' => $avg_nat_fct,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVANTAGE NATURE LOGEMENT******************/
		                            if($code == 32)
		                            {
		                            	
		                            	$logement =  $indemnite['Agindemnite']['base_montant'];
		                                //$avg_nat_log = ($logement / 240);

										 #=======================
										 $tmp_avg_nat_log =  ($logement / 240);
										 $avg_nat_log = round($tmp_avg_nat_log);
										 #=======================


		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_log,
									    'montant' => $avg_nat_log,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVANTAGE NATURE TRANSPORT******************/
		                            if($code == 33)
		                            {
		                            	
		                            	$transport =  $indemnite['Agindemnite']['base_montant'];
		                                //$avg_nat_trans = ($transport / 240);
                                        #=======================
										$tmp_avg_nat_trans =  ($transport / 240);
										$avg_nat_trans = round($tmp_avg_nat_trans);
										#=======================

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_trans,
									    'montant' => $avg_nat_trans,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVOIR 1*************************/
		                            if($code == 35)
		                            {
		                            	
		                            	$mnt_avoir1 =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir1,
									    'taux' =>'',
									    'montant' => $mnt_avoir1,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVOIR 2*************************/
		                            if($code == 36)
		                            {
		                            	
		                            	$mnt_avoir2 =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir2,
									    'taux' =>'',
									    'montant' => $mnt_avoir2,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVOIR AVANCEMENT*************************/
		                            if($code == 37)
		                            {
		                            	
		                            	$mnt_avoir_av =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir_av,
									    'taux' =>'',
									    'montant' => $mnt_avoir_av,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE GUICHET*************************/
		                           if(($code == 38 && $typefonction == 4)||
		                        	  ($code == 38 && $typefonction == 7))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVANCE SUR SALAIRE*************************/
		                            if($code == 98)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

	                                    $afftraite = $this->Afftraite->find('all', array('conditions'=>array("Afftraite.agdossier_id='{$dossier}'","Afftraite.date_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($afftraite[0]['Afftraite']['montant_traite']))?$afftraite[0]['Afftraite']['montant_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                            /******************SALAIRE BRUTE****************************/
		                            if($code == 500)
		                            {
		                            	
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
                                        $base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
	                                    $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:0;
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;
										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                $salaire_brute = 0;

		                                $salaire_brute = $trait_base + 
		                                               $indFonction +
		                                               $indLogement +
		                            	               $indTransport + 
		                            	               $indSujetion + 
		                            	               $anciennete +
		                             	               $allocation + 
		                             	               $indGuichet + 
		                             	               $indCaisse + 
		                             	               $avoir1 + 
		                             	               $avoir2 + 
		                             	               $avoirav  + 
		                             	               $avg_nat_trans +
		                             	               $avg_nat_fct + 
		                             	               $avg_nat_logement + 
		                             	               $sursalaire + 
		                             	               $heure_sup;
		                               
		                               /*--------------------*/
		                                $red = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'montant' => $salaire_brute,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($red);
		                            }
		                            /*******************CNSS*************************/
		                            if($code == 400)
		                            {
		                            	
		                            	$salairebrut = 0;
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
		                             	$base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
		                             	$fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:'';
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;

										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                               
		                                $salairebrut = $trait_base + $indFonction + $indLogement +
		                            	               $indTransport + $indSujetion + $anciennete +
		                             	               $allocation + $indGuichet + $indCaisse + 
		                             	               $avoir1 + $avoir2 + $avoirav  + $avg_nat_trans +
		                             	               $avg_nat_fct + $avg_nat_logement + $sursalaire + $heure_sup;

		                              
		                                $brute = 0.055 * $salairebrut;
		                                /*======5.5/100 salaire brute==========*/
						                $base = 0.08 * ($trait_base + $anciennete + $avoirav);
						                /*===============CNSS=========================*/
						                //$cnss = MIN($brute,$base,44000);
		                                /*--------------------*/
		                                #=========================
										$tmp_cnss = MIN($brute,$base,44000);
										
										$cnss = round($tmp_cnss);
										#=========================
		                                $ret_cnss = array('Rembulitem'=>array(
									        'rembulletin_id'=>$bulID,
									        'code'=>$code,
									        'designation'=>$libelle,
										    'base'=>$salairebrut,
										    'montant'=>$cnss,
										    'avoir_ret'=>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($ret_cnss);
		                            }
		                            /*******************CARFO*************************/
		                            if($code == 401)
		                            {
		                            	$carfo = 0;
		                            	$base_carfo =  $indemnite['Agindemnite']['base_montant'];
		                                

		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $taux = $mnt[0]['Paramindemitem']['taux'];
		                               
		                                //$carfo = $base_carfo * ($taux / 100);

										  #=========================
										  $tmp_carfo = $base_carfo * ($taux / 100);
										  $carfo = round($tmp_carfo);
										  #=========================

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$base_carfo,
									    'montant' => $carfo,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************IUTS*************************/
		                            if($code == 402)
		                            {
		                            	
		                            	$ret_cnss = 0;
		                            	$ret_carfo = 0;
		                            	$salaireImposable = 0;
                                        $exoPartiel =0;
                                        
                                        $heure_sup = 0;
		                            	

		                                $abat = 0;
		                                $salnetImposable = 0;
                                         $netImp = 0;

		                                $iutsBrute = 0;
		                                $iutsDeductible = 0;
                                        $net_iuts = 0;

		                                /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
		                                $salairebrut = 0;
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
		                             	$base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
		                             	$fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:'';
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                  /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;
										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                               
		                                $salairebrut = $trait_base + $indFonction + $indLogement +
		                            	               $indTransport + $indSujetion + $anciennete +
		                             	               $allocation + $indGuichet + $indCaisse + 
		                             	               $avoir1 + $avoir2 + $avoirav  + $avg_nat_trans +
		                             	               $avg_nat_fct + $avg_nat_logement + $sursalaire + $heure_sup;
		                                /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
		                                $ret_cnss_carfo = 0;
		                                $cotisation = $contrat['Agcontrat']['paramstructurecotsocial_id'];
		                                if(isset($cotisation) && $cotisation == 1)
		                                {
                                           /**************CNSS*******************/
				                           $white = $this->Rembulitem->find('all', array('conditions'=>array(
				                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='400'"), 'recursive'=>0));
									       $ret_cnss_carfo = (isset($white[0]['Rembulitem']['montant']))?$white[0]['Rembulitem']['montant']:0;
				                        }
		                                elseif(isset($cotisation) && $cotisation == 2)
		                                {
		                                   /**************CARFO*******************/
	                                       $yelow = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='401'"), 'recursive'=>0));
		                                   $ret_cnss_carfo = (isset($yelow[0]['Rembulitem']['montant']))?$yelow[0]['Rembulitem']['montant']:0;

		                                }else{$ret_cnss_carfo = 0;}
		                                
		                                /***************SALAIRE IMPOSABLE***********************/
		                                $salaireImposable = $salairebrut - $ret_cnss_carfo;


		                                $salImpo = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1000,
									        'designation' => 'Salaire imposable',
									        'montant' => $salaireImposable,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($salImpo);

		                                /***************Exoneration logement***********************/
		                                $logement = 0.2 *  $salaireImposable;
		                                $plafond_log = 75000;
                                        $indLog = $indLogement + $avg_nat_logement;

		                                $exoLogement = min($logement,$plafond_log,$indLog);

		                                $exo_log = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1002,
									        'designation' => 'Exoneration logement',
									        'montant' => $exoLogement,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_log);


                                        /***************Exoneration fonction***********************/
		                                $fonction = 0.05 *  $salaireImposable;
		                                $plafond_fct = 50000;
                                        $indFct = $indFonction +  $avg_nat_fct + 
                                                  $indSujetion + $indGuichet + $indCaisse;

		                                $exoFonction = min($fonction,$plafond_fct,$indFct);
                                        

							            $exo_fonct = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1004,
									        'designation' => 'Exoneration fonction',
									        'montant' => $exoFonction,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_fonct);

		                                /***************Exoneration transport***********************/
		                                $transport = 0.05 *  $salaireImposable;
		                                $plafond_transp = 30000;
                                        $indFct = $indTransport +  $avg_nat_trans;

		                                $exoTransport = min($transport,$plafond_transp,$indFct);
		                               
		                                $exo_trans = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1003,
									        'designation' => 'Exoneration transport',
									        'montant' => $exoTransport,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_trans);
                                        /**************EXONERATION PARTIEL************/

		                                $exoPartiel = $exoLogement + $exoFonction + $exoTransport;

                                        $exo_pat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1005,
									        'designation' => 'Exoneration partiel',
									        'montant' => $exoPartiel,
									        'avoir_ret' =>2
										    )
					                    );
					                    $this->Rembulitem->save($exo_pat);
		                              
		                                /*---------------Abattement---------------------*/
		                              
		                                $phi = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                                $classification_id = $phi[0]['Agavencement']['paramclassification_id'];

		                               
		                                if($classification_id <= 4)
		                                {
		                                  $abat = 0.25 * ($trait_base + $anciennete + $heure_sup + $avoirav);
		                                }
                                        else
		                                {
		                                   $abat = 0.2 * ($trait_base + $anciennete + $heure_sup + $avoirav);
		                                }

                                        
                                        
		                                $tmp_abat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1006,
									        'designation' => 'Abattement',
									        'montant' => $abat,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($tmp_abat);
		                               
		                                /*==================SALAIRE NET IMPOSABLE=====================*/

		                                $salnetImposable = $salaireImposable - $exoPartiel - $abat;

		                                $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);
		                               

							            $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1001,
									        'designation' => 'Salaire Net Imposable',
									        'montant' => $netImp,
									        'montant2' => $salnetImposable,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
		                                

							            /************* IUTS BRUTE**********************************/

		                                switch($netImp){
									        case $netImp<=30100;
									            /*---------------------------------------*/
									            //$iutsBrute = 0 * $netImp - 0;
									            $iutsBrute = $netImp;
							                    /*---------------------------------------*/
									            break;
									        case 30100<$netImp && $netImp<=50100;
									            /*---------------------------------------*/
									            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
									            $iutsBrute = 0 + (($netImp - 30100) *0.121);
					                            /*---------------------------------------*/
									            break;
									        case 50100 < $netImp && $netImp <= 80100;
									            /*---------------------------------------*/
									            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
									            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
									            /*---------------------------------------*/
									            break;
									        case 80100 < $netImp && $netImp <= 120100;
									            /*---------------------------------------*/
									            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
									            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
									            /*---------------------------------------*/
									            break;
									        case 120100 < $netImp && $netImp <=170100;
									            /*---------------------------------------*/
									            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
									            /*---------------------------------------*/
									            break;
									        case 170100 < $netImp && $netImp <= 250100;
									            /*---------------------------------------*/
									            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
									            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
									            /*---------------------------------------*/
									            break;
									        default: 
									             
                                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
									            break;
							            }

							            $iuts_brute = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 200,
									        'montant' => $iutsBrute,
									        'designation' => 'IUTS Brute',
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($iuts_brute);
							            


							           /************* IUTS DEDUCTIBLE*****************************/
                                      

							            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

		                                

		                                if($nbcharge == 0){$iutsDeductible = 0;}
		                                if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
		                                if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
		                                if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
		                                if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
		                              
		                               
							            $iuts_deduc = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 201,
									        'montant' => $iutsDeductible,
									        'designation' => 'IUTS Deductible',
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($iuts_deduc);
		                               
							           /************* IUTS NET************************************/
                                        //$net_iuts = $iutsBrute -  $iutsDeductible;
                                        #==========================
										$tmp_net_iuts = $iutsBrute -  $iutsDeductible;
										$net_iuts = round($tmp_net_iuts);
										#=========================
                                        $netIuts = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$netImp,
										    'taux' =>'',
										    'montant' =>  $net_iuts,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($netIuts);
							           /*********FIN*****UITS NET**********************************/
 
                                       /*************Impot sur salaire************************************/
                                        //$impot_salaire = $net_iuts +  $ret_cnss_carfo;
                                        #=====================================
										$tmp_impot_salaire = $net_iuts +  $ret_cnss_carfo;
										$impot_salaire = round($tmp_impot_salaire);
										#=====================================
                                         $impSal = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 501,
									        'designation' => 'Impot sur salaire',
									        'montant' => $impot_salaire,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($impSal);

							            /*************TPA************************************/
                                        //$tpa = $salairebrut * 0.03;
										#=======================
										$var_tpa = $salairebrut * 0.03;
										$tpa = round($var_tpa);
										#=======================

                                         $tmp_tpa = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 700,
									        'designation' => 'TPA',
									        'montant' => $tpa,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($tmp_tpa);

							            /*************CNSS Patronal************************************/
                                        $cnss_pat = $salairebrut * 0.215;

                                         $tmp_cnss_pat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 902,
									        'designation' => 'CNSS Patronal',
									        'montant' => $cnss_pat,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($tmp_cnss_pat);


                                      
		                            }
		                            /*******************Cotisation mtuelle*************************/
		                            if($code == 403)
		                            {
		                            	
		                            	$cot_mnt =  $indemnite['Agindemnite']['base_montant'];

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
										'taux' => '30',
									    'base' =>$cot_mnt,
									    'montant' => $cot_mnt,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AUTRE RETENUE******************/
		                            if($code == 1008)
		                            {
		                            	
		                            	$autre_ret =  $indemnite['Agindemnite']['base_montant'];
		                              

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$autre_ret,
									    'taux' => '30',
									    'montant' => $autre_ret,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
				                    /*---------Fin traitement des differents éléments---------*/
		                        }
		                        
		                    
	                    }
	                }
	                /*++++++++++++++++BULLETIN NORMALE+++++++++++++++++++++++++++++++*/
	            }
	            elseif($type_bulletin == 2)
	            {
		           
                    $date_debut = $postData['Rembulletin']['date_debut'];
		            $date_fin = $postData['Rembulletin']['date_fin'];
		            $type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];


		            $contrats = $this->Agcontrat->find('all', array('conditions'=>array("Agcontrat.statut='1'"), 'recursive'=>0));
	                $i = 0;
	                foreach($contrats as $index => $contrat)
	                {

			           /*++++++++++++++++GRATIFICATION OU 13E MOIS+++++++++++++++++++++++++++++++*/
	                   $ag_dossier = $contrat['Agcontrat']['agdossier_id'];
	                   
	                    $decembre = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.ag_dossier='{$ag_dossier}'","Rembulletin.paramtypesalaire_id='1'","Rembulletin.date_debut ='$date_debut'","Rembulletin.date_fin='{$date_fin}'"), 'recursive'=>0));
	                    $bulletinid = $decembre[0]['Rembulletin']['id'];
			            $nbr_bul = count($decembre);
		                if($nbr_bul > 0)
		                {
		                  /*-------------------------------------------*/
			              $count = 0;
			              $bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
					      $count = count($bulletins);
	                      if($count > 0){$count = $count + 1;}else{$count = 1;}
		                  /*--------------ENREGISTREMENT BULLETIN-----------------------------*/
		                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
			              $contrat_id = $contrat[0]['Agcontrat']['id'];
			              $num_contrat = $contrat[0]['Agcontrat']['num_contrat'];
			              $matricule_ag = $contrat[0]['Agcontrat']['matricule'];


		                  $agent = array('Rembulletin'=>array(
					        'num_bull' => $count,
					        'date_debut' => $date_debut,
					        'date_fin' => $date_fin,
						    'agcontrat_id' => $contrat_id,
						    'num_contrat' => $num_contrat,
						    'ag_dossier' => $ag_dossier,
						    'matricule' => $matricule_ag,
						    'paramtypesalaire_id' => $type_bulletin,
						    
							   )
				            );
						    $this->Rembulletin->save($agent);

						    $bul_id = $this->Rembulletin->id;
		                  
		                  /*--------------Salaire de base gratification------------------------------*/
		                  $grat = $this->Paramsalbasegrat->find('all', array('conditions'=>array("Paramsalbasegrat.agdossier_id='{$ag_dossier}'"), 'recursive'=>0));
		                  $sal_base = (isset($grat[0]['Paramsalbasegrat']['salaire_base_grat']))?$grat[0]['Paramsalbasegrat']['salaire_base_grat']:0;

		                  $item = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 1,
					        'designation' => 'Traitement de base',
						    'base' =>$sal_base,
						    'taux' =>30,
						    'montant' => $sal_base,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($item);
		                 
		                  /*-----------Traitement de base normale-------------*/
		                  $b = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='1'"), 'recursive'=>0));
		                  $base = (isset($b[0]['Rembulitem']['montant']))?$b[0]['Rembulitem']['montant']:0;

		                  /*-----------Fonction-------------*/
		                  $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='3'"), 'recursive'=>0));
		                  $fonction = (isset($fct[0]['Rembulitem']['montant']))?$fct[0]['Rembulitem']['montant']:0;

	                       $fct_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 3,
					        'designation' => 'Indemnite de fonction',
						    'base' =>$fonction,
						    'taux' =>30,
						    'montant' => $fonction,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($fct_ind);

		                   /*-----------Logement-------------*/
		                   $log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='4'"), 'recursive'=>0));
		                  $logement =  (isset($log[0]['Rembulitem']['montant']))?$log[0]['Rembulitem']['montant']:0;

		                   $log_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 4,
					        'designation' => 'Indemnite de logement',
						    'base' =>$logement,
						    'taux' =>30,
						    'montant' => $logement,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($log_ind);
		                   /*-----------Transport-------------*/
		                  $trp = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='5'"), 'recursive'=>0));
		                  $transport = (isset($trp[0]['Rembulitem']['montant']))?$trp[0]['Rembulitem']['montant']:0;

		                  $tpr_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 5,
					        'designation' => 'Indemnite de transport',
						    'base' =>$transport,
						    'taux' =>30,
						    'montant' => $transport,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($tpr_ind);
		                   /*-----------Sujetion-------------*/
		                  $suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='6'"), 'recursive'=>0));
		                  $sujetion = (isset($suj[0]['Rembulitem']['montant']))?$suj[0]['Rembulitem']['montant']:0;

	                      $suj_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 6,
					        'designation' => 'Indemnite de sujetion',
						    'base' =>$sujetion,
						    'taux' =>30,
						    'montant' => $sujetion,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($suj_ind);

			               /*---------------------Anciennete------------------------------*/
		                  $anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='11'"), 'recursive'=>0));
		                  $taux = (isset($anc[0]['Rembulitem']['taux']))?$anc[0]['Rembulitem']['taux']:0;

		                  $anciennete = $sal_base * $taux * 0.01;

		                   $prime_anc = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 11,
					        'designation' => 'Anciennete',
						    'base' =>$sal_base,
						    'taux' =>30,
						    'montant' => $anciennete,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($prime_anc);

		                  /*-----------Caisse-------------*/
		                  $cai = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='7'"), 'recursive'=>0));
		                  $caisse = (isset($cai[0]['Rembulitem']['montant']))?$cai[0]['Rembulitem']['montant']:0;

		                  $cais_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 7,
					        'designation' => 'Indemnite de caisse',
						    'base' =>$caisse,
						    'taux' =>30,
						    'montant' => $caisse,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($cais_ind);

		                  /*-----------Allocation-------------*/
		                  $alc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='8'"), 'recursive'=>0));
		                  $allocation = (isset($alc[0]['Rembulitem']['montant']))?$alc[0]['Rembulitem']['montant']:0;

		                   $alloc_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 8,
					        'designation' => 'Allocation familiale',
						    'base' =>$allocation,
						    'taux' =>30,
						    'montant' => $allocation,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($alloc_ind);
		                  /*-----------Avantage nature logement-------------*/
		                  $anle = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='32'"), 'recursive'=>0));
		                  $av_nat_log= (isset($anle[0]['Rembulitem']['montant']))?$anle[0]['Rembulitem']['montant']:0;

		                  $nat_log = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 32,
					        'designation' => 'Avantage nature logement',
						    'base' =>$av_nat_log,
						    'taux' =>30,
						    'montant' => $av_nat_log,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($nat_log);
		                  /*-----------Avantage nature transport-------------*/
		                  $ant = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='33'"), 'recursive'=>0));
		                  $av_nat_trans = (isset($ant[0]['Rembulitem']['montant']))?$ant[0]['Rembulitem']['montant']:0;

		                  $nat_tranp = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 33,
					        'designation' => 'Avantage nature transport',
						    'base' =>$av_nat_trans,
						    'taux' =>30,
						    'montant' => $av_nat_trans,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($nat_tranp);
		                  /*-----------Guichet-------------*/
		                  $gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='38'"), 'recursive'=>0));
		                  $guichet = (isset($gui[0]['Rembulitem']['montant']))?$gui[0]['Rembulitem']['montant']:0;

		                  $gui_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 38,
					        'designation' => 'Indemnite de guichet',
						    'base' =>$guichet,
						    'taux' =>30,
						    'montant' => $guichet,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($gui_ind);
		                  
		                  /*----------Salaire brute---------------------------*/
	                      $salairebrut = $sal_base + $anciennete + $fonction + $logement + $transport + $sujetion + $caisse + $allocation + $av_nat_log + $av_nat_trans + $guichet;


	                      $sal_brute = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 500,
					        'designation' => 'Salaire Brute',
						    'base' =>$salairebrut,
						    'taux' =>30,
						    'montant' => $salairebrut,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($sal_brute);


	                      /*----------Cotisation sociale----------------------*/
		                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
			              $cotisation = $contrat[0]['Agcontrat']['paramstructurecotsocial_id'];
	                      
	                      /**************CNSS*******************/
	                      $cnss_gratif = 0;
	                       $txt = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='400'"), 'recursive'=>0));
			               $cnss_normal = $txt[0]['Rembulitem']['montant'];

			                if(isset($cnss_normal) && $cnss_normal >= 44000)
			                  {
			                  	$cnss_gratif = 0;
			                  }
			                  else
			                  {
			                  	$cnss_gratif = 44000 - $cnss_normal;
			                  }

	                      $ret_cnss_carfo = 0;
		                  if(isset($cotisation) && $cotisation == 1)
		                  {
		                     /**************CNSS*******************/
			                 $ret_cnss_carfo =  $cnss_gratif;

				             $cnss = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 400,
						        'designation' => 'CNSS',
							    'base' =>$sal_base,
							    'taux' =>30,
							    'montant' => $ret_cnss_carfo,
							    'avoir_ret' =>2
						      )
	                           );
			                  $this->Rembulitem->save($cnss);
			               }
			               elseif(isset($cotisation) && $cotisation == 2)
		                   {
		                      /**************CARFO*******************/
		                      $ret_cnss_carfo = 0;   

		                      $carfo = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 401,
						        'designation' => 'CARFO',
							    'base' =>$sal_base,
							    'taux' =>30,
							    'montant' => $ret_cnss_carfo,
							    'avoir_ret' =>2
						      )
	                           );
			                  $this->Rembulitem->save($carfo);

		                    }else{$ret_cnss_carfo = 0;}
		                    /*----------Fin Cotisation sociale--------------------*/
		                    
	                        /***************SALAIRE IMPOSABLE***********************/
	                        $salaireImposable = $salairebrut - $ret_cnss_carfo;

	                        $salImpo = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 1000,
						        'designation' => 'Salaire imposable',
						        'montant' => $salaireImposable,
						        'avoir_ret' =>2
							    )
		                    );
				            $this->Rembulitem->save($salImpo);
	                        /*==================SALAIRE NET IMPOSABLE=====================*/

	                        $salnetImposable = $salaireImposable;

	                        $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);

				            $sal_net_imp = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 1001,
						        'designation' => 'Salaire Net Imposable',
						        'montant' => $netImp,
						        'montant2' => $salnetImposable,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($sal_net_imp);
		                        
					        /************* IUTS BRUTE**********************************/

	                        switch($netImp){
						        case $netImp<=30100;
						            /*---------------------------------------*/
						            //$iutsBrute = 0 * $netImp - 0;
						            $iutsBrute = $netImp;
				                    /*---------------------------------------*/
						            break;
						        case 30100<$netImp && $netImp<=50100;
						            /*---------------------------------------*/
						            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
						            $iutsBrute = 0 + (($netImp - 30100) *0.121);
		                            /*---------------------------------------*/
						            break;
						        case 50100 < $netImp && $netImp <= 80100;
						            /*---------------------------------------*/
						            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
						            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
						            /*---------------------------------------*/
						            break;
						        case 80100 < $netImp && $netImp <= 120100;
						            /*---------------------------------------*/
						            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
						            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
						            /*---------------------------------------*/
						            break;
						        case 120100 < $netImp && $netImp <=170100;
						            /*---------------------------------------*/
						            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
						            /*---------------------------------------*/
						            break;
						        case 170100 < $netImp && $netImp <= 250100;
						            /*---------------------------------------*/
						            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
						            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
						            /*---------------------------------------*/
						            break;
						        default: 
						             
	                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
						            break;
				            }
				            $iuts_brute = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 200,
						        'montant' => $iutsBrute,
						        'designation' => 'IUTS Brute',
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($iuts_brute);
					        /************* IUTS DEDUCTIBLE*****************************/

				            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$ag_dossier), 'recursive'=>0));
	                        $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

	                        

	                        if($nbcharge == 0){$iutsDeductible = 0;}
	                        if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
	                        if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
	                        if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
	                        if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
	                      
	                       
				            $iuts_deduc = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 201,
						        'montant' => $iutsDeductible,
						        'designation' => 'IUTS Deductible',
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($iuts_deduc);
	                       
				           /************* IUTS NET************************************/
	                        $net_iuts = $iutsBrute -  $iutsDeductible;
	                        
	                        $sal_net = $salairebrut - $ret_cnss_carfo - $net_iuts;

	                        $msg = 'BRUTE '.$salairebrut.' CNSS '.$ret_cnss_carfo.' Salaire imposable '.$salaireImposable.' Salaire netimposable '.$netImp.' IUTS BRUTE '.$iutsBrute.' IUTS Decductible '.$iutsDeductible.' IUTS NET '.$net_iuts.' Salaire NET '.$sal_net;
	                        //print_r('IUTS NET '.$net_iuts);
	                       $netIuts = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => '402',
						        'designation' => 'IUTS',
							    'base' =>$netImp,
							    'taux' =>'',
							    'montant' =>  $net_iuts,
							    'avoir_ret' =>2
							    )
							    
		                    );
				            $this->Rembulitem->save($netIuts);
				           /*********FIN*****UITS NET**********************************/

	                       /*************Impot sur salaire************************************/
	                       $impot_salaire = $net_iuts +  $ret_cnss_carfo;

	                         $impSal = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 501,
						        'designation' => 'Impot sur salaire',
						        'montant' => $impot_salaire,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($impSal);

				            /*************TPA************************************/
	                         $tpa = $salairebrut * 0.03;

	                         $tmp_tpa = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 700,
						        'designation' => 'TPA',
						        'montant' => $tpa,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($tmp_tpa);

				            /*************CNSS Patronal************************************/
	                        $cnss_pat = $salairebrut * 0.215;

	                         $tmp_cnss_pat = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 902,
						        'designation' => 'CNSS Patronal',
						        'montant' => $cnss_pat,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($tmp_cnss_pat);
		                    
		                }
		                else
		                {
		                	$msg = 'Le bulletin du mois de décembre n\'existe pas dans la base, calculer au préalable le bulletin du mois de décembre';
		                }
	    				
	                   /*++++++++++++++++FIN GRATIFICATION OU 13E MOIS+++++++++++++++++++++++++++*/
		            }                
		                      
	            }
	            elseif($type_bulletin == 3)
	            {
                    /*****************PRIME DE BILAN*******************************/
                    $date_debut = $postData['Rembulletin']['date_debut'];
		            $date_fin = $postData['Rembulletin']['date_fin'];
		            $type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];
		            $taux_prime = $postData['Rembulletin']['taux_prime'];

                    $year = date(Y) - 1;
                    $begin = $year.'-12-01';
                    $end = $year.'-12-31';
		            //$contrats = $this->Agcontrat->find('all', array('conditions'=>array("Agcontrat.statut='1'"), 'recursive'=>0));

		             $contrats = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.paramtypesalaire_id='2'","Rembulletin.date_debut ='$begin'","Rembulletin.date_fin ='$end'",), 'recursive'=>0));
                  
                    $cpt = count($contrats);
	                if($cpt > 0)
	                {
		                $i = 0;
		                foreach($contrats as $index => $contrat)
		                {

				           /*++++++++++++++++PRIME DE BILAN+++++++++++++++++++++++++++++++*/
		                   $ag_dossier = $contrat['Rembulletin']['ag_dossier'];
		                   
		                    $decembre = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.ag_dossier='{$ag_dossier}'","Rembulletin.paramtypesalaire_id='2'","Rembulletin.date_debut ='{$begin}'","Rembulletin.date_fin='{$end}'"), 'recursive'=>0));
		                    $bulletinid = $decembre[0]['Rembulletin']['id'];
				            $nbr_bul = count($decembre);
			                if($nbr_bul > 0)
			                {
			                  /*-------------------------------------------*/
				              $count = 0;
				              $bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
						      $count = count($bulletins);
		                      if($count > 0){$count = $count + 1;}else{$count = 1;}
			                  /*--------------ENREGISTREMENT BULLETIN-----------------------------*/
			                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
				              $contrat_id = $contrat[0]['Agcontrat']['id'];
				              $num_contrat = $contrat[0]['Agcontrat']['num_contrat'];
				              $matricule_ag = $contrat[0]['Agcontrat']['matricule'];


			                  $agent = array('Rembulletin'=>array(
						        'num_bull' => $count,
						        'date_debut' => $date_debut,
						        'date_fin' => $date_fin,
							    'agcontrat_id' => $contrat_id,
							    'num_contrat' => $num_contrat,
							    'ag_dossier' => $ag_dossier,
							    'matricule' => $matricule_ag,
							    'paramtypesalaire_id' => $type_bulletin,
							    
								   )
					            );
							    $this->Rembulletin->save($agent);

							    $bul_id = $this->Rembulletin->id;
			                  
			                  /*--------------Salaire de base gratification------------------------------*/
			                  $grat = $this->Paramsalbasegrat->find('all', array('conditions'=>array("Paramsalbasegrat.agdossier_id='{$ag_dossier}'"), 'recursive'=>0));
			                  $sal_base = (isset($grat[0]['Paramsalbasegrat']['salaire_base_grat']))?$grat[0]['Paramsalbasegrat']['salaire_base_grat']:0;

			                  $traitement_base = ($sal_base * $taux_prime) / 100;
		                      /*-----------------*/

			                  $item = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 1,
						        'designation' => 'Traitement de base',
							    'base' =>$traitement_base,
							    'taux' =>30,
							    'montant' => $traitement_base,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($item);
			                 
			                  /*-----------Traitement de base normale-------------*/
			                  $b = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='1'"), 'recursive'=>0));
			                  $base = (isset($b[0]['Rembulitem']['montant']))?$b[0]['Rembulitem']['montant']:0;

			                  /*-----------Fonction-------------*/
			                  $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='3'"), 'recursive'=>0));
			                  $fonction = (isset($fct[0]['Rembulitem']['montant']))?$fct[0]['Rembulitem']['montant']:0;

		                       $fct_ind = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 3,
						        'designation' => 'Indemnite de fonction',
							    'base' =>$fonction,
							    'taux' =>30,
							    'montant' => $fonction,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($fct_ind);

			                   /*-----------Logement-------------*/
			                   $log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='4'"), 'recursive'=>0));
			                  $logement =  (isset($log[0]['Rembulitem']['montant']))?$log[0]['Rembulitem']['montant']:0;

			                   $log_ind = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 4,
						        'designation' => 'Indemnite de logement',
							    'base' =>$logement,
							    'taux' =>30,
							    'montant' => $logement,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($log_ind);
			                   /*-----------Transport-------------*/
			                  $trp = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='5'"), 'recursive'=>0));
			                  $transport = (isset($trp[0]['Rembulitem']['montant']))?$trp[0]['Rembulitem']['montant']:0;

			                  $tpr_ind = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 5,
						        'designation' => 'Indemnite de transport',
							    'base' =>$transport,
							    'taux' =>30,
							    'montant' => $transport,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($tpr_ind);
			                   /*-----------Sujetion-------------*/
			                  $suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='6'"), 'recursive'=>0));
			                  $sujetion = (isset($suj[0]['Rembulitem']['montant']))?$suj[0]['Rembulitem']['montant']:0;

		                      $suj_ind = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 6,
						        'designation' => 'Indemnite de sujetion',
							    'base' =>$sujetion,
							    'taux' =>30,
							    'montant' => $sujetion,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($suj_ind);

				               /*---------------------Anciennete------------------------------*/

	                           $anciennete = 0;

		                       $anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='11'"), 'recursive'=>0));
				               $anciennete = $anc[0]['Rembulitem']['montant'];


			                   $traitement_base = ($sal_base * $taux_prime) / 100;

			                   $prime_anc = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 11,
						        'designation' => 'Anciennete',
							    'base' =>$traitement_base,
							    'taux' =>30,
							    'montant' => $anciennete,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($prime_anc);

			                  /*-----------Caisse-------------*/
			                  $cai = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='7'"), 'recursive'=>0));
			                  $caisse = (isset($cai[0]['Rembulitem']['montant']))?$cai[0]['Rembulitem']['montant']:0;

			                  $cais_ind = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 7,
						        'designation' => 'Indemnite de caisse',
							    'base' =>$caisse,
							    'taux' =>30,
							    'montant' => $caisse,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($cais_ind);

			                  /*-----------Allocation-------------*/
			                  $alc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='8'"), 'recursive'=>0));
			                  $allocation = (isset($alc[0]['Rembulitem']['montant']))?$alc[0]['Rembulitem']['montant']:0;

			                   $alloc_ind = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 8,
						        'designation' => 'Allocation familiale',
							    'base' =>$allocation,
							    'taux' =>30,
							    'montant' => $allocation,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($alloc_ind);
			                  /*-----------Avantage nature logement-------------*/
			                  $anle = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='32'"), 'recursive'=>0));
			                  $av_nat_log= (isset($anle[0]['Rembulitem']['montant']))?$anle[0]['Rembulitem']['montant']:0;

			                  $nat_log = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 32,
						        'designation' => 'Avantage nature logement',
							    'base' =>$av_nat_log,
							    'taux' =>30,
							    'montant' => $av_nat_log,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($nat_log);
			                  /*-----------Avantage nature transport-------------*/
			                  $ant = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='33'"), 'recursive'=>0));
			                  $av_nat_trans = (isset($ant[0]['Rembulitem']['montant']))?$ant[0]['Rembulitem']['montant']:0;

			                  $nat_tranp = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 33,
						        'designation' => 'Avantage nature transport',
							    'base' =>$av_nat_trans,
							    'taux' =>30,
							    'montant' => $av_nat_trans,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($nat_tranp);
			                  /*-----------Guichet-------------*/
			                  $gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='38'"), 'recursive'=>0));
			                  $guichet = (isset($gui[0]['Rembulitem']['montant']))?$gui[0]['Rembulitem']['montant']:0;

			                  $gui_ind = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 38,
						        'designation' => 'Indemnite de guichet',
							    'base' =>$guichet,
							    'taux' =>30,
							    'montant' => $guichet,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($gui_ind);
			                  
			                  /*----------Salaire brute---------------------------*/
			                  /*---------------------------*/
				                  $traitement_base = ($sal_base * $taux_prime) / 100;
				                  /*------------------*/
		                      $salairebrut = $traitement_base + $anciennete + $fonction + $logement + $transport + $sujetion + $caisse + $allocation + $av_nat_log + $av_nat_trans + $guichet;


		                      $sal_brute = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 500,
						        'designation' => 'Salaire Brute',
							    'base' =>$salairebrut,
							    'taux' =>30,
							    'montant' => $salairebrut,
							    'avoir_ret' =>1
							   )
		                       );
				               $this->Rembulitem->save($sal_brute);


		                      /*----------Cotisation sociale----------------------*/
			                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
				              $cotisation = $contrat[0]['Agcontrat']['paramstructurecotsocial_id'];


				              /*******************AUTRE RETENUE******************/  
                              $indemnites = $this->Agindemnite->find('all', array('conditions'=>array("Agindemnite.agcontrat_id='$contrat_id'","Agindemnite.code_paramind='1008'")));
                               $mnt_retenue =  $indemnites[0]['Agindemnite']['base_montant'];
	                        	
                                 
	                        	$retenue = array('Rembulitem'=>array(
						            'rembulletin_id' => $bul_id,
							        'code' => 1008,
							        'designation' => 'Autre retenue',
								    'base' =>$traitement_base,
								    'taux' =>30,
								    'montant' => $mnt_retenue,
								    'avoir_ret' =>2
							          )
				                    );
						        $this->Rembulitem->save($retenue);  
				              /*---------Fin AUTRE RETENUE---------*/
		                      
		                      /**************CNSS*******************/
		                       $cnss_prime = 0;


		                       /*$gratif = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='400'"), 'recursive'=>0));
				               $cnss_gratif = $gratif[0]['Rembulitem']['montant'];
				              

				               if($cnss_gratif == 0)
				               {
				               	  $cnss_prime = 0;
				               }

				               if($cnss_gratif > 0 && $cnss_gratif <= 33000)
				               {
				               	  $cnss_prime = 33000 - $cnss_gratif;
				               }
				               else{ $cnss_prime = 0; }*/

		                      
		                      $ret_cnss_carfo = 0;

		                      $traitement_base = ($sal_base * $taux_prime) / 100;
			                  if(isset($cotisation) && $cotisation == 1)
			                  {
			                     /**************CNSS*******************/

				                 $ret_cnss_carfo =  $cnss_prime;

					             $cnss = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 400,
							        'designation' => 'CNSS',
								    'base' =>$traitement_base,
								    'taux' =>30,
								    'montant' => $ret_cnss_carfo,
								    'avoir_ret' =>2
							      )
		                           );
				                  $this->Rembulitem->save($cnss);
				               }
				               elseif(isset($cotisation) && $cotisation == 2)
			                   {
			                      /**************CARFO*******************/
			                      $ret_cnss_carfo = 0;   

			                      $carfo = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 401,
							        'designation' => 'CARFO',
								    'base' =>$traitement_base,
								    'taux' =>30,
								    'montant' => $ret_cnss_carfo,
								    'avoir_ret' =>2
							      )
		                           );
				                  $this->Rembulitem->save($carfo);

			                    }else{$ret_cnss_carfo = 0;}
			                    /*----------Fin Cotisation sociale--------------------*/
			                    
		                        /***************SALAIRE IMPOSABLE***********************/
		                        $salaireImposable = $salairebrut - $ret_cnss_carfo;

		                        $salImpo = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 1000,
							        'designation' => 'Salaire imposable',
							        'montant' => $salaireImposable,
							        'avoir_ret' =>2
								    )
			                    );
					            $this->Rembulitem->save($salImpo);
		                        /*==================SALAIRE NET IMPOSABLE=====================*/

		                        $salnetImposable = $salaireImposable;

		                        $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);

					            $sal_net_imp = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 1001,
							        'designation' => 'Salaire Net Imposable',
							        'montant' => $netImp,
							        'montant2' => $salnetImposable,
							        'avoir_ret' =>2
								    
								    )
								    
			                    );
					            $this->Rembulitem->save($sal_net_imp);
			                        
						        /************* IUTS BRUTE**********************************/

		                        switch($netImp){
							        case $netImp<=30100;
							            /*---------------------------------------*/
							            //$iutsBrute = 0 * $netImp - 0;
							            $iutsBrute = $netImp;
					                    /*---------------------------------------*/
							            break;
							        case 30100<$netImp && $netImp<=50100;
							            /*---------------------------------------*/
							            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
							            $iutsBrute = 0 + (($netImp - 30100) *0.121);
			                            /*---------------------------------------*/
							            break;
							        case 50100 < $netImp && $netImp <= 80100;
							            /*---------------------------------------*/
							            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
							            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
							            /*---------------------------------------*/
							            break;
							        case 80100 < $netImp && $netImp <= 120100;
							            /*---------------------------------------*/
							            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
							            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
							            /*---------------------------------------*/
							            break;
							        case 120100 < $netImp && $netImp <=170100;
							            /*---------------------------------------*/
							            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
							            /*---------------------------------------*/
							            break;
							        case 170100 < $netImp && $netImp <= 250100;
							            /*---------------------------------------*/
							            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
							            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
							            /*---------------------------------------*/
							            break;
							        default: 
							             
		                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
							            break;
					            }
					            $iuts_brute = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 200,
							        'montant' => $iutsBrute,
							        'designation' => 'IUTS Brute',
							        'avoir_ret' =>2
								    
								    )
								    
			                    );
					            $this->Rembulitem->save($iuts_brute);
						        /************* IUTS DEDUCTIBLE*****************************/

					            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$ag_dossier), 'recursive'=>0));
		                        $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

		                        

		                        if($nbcharge == 0){$iutsDeductible = 0;}
		                        if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
		                        if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
		                        if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
		                        if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
		                      
		                       
					            $iuts_deduc = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 201,
							        'montant' => $iutsDeductible,
							        'designation' => 'IUTS Deductible',
							        'avoir_ret' =>2
								    
								    )
								    
			                    );
					            $this->Rembulitem->save($iuts_deduc);
		                       
					           /************* IUTS NET************************************/
		                        $net_iuts = $iutsBrute -  $iutsDeductible;
		                        
		                        $sal_net = $salairebrut - $ret_cnss_carfo - $net_iuts;

		                        $msg = 'BRUTE '.$salairebrut.' CNSS '.$ret_cnss_carfo.' Salaire imposable '.$salaireImposable.' Salaire netimposable '.$netImp.' IUTS BRUTE '.$iutsBrute.' IUTS Decductible '.$iutsDeductible.' IUTS NET '.$net_iuts.' Salaire NET '.$sal_net;
		                        //print_r('IUTS NET '.$net_iuts);
		                       $netIuts = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => '402',
							        'designation' => 'IUTS',
								    'base' =>$netImp,
								    'taux' =>'',
								    'montant' =>  $net_iuts,
								    'avoir_ret' =>2
								    )
								    
			                    );
					            $this->Rembulitem->save($netIuts);
					           /*********FIN*****UITS NET**********************************/

		                       /*************Impot sur salaire************************************/
		                       $impot_salaire = $net_iuts +  $ret_cnss_carfo;

		                         $impSal = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 501,
							        'designation' => 'Impot sur salaire',
							        'montant' => $impot_salaire,
							        'avoir_ret' =>2
								    
								    )
								    
			                    );
					            $this->Rembulitem->save($impSal);

					            /*************TPA************************************/
		                         $tpa = $salairebrut * 0.03;

		                         $tmp_tpa = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 700,
							        'designation' => 'TPA',
							        'montant' => $tpa,
							        'avoir_ret' =>2
								    
								    )
								    
			                    );
					            $this->Rembulitem->save($tmp_tpa);

					            /*************CNSS Patronal************************************/
		                        $cnss_pat = $salairebrut * 0.215;

		                         $tmp_cnss_pat = array('Rembulitem'=>array(
							        'rembulletin_id' => $bul_id,
							        'code' => 902,
							        'designation' => 'CNSS Patronal',
							        'montant' => $cnss_pat,
							        'avoir_ret' =>2
								    
								    )
								    
			                    );
					            $this->Rembulitem->save($tmp_cnss_pat);
			                    
			                }
			                else
			                {
			                	$msg = 'Le bulletin du mois de décembre n\'existe pas dans la base, calculer au préalable le bulletin du mois de décembre';
			                }
		    				
		                   /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
			            }                
		            }
		            else
		            {
		            	$msg = 'La gratification du mois de décembre n\'existe pas dans la base, calculer au préalable le gratification du mois de décembre';
		            }      
                    /*++++++++++++++++FIN PRIME DE BILAN+++++++++++++++++++++++++++*/
                }
                elseif($type_bulletin == 4)
				{
				    /*++++++++++++++++SALAIRE SPECIAL+++++++++++++++++++++++++++++++*/
	                $contrats = $this->Agcontrat->find('all', array('order'=>'id ASC'));
	                $i = 0;
	                foreach ($contrats as $index => $contrat)
	                {
	                	$hp = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin. 	agcontrat_id='{$contrat['Agcontrat']['id']}'","Rembulletin.paramtypesalaire_id='{$postData['Rembulletin']['paramtypesalaire_id']}'","Rembulletin.date_debut ='{$postData['Rembulletin']['date_debut']}'","Rembulletin.date_fin='{$postData['Rembulletin']['date_fin']}'"), 'recursive'=>0));
			            $nbr_bul = count($hp);
	                    if($nbr_bul > 0)
	                    {
	                      /////****************/
	                    }
	                    else
	                    {
		                	$i++;
		                	/*-------------------------------------------*/
		                	$count = 0;
		                	$bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
				            $count = count($bulletins);

				            if($count > 0){$count = $count + 1;}else{$count = 1;}
		                	/*-------------------------------------------*/
		                	$agent = array('Rembulletin'=>array(
						        'num_bull' => $count,
						        'date_debut' => $postData['Rembulletin']['date_debut'],
						        'date_fin' => $postData['Rembulletin']['date_fin'],
							    'agcontrat_id' => $contrat['Agcontrat']['id'],
							    'num_contrat' => $contrat['Agcontrat']['num_contrat'],
							    'ag_dossier' => $contrat['Agcontrat']['agdossier_id'],
							    'matricule' => $contrat['Agcontrat']['matricule'],
							    'paramtypesalaire_id' => $postData['Rembulletin']['paramtypesalaire_id'],
							   
								   )
					            );
							    $this->Rembulletin->save($agent);
							    $bulID = $this->Rembulletin->id;
							    $periode_fin_bul = $postData['Rembulletin']['date_fin'];
		                        /*-----------------Contrat de l'agent-----------------------------------*/
		                        $cont = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $date_embauche = $cont[0]['Agcontrat']['date_debut'];
		                        $agdossier = $cont[0]['Agcontrat']['agdossier_id'];
		                        /*-----------------Indemnites/Avantages/Retenues de l'agent------------*/
		                        $indemnites = $this->Agindemnite->find('all', array('conditions'=>array('Agindemnite.agcontrat_id='.$contrat['Agcontrat']['id'])));
		                        
		                        $ind = $this->Paramindemnite->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC'));
		                        /*---------------Affectation ou Mutation de l'agent-------------------*/
		                         $affec = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $typefonction = $affec[0]['Agaffectmutation']['paramtypefonction_id'];
		                        /*-----------------------------------------------*/
		                        $base = 0;
		                        $montant = 0;
		                        foreach ($indemnites as $indemnite)
		                        {
		                        	
		                        	$X =  $indemnite['Agindemnite']['paramindemnite_id'];

		                        	$ind = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.id='.$X), 'recursive'=>0));
		                        	$identifiant = $ind[0]['Paramindemnite']['id'];
				                    $code = $ind[0]['Paramindemnite']['code'];
				                    $libelle = $ind[0]['Paramindemnite']['libelle'];
		                            $avoiret = $ind[0]['Paramindemnite']['paramavoiret_id'];
		                            //$type = $ind[0]['Paramindemnite']['paramtypefonction_id'];
		                            /*-----------AVANCEMENT-----------------------------------*/
		                            $av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                            $classification_id = $av[0]['Agavencement']['paramclassification_id'];
				                    $paramechelon_id = $av[0]['Agavencement']['paramechelon_id'];
				                    $year_anc = $av[0]['Agavencement']['anciennete'];
		                            /*======================*/
		                            /*---------TRAITEMENT DE BASE------------------------------*/
		                            if($code == 1)
		                            {
		                                $sal = $this->Paramgrillesal->find('all', array('conditions'=>array(
		                                	"Paramgrillesal.paramclassification_id='$classification_id'",
		                                    "Paramgrillesal.paramechelon_id='$paramechelon_id'"), 'recursive'=>0));
		                                $base = $sal[0]['Paramgrillesal']['salaire'];
		                                
		                                $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$base,
										    'taux' =>30,
										    'montant' => $base,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
							           
		                            }
		                            /*******************INDEMNITE FONCTION*************************/
		                            if(($code == 3 && $typefonction == 1) ||
		                        	   ($code == 3 && $typefonction == 2) ||
		                               ($code == 3 && $typefonction == 3))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE LOGEMENT*************************/
		                            if($code == 4)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE TRANSPORT*************************/
		                            if($code == 5)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE SUJETION*************************/
		                            if($code == 6)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE CAISSE*************************/
		                            if($code == 7 && $typefonction == 5)
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************ALLOCATION FAMILLIALE*************************/
		                            if($code == 8)
		                            {
		                            	$alloc = 0;
		                            	
		                                $pegaz = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $pegaz[0]['Agdossier']['ag_charge'];

		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                                $alloc = $nbcharge * $montant;

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$alloc,
									    'montant' => $alloc,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************ANCIENNETE*************************/
		                            if($code == 11)
		                            {
		                            	
		    						    $sal = $this->Paramgrillesal->find('all', array('conditions'=>array("Paramgrillesal.paramclassification_id='$classification_id'",
		                                    "Paramgrillesal.paramechelon_id='$paramechelon_id'"), 'recursive'=>0));
		                                $base = $sal[0]['Paramgrillesal']['salaire'];
		    						    $anciennete = $year_anc * ($base * 0.01);

		    						    
		                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$base,
										    'taux' =>$year_anc,
										    'montant' => $anciennete,
										    'avoir_ret' => $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
								        
		                            }
		                            /*******************AVANTAGE NATURE TRANSPORT******************/
		                            if($code == 33)
		                            {
		                            	
		                            	$transport =  $indemnite['Agindemnite']['base_montant'];
		                                

		                                /*$mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $taux = $mnt[0]['Paramindemitem']['taux'];
		                               
		                                $carfo = $base_carfo * ($taux / 100);*/

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$transport,
									    'montant' => $transport,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVOIR 2*************************/
		                            if($code == 36)
		                            {
		                            	
		                            	$mnt_avoir =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir,
									    'taux' =>'',
									    'montant' => $mnt_avoir,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE GUICHET*************************/
		                            if($code == 38 && $typefonction == 4)
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /******************SALAIRE BRUTE****************************/
		                            if($code == 500)
		                            {
		                            	$salairebrut = 0;
		                             	$indFonction = 0;
		                             	$allocation  = 0;
		                             	$cnss = 0;
		                             	$avoir2  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
		                             	/**************Taitement de base********************************/
		                             	$alpha = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $salairebase = $alpha[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction********************************/
		                             	$beta = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $beta[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de guichet********************************/
		                             	$fax = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4)
		                             	{
		                                	$indGuichet = $fax[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$pils = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5)
		                             	{
		                                	$indCaisse = $pils[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                /**************Indemnité de logement********************************/
		                             	$gama = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $gama[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$sigma = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $sigma[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$epsilon = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $epsilon[0]['Rembulitem']['montant'];
		                                /**************Anciennete********************************/
		                             	$rho = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($rho[0]['Rembulitem']['montant']))?$rho[0]['Rembulitem']['montant']:'';
		                               /**************Allocation********************************/
		                             	$fam = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                                //$allocation = $fam[0]['Rembulitem']['montant'];
		                                 $allocation = (isset($fam[0]['Rembulitem']['montant']))?$fam[0]['Rembulitem']['montant']:0;
		                                 //$avoir2
		                                 /**************Avoir2********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                               
		                                $salairebrut = $salairebase + $indFonction + $indLogement + $indTransport + $indSujetion + $anciennete + $allocation + $indGuichet + $indCaisse + $avoir2;
		                               
		                               /*--------------------*/
		                                $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'montant' => $salairebrut,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
		                            }
		                            /*******************CEGECI*************************/
		                            if($code == 13)
		                            {
		                            	$carfo = 0;
		                            	$mnt_cegeci =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_cegeci,
									    'taux' =>'30',
									    'montant' => $mnt_cegeci,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************RETENUE PRET*************************/
		                            if($code == 98)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

		                                $afftraite = $this->Afftraite->find('all', array('conditions'=>array("Afftraite.agdossier_id='$dossier'","Afftraite.date_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($afftraite[0]['Afftraite']['montant_traite']))?$afftraite[0]['Afftraite']['montant_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                            /*******************CNSS*************************/
		                            if($code == 400)
		                            {
		                            	//$this->calculCNSS($bulID,$code,$libelle,$avoiret);
		                            	$salairebase = 0;
		                            	$salairebrut = 0;
		                             	$indFonction = 0;
		                             	$allocation  = 0;
		                             	$anciennete = 0;
		                             	$avoir2  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
		                             	/**************Taitement de base********************************/
		                             	$alpha = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $salairebase = $alpha[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction********************************/
		                             	$beta = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $beta[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                 /**************Indemnité de guichet********************************/
		                             	$fax = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4)
		                             	{
		                                	$indGuichet = $fax[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$pils = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5)
		                             	{
		                                	$indCaisse = $pils[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                /**************Indemnité de logement********************************/
		                             	$gama = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $gama[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$sigma = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $sigma[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$epsilon = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $epsilon[0]['Rembulitem']['montant'];
		                                /**************Allocation********************************/
		                             	$fam = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                                //$allocation = $fam[0]['Rembulitem']['montant'];
		                                 $allocation = (isset($fam[0]['Rembulitem']['montant']))?$fam[0]['Rembulitem']['montant']:0;
		                                /**************Avoir2********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;
		                               
		                                /**************Anciennete********************************/
		                             	$rho = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = $rho[0]['Rembulitem']['montant'];
		                              
		                                /******************************************************/
		                                $salairebrute = $salairebase + $indFonction + $indLogement + $indTransport + $indSujetion + $indGuichet + $anciennete + $allocation+ $indCaisse + $avoir2;
		                              
		                                $brute = 0.055 * $salairebrute;
		                                /*======5.5/100 salaire brute==========*/
						                $base = 0.08 * ($salairebase + $anciennete);
						                /*===============CNSS=========================*/
						                $cnss = MIN($brute,$base,44000);
		                                /*--------------------*/
		                                
		                                $ret_cnss = array('Rembulitem'=>array(
									        'rembulletin_id'=>$bulID,
									        'code'=>$code,
									        'designation'=>$libelle,
										    'base'=>$salairebrute,
										    'montant'=>$cnss,
										    'avoir_ret'=>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($ret_cnss);
		                            }
		                            /*******************CARFO*************************/
		                            if($code == 401)
		                            {
		                            	$carfo = 0;
		                            	$base_carfo =  $indemnite['Agindemnite']['base_montant'];
		                                

		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $taux = $mnt[0]['Paramindemitem']['taux'];
		                               
		                                $carfo = $base_carfo * ($taux / 100);

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$base_carfo,
									    'montant' => $carfo,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************IUTS*************************/
		                            if($code == 402)
		                            {
		                            	$salairebase = 0;
		                            	$salairebrut = 0;
		                             	$indFonction = 0;
		                             	$avoir2  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
		                             	$cnss = 0;
		                             	$salaireImposable = 0;
		                             	
		                             	
		                             	$exoLogement =  0;
		                             	$exoTransport =  0;
		                             	$exoFonction =  0;
		                             	$allocation = 0;

		                             	$anciennete = 0;
		                             	$carfo = 0;
		                             	/**************Taitement de base********************************/
		                             	$alpha = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $salairebase = $alpha[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction********************************/
		                             	$beta = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $beta[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de guichet********************************/
		                             	$fax = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4)
		                             	{
		                                	$indGuichet = $fax[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                 /**************Indemnité de caisse********************************/
		                             	$pils = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5)
		                             	{
		                                	$indCaisse = $pils[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                /**************Indemnité de logement********************************/
		                             	$gama = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $gama[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$sigma = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $sigma[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$epsilon = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $epsilon[0]['Rembulitem']['montant'];
		                                /**************Anciennete********************************/
		                             	$rho = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = $rho[0]['Rembulitem']['montant'];
		                                /**************Allocation********************************/
		                             	$fam = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                                //$allocation = $fam[0]['Rembulitem']['montant'];
		                                 $allocation = (isset($fam[0]['Rembulitem']['montant']))?$fam[0]['Rembulitem']['montant']:0;

		                                  /**************Avoir2********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;
		                               
		                               
		                                /**************CNSS ********************************/
		                             	$ohm = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='400'"), 'recursive'=>0));
		                                $cnss = (isset($ohm[0]['Rembulitem']['montant']))?$ohm[0]['Rembulitem']['montant']:0;//$ohm[0]['Rembulitem']['montant'];
		                                /**************CARFO ********************************/
		                             	$retpub = $this->Rembulitem->find('all', array('conditions'=>array(
		                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='401'"), 'recursive'=>0));
		                                $carfo = (isset($retpub[0]['Rembulitem']['montant']))?$retpub[0]['Rembulitem']['montant']:0;//$retpub[0]['Rembulitem']['montant'];
		                                /******************************************************/
		                                $salairebrut = $salairebase + $indFonction + $indLogement + $indTransport + $indSujetion + $anciennete +  $allocation + $indGuichet + $indCaisse +  $avoir2;
		                               
		                                $salaireImposable = $salairebrut - $cnss -  $carfo;
		                                /*----------------------*/
		                                $netImposable = 0;
		                                $exoPartiel =0;
		                                $abat = 0;


		                               /* $logement = 0.2 *  $salaireImposable;$exoLogement = min($logement,75000,$indLogement);
		                                $fontion = 0.05 *  $salaireImposable;$exoFonction = min($fontion,50000,$indFonction + $indSujetion);
		                                $transport = 0.05 * $salaireImposable;$exoTransport = min($transport,30000,$indTransport);
		                                


		                                $exoPartiel = $exoLogement + $exoFonction + $exoTransport;*/
		                              
		                                /*---------------Abattement---------------------*/
		                              
		                                $phi = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                                $classification_id = $phi[0]['Agavencement']['paramclassification_id'];

		                               
		                                if($classification_id <= 9)
		                                {
		                                  $abat = 0.25 * ($salairebase + $anciennete);
		                                }
                                        else
		                                {
		                                   $abat = 0.2 * ($salairebase + $anciennete);
		                                }
		                               
		                                //$netImposable = $salaireImposable - ($exoPartiel + $abat);
		                                //$netImp = round($netImposable,-2);

		                                 $netImposable = $salaireImposable - $indLogement - $indTransport - $indFonction - $indSujetion - $abat;

		                                /*$netImposable = $salaireImposable - $indLogement - $indTransport - $indFonction - $indSujetion - $indGuichet - $indCaisse - $avoir2 - $abat;*/
		                                $netImp = round($netImposable,-2,PHP_ROUND_HALF_DOWN);
		                                /*---------------*/
		                                 $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1001,
									        'designation' => 'Net imposable',
										    'montant' => $netImp
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
							            //'montant' => round($netImposable, -2),

							            /*---------------*/
                                      /* SI($AA6<=30000;$AA6*0%;
                                       SI($AA6<=50000;0+($AA6-30000)*12,1%;
                                       SI($AA6<=80000;2420+($AA6-50000)*13,9%;
                                       SI($AA6<=120000;6590+($AA6-80000)*15,7%;
                                       SI($AA6<=170000;12870+($AA6-120000)*18,4%;
                                       SI($AA6<=250000;22070+($AA6-170000)*21,7%;

                                       	39430+($AA6-250000)*25%))))))*/

							            /*-------IUTS Brute--------------------*/
		                                $netImp = round($netImposable,-2,PHP_ROUND_HALF_DOWN);
		                                $iutsBrute = 0;

		                                switch($netImp){
									        case $netImp<=30000;
									            /*---------------------------------------*/
									            $iutsBrute = 0 * $netImp - 0;
							                    /*---------------------------------------*/
									            break;
									        case 30000<$netImp && $netImp<=50000;
									            /*---------------------------------------*/
									            $iutsBrute = 0 + (($netImp - 30000) *0.121);
					                            /*---------------------------------------*/
									            break;
									        case 50000 < $netImp && $netImp <= 80000;
									            /*---------------------------------------*/
									            $iutsBrute = 2420 + (($netImp - 50000) * 0.139);
									            /*---------------------------------------*/
									            break;
									        case 80000 < $netImp && $netImp <= 120000;
									            /*---------------------------------------*/
									            $iutsBrute = 6590 + (($netImp - 80000) * 0.157);
									            /*---------------------------------------*/
									            break;
									        case 120000 < $netImp && $netImp <=170000;
									            /*---------------------------------------*/
									            $iutsBrute = 12870 + (($netImp - 120000) * 0.184);
									            /*---------------------------------------*/
									            break;
									        case 170000 < $netImp && $netImp <= 250000;
									            /*---------------------------------------*/
									            $iutsBrute = 22070 + (($netImp - 170000) * 0.217);
									            /*---------------------------------------*/
									            break;
									        default: 
									             
                                                $iutsBrute = 39430 + (($netImp - 250000) * 0.25);
									            break;
							            }
							            /*-----------------IUTS NET DECUCTIBLE----------------*/
		                                $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

		                                /*==============================*/
		                               /* SI(D4=0;
		                                	0;
		                                	SI($D4=1;
		                                		$AB4*8%;
		                                		SI($D4=2;
		                                			$AB4*10%;
		                                			SI($D4=3;
		                                				$AB4*12%;
		                                				SI($D4=4;
		                                					$AB4*14%;
		                                					SI($D4=5;
		                                						$AB4*16%;
		                                						SI($D4=6;
		                                							$AB4*18%;
		                                							SI($D4>=7;
		                                								AB4*20%))))))))*/
		                                /*==============================*/
		                                $net_iuts = 0;
		                                $iutsDeductible = 0;

		                                if($nbcharge == 0){$iutsDeductible = 0;}
		                                if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
		                                if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
		                                if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
		                                if($nbcharge == 4){$iutsDeductible = $iutsBrute * 0.14;}
		                                if($nbcharge == 5){$iutsDeductible = $iutsBrute * 0.16;}
		                                if($nbcharge == 6){$iutsDeductible = $iutsBrute * 0.18;}
		                                if($nbcharge >= 7){$iutsDeductible = $iutsBrute * 0.20;}

		                               
		                               $net_iuts = $iutsBrute -  $iutsDeductible;
							          
							            $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$netImp,
										    'taux' =>'',
										    'montant' =>  $net_iuts,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
		                            }
		                            /*******************RETENUE PHARMACIE*************************/
		                            if($code == 29)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

		                                $affbontraite = $this->Affbontraite->find('all', array('conditions'=>array("Affbontraite.agdossier_id='$dossier'","Affbontraite.date_ret_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($affbontraite[0]['Affbontraite']['montant_ret_traite']))?$affbontraite[0]['Affbontraite']['montant_ret_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                           
		                        }
		                        
		                    
	                    }
	                }
	                /*++++++++++++++++BULLETIN NORMALE+++++++++++++++++++++++++++++++*/
	            }
                else
                {
                	$this->Session->setFlash('Selectionner un type de bulletin','flash notice'); 
                }    
                $saveId = true;
                $this->Session->setFlash('Les bulletins sont générés avec succès');
                $this->redirect(array('controller'=>'Rembulletins', 'view'=>'index'));
                }
		           //$dossier = $tmp[0]['Agcontrat']['agdossier_id'];
		          /* $saveId = $this->Rembulletin->save($postData);
						if($saveId){
							$log .= $saveId;
							$this->requestAction('Logs' ,'record', $log);
							$this->Session->setFlash('Enregistré avec succès');
							if($this->Session->check('return')){
					$this->redirect(array('controller'=>'Rembulletins', 'view'=>'index', 'params'=>array('contratid:'.$varcontrat)));
							}else{
								$this->redirect(array('controller'=>'Rembulletins', 'view'=>'index'));
							}
						}else {
						//Display Errors
						}
					}*/
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
				$this->data = $this->Rembulletin->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION BULLETIN GROUPE':'MODIFICATION BULLETIN GROUPE'));
		$this->set('toolbar', $toolbar);

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));
		$this->set('tauxprimebilans', $this->Paramtauxprimebilan->find('list', array('list'=>array('taux','taux'), 'order'=>'taux ASC')));
		
	}

    
	public function search() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE BULLETIN'));
		$this->set('toolbar', $toolbar);
	}
	
    /*-------------------------------*/
	public function index2() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		$postData = $this->postData();
		 $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01'; //$tmp[0]['Rembulletin']['date_debut'];
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; //$tmp[0]['Rembulletin']['date_fin'];
		$typesal = (isset($tmp[0]['Rembulletin']['paramtypesalaire_id']))?$tmp[0]['Rembulletin']['paramtypesalaire_id']:'1'; //$tmp[0]['Rembulletin']['paramtypesalaire_id'];
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
		
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'BULLETINS INDIVIDUELS<span class="pageTitle">'.$name . SEP . $username.'</span>');
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
		//$this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.statut'=>'INDIVIDUEL');
		$this->set('rembulletins', $this->paginate('Rembulletin'));
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));
		
	}
	
	/*controleur*/
	public function edit2() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
        $msg = '';
		$postData = $this->postData();
		if(isset($postData['Rembulletin']['submit']) && isset($postData['Rembulletin'])){
            if($postData['Rembulletin']['ag_dossier']<>'' &&
               $postData['Rembulletin']['date_debut']<>'' &&
			   $postData['Rembulletin']['date_fin']<>'' &&
			   $postData['Rembulletin']['paramtypesalaire_id']<>'')
            {
				
				/*********************************************************************/
				$log = ($this->getGetParam('id')?'Modification':'Creation') . ' Rembulletin ' . 'id: ';
				if($accessLevel['view'] && $accessLevel['edit']){
			    /*======================================*/
                //$contrats = $this->Agcontrat->find('all');
                $type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];
				if($type_bulletin == 1)
				{
				    /*+++++++++++++++++BULLETIN NORMALE+++++++++++++++++++++++++++++++*/
	                $contrats = $this->Agcontrat->find('all', array('conditions'=>array("Agcontrat.agdossier_id='{$postData['Rembulletin']['ag_dossier']}'","Agcontrat.statut ='1'"), 'recursive'=>0));
			
	                $i = 0;
	                foreach ($contrats as $index => $contrat){
	                	$hp = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.ag_dossier='{$postData['Rembulletin']['ag_dossier']}'","Rembulletin.paramtypesalaire_id='{$postData['Rembulletin']['paramtypesalaire_id']}'","Rembulletin.date_debut ='{$postData['Rembulletin']['date_debut']}'","Rembulletin.date_fin='{$postData['Rembulletin']['date_fin']}'"), 'recursive'=>0));
			            $nbr_bul = count($hp);
	                    if($nbr_bul > 0)
	                    {
	                      $msg = 'Bulletin existe dans la base';
	                    }
	                    else
	                    {
		                	$i++;
		                	/*-------------------------------------------*/
		                	$count = 0;
		                	$bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
				            $count = count($bulletins);

				            if($count > 0){$count = $count + 1;}else{$count = 1;}
		                	/*-------------------------------------------*/
		                	$agent = array('Rembulletin'=>array(
						        'num_bull' => $count,
						        'date_debut' => $postData['Rembulletin']['date_debut'],
						        'date_fin' => $postData['Rembulletin']['date_fin'],
							    'agcontrat_id' => $contrat['Agcontrat']['id'],
							    'num_contrat' => $contrat['Agcontrat']['num_contrat'],
							    'ag_dossier' => $contrat['Agcontrat']['agdossier_id'],
							    'matricule' => $contrat['Agcontrat']['matricule'],
							    'paramtypesalaire_id' => $postData['Rembulletin']['paramtypesalaire_id'],
							    
								   )
					            );
							    $this->Rembulletin->save($agent);

							    $bulID = $this->Rembulletin->id;

							    $periode_fin_bul = $postData['Rembulletin']['date_fin'];
		                        /*-----------------Contrat de l'agent-----------------------------------*/
		                        $cont = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $date_embauche = $cont[0]['Agcontrat']['date_debut'];
		                        $agdossier = $cont[0]['Agcontrat']['agdossier_id'];
		                        /*-----------------Indemnites/Avantages/Retenues de l'agent------------*/
		                        $indemnites = $this->Agindemnite->find('all', array('conditions'=>array('Agindemnite.agcontrat_id='.$contrat['Agcontrat']['id'])));
		                        
		                        $ind = $this->Paramindemnite->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC'));
		                        /*---------------Affectation ou Mutation de l'agent-------------------*/
		                         $affec = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $typefonction = $affec[0]['Agaffectmutation']['paramtypefonction_id'];
		                        /*-----------------------------------------------*/
		                        $base = 0;
		                        $montant = 0;
		                        foreach ($indemnites as $indemnite){
		                        	
		                        	$code_paramind =  $indemnite['Agindemnite']['code_paramind'];
		                        	
		                        	
		                        	

		                        	$ind = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.code ='.$code_paramind), 'recursive'=>0));
		                        	$identifiant = $ind[0]['Paramindemnite']['id'];
				                    $code = $ind[0]['Paramindemnite']['code'];
				                    $libelle = $ind[0]['Paramindemnite']['libelle'];
		                            $avoiret = $ind[0]['Paramindemnite']['paramavoiret_id'];
		                            //$type = $ind[0]['Paramindemnite']['paramtypefonction_id'];
		                            /*-----------AVANCEMENT-----------------------------------*/
		                            $av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                            $classification_id = $av[0]['Agavencement']['paramclassification_id'];
				                    $paramechelon_id = $av[0]['Agavencement']['paramechelon_id'];
				                    $year_anc = $av[0]['Agavencement']['anciennete'];

		                            /*---------TRAITEMENT DE BASE------------------------------*/
		                            if($code == 1)
		                            {
                                        $green = $this->Paramgrillesal->find('all', array('conditions'=>array("Paramgrillesal.paramclassification_id='{$classification_id}'","Paramgrillesal.paramechelon_id='{$paramechelon_id}'"), 'recursive'=>0));
		                                  
		                                $base = $green[0]['Paramgrillesal']['salaire'];
		                                
		                                $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$base,
										    'taux' =>30,
										    'montant' => $base,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
							           
		                            }
		                           
		                            /*******************INDEMNITE FONCTION*************************/
		                            if(($code == 3 && $typefonction == 1) ||
		                        	   ($code == 3 && $typefonction == 2) ||
		                               ($code == 3 && $typefonction == 3))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE LOGEMENT*************************/
		                            if($code == 4)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE TRANSPORT*************************/
		                            if($code == 5)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE SUJETION*************************/
		                            if($code == 6)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$montant,
									    'taux' =>30,
									    'montant' => $montant,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE CAISSE*************************/
		                            if(($code == 7 && $typefonction == 5) ||
		                        	   ($code == 7 && $typefonction == 7))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************ALLOCATION FAMILLIALE*************************/
		                            if($code == 8)
		                            {
		                            	$alloc = 0;
		                            	
		                            	$montant =  $indemnite['Agindemnite']['base_montant'];

		                                $pegaz = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $pegaz[0]['Agdossier']['ag_charge'];
                                        /*
		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];*/
		                               
		                                $alloc = $nbcharge * $montant;

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$alloc,
									    'montant' => $alloc,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
                                    /*******************CONGES MATERNITE******************/
		                            if($code == 10)
		                            {
		                            	
		                            	$conge_mat =  $indemnite['Agindemnite']['base_montant'];
		                              
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$conge_mat,
									    'montant' => $conge_mat,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************ANCIENNETE*************************/
		                            if($code == 11)
		                            {
		                            	$sal = $this->Paramgrillesal->find('all', array('conditions'=>array("Paramgrillesal.paramclassification_id='$classification_id'",
		                                    "Paramgrillesal.paramechelon_id='$paramechelon_id'"), 'recursive'=>0));
		                                $base = $sal[0]['Paramgrillesal']['salaire'];
		                        	    
		    						    //$anciennete = $year_anc * ($base * 0.01);
										#==========================
										$tmp_anc = $year_anc * ($base * 0.01);
										$anciennete = round($tmp_anc);
		                                #==========================
		                               
		                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$base,
										    'taux' =>$year_anc,
										    'montant' => $anciennete,
										    'avoir_ret' => $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
								        
		                            }
		                            /*******************HEURE SUP******************/
		                            if($code == 12)
		                            {
		                            	
		                            	$heure_sup =  $indemnite['Agindemnite']['base_montant'];
		                                

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>'',
									    'montant' => $heure_sup,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
                                    /*******************CONGES PAYES******************/
		                            if($code == 23)
		                            {
		                            	
		                            	$conge_paye =  $indemnite['Agindemnite']['base_montant'];
		                              
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$conge_paye,
									    'montant' => $conge_paye,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************RETENUE PHARMACIE*************************/
		                            if($code == 29)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

		                                $affbontraite = $this->Affbontraite->find('all', array('conditions'=>array("Affbontraite.agdossier_id='$dossier'","Affbontraite.date_ret_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($affbontraite[0]['Affbontraite']['montant_ret_traite']))?$affbontraite[0]['Affbontraite']['montant_ret_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                            /*******************AVANTAGE NATURE FONCTION******************/
		                            if($code == 31)
		                            {
		                            	
		                            	$fonction =  $indemnite['Agindemnite']['base_montant'];
		                                //$avg_nat_fct = ($fonction / 240);

										#=======================
										$tmp_avg_nat_fct =  ($fonction / 240);
										$avg_nat_fct = round($tmp_avg_nat_fct);
										#=======================


		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_fct,
									    'montant' => $avg_nat_fct,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVANTAGE NATURE LOGEMENT******************/
		                            if($code == 32)
		                            {
		                            	
		                            	$logement =  $indemnite['Agindemnite']['base_montant'];
		                                //$avg_nat_log = ($logement / 240);
                                        
										#=======================
										$tmp_avg_nat_log =  ($logement / 240);
										$avg_nat_log = round($tmp_avg_nat_log);
										#=======================

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_log,
									    'montant' => $avg_nat_log,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVANTAGE NATURE TRANSPORT******************/
		                            if($code == 33)
		                            {
		                            	
		                            	$transport =  $indemnite['Agindemnite']['base_montant'];
		                                //$avg_nat_trans = ($transport / 240);
                                        #=======================
										$tmp_avg_nat_trans =  ($transport / 240);
										$avg_nat_trans = round($tmp_avg_nat_trans);
										#=======================

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_trans,
									    'montant' => $avg_nat_trans,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVOIR 1*************************/
		                            if($code == 35)
		                            {
		                            	
		                            	$mnt_avoir1 =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir1,
									    'taux' =>'',
									    'montant' => $mnt_avoir1,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVOIR 2*************************/
		                            if($code == 36)
		                            {
		                            	
		                            	$mnt_avoir2 =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir2,
									    'taux' =>'',
									    'montant' => $mnt_avoir2,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVOIR AVANCEMENT*************************/
		                            if($code == 37)
		                            {
		                            	
		                            	$mnt_avoir_av =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir_av,
									    'taux' =>'',
									    'montant' => $mnt_avoir_av,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE GUICHET*************************/
		                           if(($code == 38 && $typefonction == 4)||
		                        	  ($code == 38 && $typefonction == 7))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$montant,
										    'taux' =>30,
										    'montant' => $montant,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVANCE SUR SALAIRE*************************/
		                            if($code == 98)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

	                                    $afftraite = $this->Afftraite->find('all', array('conditions'=>array("Afftraite.agdossier_id='{$dossier}'","Afftraite.date_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($afftraite[0]['Afftraite']['montant_traite']))?$afftraite[0]['Afftraite']['montant_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                            /******************SALAIRE BRUTE****************************/
		                            if($code == 500)
		                            {
		                            	
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
                                        $base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
	                                    $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:0;
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;
										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                $salaire_brute = 0;

		                                $salaire_brute = $trait_base + 
		                                               $indFonction +
		                                               $indLogement +
		                            	               $indTransport + 
		                            	               $indSujetion + 
		                            	               $anciennete +
		                             	               $allocation + 
		                             	               $indGuichet + 
		                             	               $indCaisse + 
		                             	               $avoir1 + 
		                             	               $avoir2 + 
		                             	               $avoirav  + 
		                             	               $avg_nat_trans +
		                             	               $avg_nat_fct + 
		                             	               $avg_nat_logement + 
		                             	               $sursalaire + 
		                             	               $heure_sup;
		                               
		                               /*--------------------*/
		                                $red = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'montant' => $salaire_brute,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($red);
		                            }
		                            /*******************CNSS*************************/
		                            if($code == 400)
		                            {
		                            	
		                            	$salairebrut = 0;
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
		                             	$base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
		                             	$fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:'';
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;

										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                               
		                                $salairebrut = $trait_base + $indFonction + $indLogement +
		                            	               $indTransport + $indSujetion + $anciennete +
		                             	               $allocation + $indGuichet + $indCaisse + 
		                             	               $avoir1 + $avoir2 + $avoirav  + $avg_nat_trans +
		                             	               $avg_nat_fct + $avg_nat_logement + $sursalaire + $heure_sup;

		                              
		                                $brute = 0.055 * $salairebrut;
		                                /*======5.5/100 salaire brute==========*/
						                $base = 0.08 * ($trait_base + $anciennete + $avoirav);
						                /*===============CNSS=========================*/
						                //$cnss = MIN($brute,$base,44000);
		                                /*--------------------*/
										 #=========================
										 $tmp_cnss = MIN($brute,$base,44000);
										
										 $cnss = round($tmp_cnss);
										 #=========================
		                                
		                                $ret_cnss = array('Rembulitem'=>array(
									        'rembulletin_id'=>$bulID,
									        'code'=>$code,
									        'designation'=>$libelle,
										    'base'=>$sal_brute,
										    'montant'=>$cnss,
										    'avoir_ret'=>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($ret_cnss);
		                            }
		                            /*******************CARFO*************************/
		                            if($code == 401)
		                            {
		                            	$carfo = 0;
		                            	$base_carfo =  $indemnite['Agindemnite']['base_montant'];
		                                

		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $taux = $mnt[0]['Paramindemitem']['taux'];
		                               
		                               // $carfo = $base_carfo * ($taux / 100);

										 #=========================
										 $tmp_carfo = $base_carfo * ($taux / 100);
										 $carfo = round($tmp_carfo);
										 #=========================

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$base_carfo,
									    'montant' => $carfo,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************IUTS*************************/
		                            if($code == 402)
		                            {
		                            	
		                            	$ret_cnss = 0;
		                            	$ret_carfo = 0;
		                            	$salaireImposable = 0;
                                        $exoPartiel =0;
                                        
                                        $heure_sup = 0;
		                            	

		                                $abat = 0;
		                                $salnetImposable = 0;
                                         $netImp = 0;

		                                $iutsBrute = 0;
		                                $iutsDeductible = 0;
                                        $net_iuts = 0;

		                                /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
		                                $salairebrut = 0;
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
		                             	$base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
		                             	$fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:'';
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                  /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;
										/**************HEURE SUPLEMENTAIRE *******************/
		                                $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                               
		                                $salairebrut = $trait_base + $indFonction + $indLogement +
		                            	               $indTransport + $indSujetion + $anciennete +
		                             	               $allocation + $indGuichet + $indCaisse + 
		                             	               $avoir1 + $avoir2 + $avoirav  + $avg_nat_trans +
		                             	               $avg_nat_fct + $avg_nat_logement + $sursalaire + $heure_sup;
		                                /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
		                                $ret_cnss_carfo = 0;
		                                $cotisation = $contrat['Agcontrat']['paramstructurecotsocial_id'];
		                                if(isset($cotisation) && $cotisation == 1)
		                                {
                                           /**************CNSS*******************/
				                           $white = $this->Rembulitem->find('all', array('conditions'=>array(
				                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='400'"), 'recursive'=>0));
									       $ret_cnss_carfo = (isset($white[0]['Rembulitem']['montant']))?$white[0]['Rembulitem']['montant']:0;
				                        }
		                                elseif(isset($cotisation) && $cotisation == 2)
		                                {
		                                   /**************CARFO*******************/
	                                       $yelow = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='401'"), 'recursive'=>0));
		                                   $ret_cnss_carfo = (isset($yelow[0]['Rembulitem']['montant']))?$yelow[0]['Rembulitem']['montant']:0;

		                                }else{$ret_cnss_carfo = 0;}
		                                
		                                /***************SALAIRE IMPOSABLE***********************/
		                                $salaireImposable = $salairebrut - $ret_cnss_carfo;


		                                $salImpo = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1000,
									        'designation' => 'Salaire imposable',
									        'montant' => $salaireImposable,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($salImpo);

		                                /***************Exoneration logement***********************/
		                                $logement = 0.2 *  $salaireImposable;
		                                $plafond_log = 75000;
                                        $indLog = $indLogement + $avg_nat_logement;

		                                $exoLogement = min($logement,$plafond_log,$indLog);

		                                $exo_log = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1002,
									        'designation' => 'Exoneration logement',
									        'montant' => $exoLogement,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_log);


                                        /***************Exoneration fonction***********************/
		                                $fonction = 0.05 *  $salaireImposable;
		                                $plafond_fct = 50000;
                                        $indFct = $indFonction +  $avg_nat_fct + 
                                                  $indSujetion + $indGuichet + $indCaisse;

		                                $exoFonction = min($fonction,$plafond_fct,$indFct);
                                        

							            $exo_fonct = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1004,
									        'designation' => 'Exoneration fonction',
									        'montant' => $exoFonction,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_fonct);

		                                /***************Exoneration transport***********************/
		                                $transport = 0.05 *  $salaireImposable;
		                                $plafond_transp = 30000;
                                        $indFct = $indTransport +  $avg_nat_trans;

		                                $exoTransport = min($transport,$plafond_transp,$indFct);
		                               
		                                $exo_trans = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1003,
									        'designation' => 'Exoneration transport',
									        'montant' => $exoTransport,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_trans);
                                        /**************EXONERATION PARTIEL************/

		                                $exoPartiel = $exoLogement + $exoFonction + $exoTransport;

                                        $exo_pat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1005,
									        'designation' => 'Exoneration partiel',
									        'montant' => $exoPartiel,
									        'avoir_ret' =>2
										    )
					                    );
					                    $this->Rembulitem->save($exo_pat);
		                              
		                                /*---------------Abattement---------------------*/
		                              
		                                $phi = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                                $classification_id = $phi[0]['Agavencement']['paramclassification_id'];

		                               
		                                if($classification_id <= 4)
		                                {
		                                  $abat = 0.25 * ($trait_base + $anciennete + $heure_sup + $avoirav);
		                                }
                                        else
		                                {
		                                   $abat = 0.2 * ($trait_base + $anciennete + $heure_sup + $avoirav);
		                                }

                                        
                                        
		                                $tmp_abat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1006,
									        'designation' => 'Abattement',
									        'montant' => $abat,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($tmp_abat);
		                               
		                                /*==================SALAIRE NET IMPOSABLE=====================*/

		                                $salnetImposable = $salaireImposable - $exoPartiel - $abat;

		                                $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);
		                               

							            $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1001,
									        'designation' => 'Salaire Net Imposable',
									        'montant' => $netImp,
									        'montant2' => $salnetImposable,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
		                                

							            /************* IUTS BRUTE**********************************/

		                                switch($netImp){
									        case $netImp<=30100;
									            /*---------------------------------------*/
									            //$iutsBrute = 0 * $netImp - 0;
									            $iutsBrute = $netImp;
							                    /*---------------------------------------*/
									            break;
									        case 30100<$netImp && $netImp<=50100;
									            /*---------------------------------------*/
									            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
									            $iutsBrute = 0 + (($netImp - 30100) *0.121);
					                            /*---------------------------------------*/
									            break;
									        case 50100 < $netImp && $netImp <= 80100;
									            /*---------------------------------------*/
									            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
									            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
									            /*---------------------------------------*/
									            break;
									        case 80100 < $netImp && $netImp <= 120100;
									            /*---------------------------------------*/
									            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
									            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
									            /*---------------------------------------*/
									            break;
									        case 120100 < $netImp && $netImp <=170100;
									            /*---------------------------------------*/
									            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
									            /*---------------------------------------*/
									            break;
									        case 170100 < $netImp && $netImp <= 250100;
									            /*---------------------------------------*/
									            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
									            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
									            /*---------------------------------------*/
									            break;
									        default: 
									             
                                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
									            break;
							            }

							            $iuts_brute = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 200,
									        'montant' => $iutsBrute,
									        'designation' => 'IUTS Brute',
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($iuts_brute);
							            


							           /************* IUTS DEDUCTIBLE*****************************/
                                      

							            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

		                                

		                                if($nbcharge == 0){$iutsDeductible = 0;}
		                                if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
		                                if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
		                                if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
		                                if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
		                              
		                               
							            $iuts_deduc = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 201,
									        'montant' => $iutsDeductible,
									        'designation' => 'IUTS Deductible',
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($iuts_deduc);
		                               
							           /************* IUTS NET************************************/
                                        //$net_iuts = $iutsBrute -  $iutsDeductible;
										#==========================
										$tmp_net_iuts = $iutsBrute -  $iutsDeductible;
										$net_iuts = round($tmp_net_iuts);
										#=========================
                                        $netIuts = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$netImp,
										    'taux' =>'',
										    'montant' =>  $net_iuts,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($netIuts);
							           /*********FIN*****UITS NET**********************************/
 
                                       /*************Impot sur salaire************************************/
                                        //$impot_salaire = $net_iuts +  $ret_cnss_carfo;
                                        #=====================================
										$tmp_impot_salaire = $net_iuts +  $ret_cnss_carfo;
										$impot_salaire = round($tmp_impot_salaire);
										#=====================================
                                         $impSal = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 501,
									        'designation' => 'Impot sur salaire',
									        'montant' => $impot_salaire,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($impSal);

							            /*************TPA************************************/
                                        //$tpa = $salairebrut * 0.03;
										#=======================
										$var_tpa = $salairebrut * 0.03;
										$tpa = round($var_tpa);
										#=======================

                                         $tmp_tpa = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 700,
									        'designation' => 'TPA',
									        'montant' => $tpa,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($tmp_tpa);

							            /*************CNSS Patronal************************************/
                                        $cnss_pat = $salairebrut * 0.215;

                                         $tmp_cnss_pat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 902,
									        'designation' => 'CNSS Patronal',
									        'montant' => $cnss_pat,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($tmp_cnss_pat);


                                      
		                            }
		                            /*******************Cotisation mtuelle*************************/
		                            if($code == 403)
		                            {
		                            	
		                            	$cot_mnt =  $indemnite['Agindemnite']['base_montant'];

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$cot_mnt,
									    'montant' => $cot_mnt,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AUTRE RETENUE******************/
		                            if($code == 1008)
		                            {
		                            	
		                            	$autre_ret =  $indemnite['Agindemnite']['base_montant'];
		                              

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$autre_ret,
									    'taux' => '30',
									    'montant' => $autre_ret,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                           
		         
		                       }
		                    $msg = 'Bulletin généré avec succès';          
	                    }
	                }
                    /*++++++++++++++++BULLETIN NORMALE+++++++++++++++++++++++++++++++*/
	            }
	            elseif($type_bulletin == 2)
	            {
	                  /*++++++++++++++++GRATIFICATION OU 13E MOIS+++++++++++++++++++++++++++++++*/
	                  $ag_dossier = $postData['Rembulletin']['ag_dossier'];
	                  $date_debut = $postData['Rembulletin']['date_debut'];
	                  $date_fin = $postData['Rembulletin']['date_fin'];
	                  $type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];

	                 

	                    $decembre = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.ag_dossier='{$ag_dossier}'","Rembulletin.paramtypesalaire_id='1'","Rembulletin.date_debut ='$date_debut'","Rembulletin.date_fin='{$date_fin}'"), 'recursive'=>0));
	                    $bulletinid = $decembre[0]['Rembulletin']['id'];
			            $nbr_bul = count($decembre);
		                if($nbr_bul > 0)
		                {
		                  /*-------------------------------------------*/
			              $count = 0;
			              $bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
					      $count = count($bulletins);
	                      if($count > 0){$count = $count + 1;}else{$count = 1;}
		                  /*--------------ENREGISTREMENT BULLETIN-----------------------------*/
		                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
			              $contrat_id = $contrat[0]['Agcontrat']['id'];
			              $num_contrat = $contrat[0]['Agcontrat']['num_contrat'];
			              $matricule_ag = $contrat[0]['Agcontrat']['matricule'];


		                  $agent = array('Rembulletin'=>array(
					        'num_bull' => $count,
					        'date_debut' => $date_debut,
					        'date_fin' => $date_fin,
						    'agcontrat_id' => $contrat_id,
						    'num_contrat' => $num_contrat,
						    'ag_dossier' => $ag_dossier,
						    'matricule' => $matricule_ag,
						    'paramtypesalaire_id' => $type_bulletin,
						    
							   )
				            );
						    $this->Rembulletin->save($agent);

						    $bul_id = $this->Rembulletin->id;
		                  
		                  /*--------------Salaire de base gratification------------------------------*/
		                  $grat = $this->Paramsalbasegrat->find('all', array('conditions'=>array("Paramsalbasegrat.agdossier_id='{$ag_dossier}'"), 'recursive'=>0));
		                  $sal_base = (isset($grat[0]['Paramsalbasegrat']['salaire_base_grat']))?$grat[0]['Paramsalbasegrat']['salaire_base_grat']:0;

		                  $item = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 1,
					        'designation' => 'Traitement de base',
						    'base' =>$sal_base,
						    'taux' =>30,
						    'montant' => $sal_base,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($item);
		                 
		                  /*-----------Traitement de base normale-------------*/
		                  $b = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='1'"), 'recursive'=>0));
		                  $base = (isset($b[0]['Rembulitem']['montant']))?$b[0]['Rembulitem']['montant']:0;

		                  /*-----------Fonction-------------*/
		                  $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='3'"), 'recursive'=>0));
		                  $fonction = (isset($fct[0]['Rembulitem']['montant']))?$fct[0]['Rembulitem']['montant']:0;

	                       $fct_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 3,
					        'designation' => 'Indemnite de fonction',
						    'base' =>$fonction,
						    'taux' =>30,
						    'montant' => $fonction,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($fct_ind);

		                   /*-----------Logement-------------*/
		                   $log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='4'"), 'recursive'=>0));
		                  $logement =  (isset($log[0]['Rembulitem']['montant']))?$log[0]['Rembulitem']['montant']:0;

		                   $log_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 4,
					        'designation' => 'Indemnite de logement',
						    'base' =>$logement,
						    'taux' =>30,
						    'montant' => $logement,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($log_ind);
		                   /*-----------Transport-------------*/
		                  $trp = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='5'"), 'recursive'=>0));
		                  $transport = (isset($trp[0]['Rembulitem']['montant']))?$trp[0]['Rembulitem']['montant']:0;

		                  $tpr_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 5,
					        'designation' => 'Indemnite de transport',
						    'base' =>$transport,
						    'taux' =>30,
						    'montant' => $transport,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($tpr_ind);
		                   /*-----------Sujetion-------------*/
		                  $suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='6'"), 'recursive'=>0));
		                  $sujetion = (isset($suj[0]['Rembulitem']['montant']))?$suj[0]['Rembulitem']['montant']:0;

	                      $suj_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 6,
					        'designation' => 'Indemnite de sujetion',
						    'base' =>$sujetion,
						    'taux' =>30,
						    'montant' => $sujetion,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($suj_ind);

			               /*---------------------Anciennete------------------------------*/
		                  $anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='11'"), 'recursive'=>0));
		                  $taux = (isset($anc[0]['Rembulitem']['taux']))?$anc[0]['Rembulitem']['taux']:0;

		                  $anciennete = $sal_base * $taux * 0.01;

		                   $prime_anc = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 11,
					        'designation' => 'Anciennete',
						    'base' =>$sal_base,
						    'taux' =>30,
						    'montant' => $anciennete,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($prime_anc);

		                  /*-----------Caisse-------------*/
		                  $cai = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='7'"), 'recursive'=>0));
		                  $caisse = (isset($cai[0]['Rembulitem']['montant']))?$cai[0]['Rembulitem']['montant']:0;

		                  $cais_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 7,
					        'designation' => 'Indemnite de caisse',
						    'base' =>$caisse,
						    'taux' =>30,
						    'montant' => $caisse,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($cais_ind);

		                  /*-----------Allocation-------------*/
		                  $alc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='8'"), 'recursive'=>0));
		                  $allocation = (isset($alc[0]['Rembulitem']['montant']))?$alc[0]['Rembulitem']['montant']:0;

		                   $alloc_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 8,
					        'designation' => 'Allocation familiale',
						    'base' =>$allocation,
						    'taux' =>30,
						    'montant' => $allocation,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($alloc_ind);
		                  /*-----------Avantage nature logement-------------*/
		                  $anle = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='32'"), 'recursive'=>0));
		                  $av_nat_log= (isset($anle[0]['Rembulitem']['montant']))?$anle[0]['Rembulitem']['montant']:0;

		                  $nat_log = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 32,
					        'designation' => 'Avantage nature logement',
						    'base' =>$av_nat_log,
						    'taux' =>30,
						    'montant' => $av_nat_log,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($nat_log);
		                  /*-----------Avantage nature transport-------------*/
		                  $ant = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='33'"), 'recursive'=>0));
		                  $av_nat_trans = (isset($ant[0]['Rembulitem']['montant']))?$ant[0]['Rembulitem']['montant']:0;

		                  $nat_tranp = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 33,
					        'designation' => 'Avantage nature transport',
						    'base' =>$av_nat_trans,
						    'taux' =>30,
						    'montant' => $av_nat_trans,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($nat_tranp);
		                  /*-----------Guichet-------------*/
		                  $gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='38'"), 'recursive'=>0));
		                  $guichet = (isset($gui[0]['Rembulitem']['montant']))?$gui[0]['Rembulitem']['montant']:0;

		                  $gui_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 38,
					        'designation' => 'Indemnite de guichet',
						    'base' =>$guichet,
						    'taux' =>30,
						    'montant' => $guichet,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($gui_ind);
		                  
		                  /*----------Salaire brute---------------------------*/
	                      $salairebrut = $sal_base + $anciennete + $fonction + $logement + $transport + $sujetion + $caisse + $allocation + $av_nat_log + $av_nat_trans + $guichet;


	                      $sal_brute = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 500,
					        'designation' => 'Salaire Brute',
						    'base' =>$salairebrut,
						    'taux' =>30,
						    'montant' => $salairebrut,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($sal_brute);


	                      /*----------Cotisation sociale----------------------*/
		                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
			              $cotisation = $contrat[0]['Agcontrat']['paramstructurecotsocial_id'];
	                      
	                      /**************CNSS*******************/
	                      $cnss_gratif = 0;
	                       $txt = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='400'"), 'recursive'=>0));
			               $cnss_normal = $txt[0]['Rembulitem']['montant'];

			                if(isset($cnss_normal) && $cnss_normal >= 44000)
			                  {
			                  	$cnss_gratif = 0;
			                  }
			                  else
			                  {
			                  	$cnss_gratif = 44000 - $cnss_normal;
			                  }

	                      $ret_cnss_carfo = 0;
		                  if(isset($cotisation) && $cotisation == 1)
		                  {
		                     /**************CNSS*******************/
			                 $ret_cnss_carfo =  $cnss_gratif;

				             $cnss = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 400,
						        'designation' => 'CNSS',
							    'base' =>$sal_base,
							    'taux' =>30,
							    'montant' => $ret_cnss_carfo,
							    'avoir_ret' =>2
						      )
	                           );
			                  $this->Rembulitem->save($cnss);
			               }
			               elseif(isset($cotisation) && $cotisation == 2)
		                   {
		                      /**************CARFO*******************/
		                      $ret_cnss_carfo = 0;   

		                      $carfo = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 401,
						        'designation' => 'CARFO',
							    'base' =>$sal_base,
							    'taux' =>30,
							    'montant' => $ret_cnss_carfo,
							    'avoir_ret' =>2
						      )
	                           );
			                  $this->Rembulitem->save($carfo);

		                    }else{$ret_cnss_carfo = 0;}
		                    /*----------Fin Cotisation sociale--------------------*/
		                    
	                        /***************SALAIRE IMPOSABLE***********************/
	                        $salaireImposable = $salairebrut - $ret_cnss_carfo;

	                        $salImpo = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 1000,
						        'designation' => 'Salaire imposable',
						        'montant' => $salaireImposable,
						        'avoir_ret' =>2
							    )
		                    );
				            $this->Rembulitem->save($salImpo);
	                        /*==================SALAIRE NET IMPOSABLE=====================*/

	                        $salnetImposable = $salaireImposable;

	                        $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);

				            $sal_net_imp = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 1001,
						        'designation' => 'Salaire Net Imposable',
						        'montant' => $netImp,
						        'montant2' => $salnetImposable,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($sal_net_imp);
		                        
					        /************* IUTS BRUTE**********************************/

	                        switch($netImp){
						        case $netImp<=30100;
						            /*---------------------------------------*/
						            //$iutsBrute = 0 * $netImp - 0;
						            $iutsBrute = $netImp;
				                    /*---------------------------------------*/
						            break;
						        case 30100<$netImp && $netImp<=50100;
						            /*---------------------------------------*/
						            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
						            $iutsBrute = 0 + (($netImp - 30100) *0.121);
		                            /*---------------------------------------*/
						            break;
						        case 50100 < $netImp && $netImp <= 80100;
						            /*---------------------------------------*/
						            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
						            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
						            /*---------------------------------------*/
						            break;
						        case 80100 < $netImp && $netImp <= 120100;
						            /*---------------------------------------*/
						            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
						            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
						            /*---------------------------------------*/
						            break;
						        case 120100 < $netImp && $netImp <=170100;
						            /*---------------------------------------*/
						            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
						            /*---------------------------------------*/
						            break;
						        case 170100 < $netImp && $netImp <= 250100;
						            /*---------------------------------------*/
						            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
						            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
						            /*---------------------------------------*/
						            break;
						        default: 
						             
	                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
						            break;
				            }
				            $iuts_brute = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 200,
						        'montant' => $iutsBrute,
						        'designation' => 'IUTS Brute',
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($iuts_brute);
					        /************* IUTS DEDUCTIBLE*****************************/

				            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$ag_dossier), 'recursive'=>0));
	                        $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

	                        

	                        if($nbcharge == 0){$iutsDeductible = 0;}
	                        if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
	                        if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
	                        if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
	                        if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
	                      
	                       
				            $iuts_deduc = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 201,
						        'montant' => $iutsDeductible,
						        'designation' => 'IUTS Deductible',
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($iuts_deduc);
	                       
				           /************* IUTS NET************************************/
	                        $net_iuts = $iutsBrute -  $iutsDeductible;
	                        
	                        $sal_net = $salairebrut - $ret_cnss_carfo - $net_iuts;

	                        $msg = 'BRUTE '.$salairebrut.' CNSS '.$ret_cnss_carfo.' Salaire imposable '.$salaireImposable.' Salaire netimposable '.$netImp.' IUTS BRUTE '.$iutsBrute.' IUTS Decductible '.$iutsDeductible.' IUTS NET '.$net_iuts.' Salaire NET '.$sal_net;
	                        //print_r('IUTS NET '.$net_iuts);
	                       $netIuts = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => '402',
						        'designation' => 'IUTS',
							    'base' =>$netImp,
							    'taux' =>'',
							    'montant' =>  $net_iuts,
							    'avoir_ret' =>2
							    )
							    
		                    );
				            $this->Rembulitem->save($netIuts);
				           /*********FIN*****UITS NET**********************************/

	                       /*************Impot sur salaire************************************/
	                       $impot_salaire = $net_iuts +  $ret_cnss_carfo;

	                         $impSal = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 501,
						        'designation' => 'Impot sur salaire',
						        'montant' => $impot_salaire,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($impSal);

				            /*************TPA************************************/
	                         $tpa = $salairebrut * 0.03;

	                         $tmp_tpa = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 700,
						        'designation' => 'TPA',
						        'montant' => $tpa,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($tmp_tpa);

				            /*************CNSS Patronal************************************/
	                        $cnss_pat = $salairebrut * 0.215;

	                         $tmp_cnss_pat = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 902,
						        'designation' => 'CNSS Patronal',
						        'montant' => $cnss_pat,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($tmp_cnss_pat);
		                    
		                }
		                else
		                {
		                	$msg = 'Le bulletin du mois de décembre n\'existe pas dans la base, calculer au préalable le bulletin du mois de décembre';
		                }
	    			  
	               /*++++++++++++++++FIN GRATIFICATION OU 13E MOIS+++++++++++++++++++++++++++*/
                }
                elseif($type_bulletin == 3)
	            {
	                  /*++++++++++++++++PRIME DE BILAN+++++++++++++++++++++++++++++++*/
	                  
	                  $ag_dossier = $postData['Rembulletin']['ag_dossier'];
	                  $date_debut = $postData['Rembulletin']['date_debut'];
	                  $date_fin = $postData['Rembulletin']['date_fin'];
	                  $type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];
	                  $taux_prime = $postData['Rembulletin']['taux_prime'];

	                $year = date(Y) - 1;
                    $begin = $year.'-12-01';
                    $end = $year.'-12-31';

	               $contrats = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.paramtypesalaire_id='2'","Rembulletin.ag_dossier ='{$ag_dossier}'","Rembulletin.date_debut ='{$begin}'","Rembulletin.date_fin ='{$end}'",), 'recursive'=>0));
                   
	                $cpt = count($contrats);
	                if($cpt > 0)
	                {
                        
	                    $decembre = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.ag_dossier='{$ag_dossier}'","Rembulletin.paramtypesalaire_id='2'","Rembulletin.date_debut ='$begin'","Rembulletin.date_fin='{$end}'"), 'recursive'=>0));
	                    $bulletinid = $decembre[0]['Rembulletin']['id'];

			            $nbr_bul = count($decembre);
		                if($nbr_bul > 0)
		                {
		                  /*-------------------------------------------*/
			              $count = 0;
			              $bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
					      $count = count($bulletins);
	                      if($count > 0){$count = $count + 1;}else{$count = 1;}
		                  /*--------------ENREGISTREMENT BULLETIN-----------------------------*/
		                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
			              $contrat_id = $contrat[0]['Agcontrat']['id'];
			              $num_contrat = $contrat[0]['Agcontrat']['num_contrat'];
			              $matricule_ag = $contrat[0]['Agcontrat']['matricule'];


		                  $agent = array('Rembulletin'=>array(
					        'num_bull' => $count,
					        'date_debut' => $date_debut,
					        'date_fin' => $date_fin,
						    'agcontrat_id' => $contrat_id,
						    'num_contrat' => $num_contrat,
						    'ag_dossier' => $ag_dossier,
						    'matricule' => $matricule_ag,
						    'paramtypesalaire_id' => $type_bulletin,
						    
							   )
				            );
						    $this->Rembulletin->save($agent);

						    $bul_id = $this->Rembulletin->id;
		                  
		                  /*--------------Salaire de base gratification------------------------------*/
		                  $grat = $this->Paramsalbasegrat->find('all', array('conditions'=>array("Paramsalbasegrat.agdossier_id='{$ag_dossier}'"), 'recursive'=>0));
		                  $sal_base = (isset($grat[0]['Paramsalbasegrat']['salaire_base_grat']))?$grat[0]['Paramsalbasegrat']['salaire_base_grat']:0;

                          $traitement_base = ($sal_base * $taux_prime) / 100;
		                  /*-----------------*/

		                  $item = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 1,
					        'designation' => 'Traitement de base',
						    'base' =>$traitement_base,
						    'taux' =>$taux_prime,
						    'montant' => $sal_base,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($item);
		                 
		                  /*-----------Traitement de base normale-------------*/
		                  $b = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='1'"), 'recursive'=>0));
		                  $base = (isset($b[0]['Rembulitem']['montant']))?$b[0]['Rembulitem']['montant']:0;

		                  /*-----------Fonction-------------*/
		                  $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='3'"), 'recursive'=>0));
		                  $fonction = (isset($fct[0]['Rembulitem']['montant']))?$fct[0]['Rembulitem']['montant']:0;

	                       $fct_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 3,
					        'designation' => 'Indemnite de fonction',
						    'base' =>$fonction,
						    'taux' =>30,
						    'montant' => $fonction,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($fct_ind);

		                   /*-----------Logement-------------*/
		                   $log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='4'"), 'recursive'=>0));
		                  $logement =  (isset($log[0]['Rembulitem']['montant']))?$log[0]['Rembulitem']['montant']:0;

		                   $log_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 4,
					        'designation' => 'Indemnite de logement',
						    'base' =>$logement,
						    'taux' =>30,
						    'montant' => $logement,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($log_ind);
		                   /*-----------Transport-------------*/
		                  $trp = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='5'"), 'recursive'=>0));
		                  $transport = (isset($trp[0]['Rembulitem']['montant']))?$trp[0]['Rembulitem']['montant']:0;

		                  $tpr_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 5,
					        'designation' => 'Indemnite de transport',
						    'base' =>$transport,
						    'taux' =>30,
						    'montant' => $transport,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($tpr_ind);
		                   /*-----------Sujetion-------------*/
		                  $suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='6'"), 'recursive'=>0));
		                  $sujetion = (isset($suj[0]['Rembulitem']['montant']))?$suj[0]['Rembulitem']['montant']:0;

	                      $suj_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 6,
					        'designation' => 'Indemnite de sujetion',
						    'base' =>$sujetion,
						    'taux' =>30,
						    'montant' => $sujetion,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($suj_ind);

			               /*---------------------Anciennete------------------------------*/

                           $anciennete = 0;

	                       $anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='11'"), 'recursive'=>0));
			               $anciennete = $anc[0]['Rembulitem']['montant'];


		                   $traitement_base = ($sal_base * $taux_prime) / 100;

		                   $prime_anc = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 11,
					        'designation' => 'Anciennete',
						    'base' =>$traitement_base,
						    'taux' =>30,
						    'montant' => $anciennete,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($prime_anc);

		                  /*-----------Caisse-------------*/
		                  $cai = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='7'"), 'recursive'=>0));
		                  $caisse = (isset($cai[0]['Rembulitem']['montant']))?$cai[0]['Rembulitem']['montant']:0;

		                  $cais_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 7,
					        'designation' => 'Indemnite de caisse',
						    'base' =>$caisse,
						    'taux' =>30,
						    'montant' => $caisse,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($cais_ind);

		                  /*-----------Allocation-------------*/
		                  $alc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='8'"), 'recursive'=>0));
		                  $allocation = (isset($alc[0]['Rembulitem']['montant']))?$alc[0]['Rembulitem']['montant']:0;

		                   $alloc_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 8,
					        'designation' => 'Allocation familiale',
						    'base' =>$allocation,
						    'taux' =>30,
						    'montant' => $allocation,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($alloc_ind);
		                  /*-----------Avantage nature logement-------------*/
		                  $anle = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='32'"), 'recursive'=>0));
		                  $av_nat_log= (isset($anle[0]['Rembulitem']['montant']))?$anle[0]['Rembulitem']['montant']:0;

		                  $nat_log = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 32,
					        'designation' => 'Avantage nature logement',
						    'base' =>$av_nat_log,
						    'taux' =>30,
						    'montant' => $av_nat_log,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($nat_log);
		                  /*-----------Avantage nature transport-------------*/
		                  $ant = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='33'"), 'recursive'=>0));
		                  $av_nat_trans = (isset($ant[0]['Rembulitem']['montant']))?$ant[0]['Rembulitem']['montant']:0;

		                  $nat_tranp = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 33,
					        'designation' => 'Avantage nature transport',
						    'base' =>$av_nat_trans,
						    'taux' =>30,
						    'montant' => $av_nat_trans,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($nat_tranp);
		                  /*-----------Guichet-------------*/
		                  $gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='38'"), 'recursive'=>0));
		                  $guichet = (isset($gui[0]['Rembulitem']['montant']))?$gui[0]['Rembulitem']['montant']:0;

		                  $gui_ind = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 38,
					        'designation' => 'Indemnite de guichet',
						    'base' =>$guichet,
						    'taux' =>30,
						    'montant' => $guichet,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($gui_ind);
		                  
		                  /*----------Salaire brute---------------------------*/
		                  $traitement_base = ($sal_base * $taux_prime) / 100;
		                  /*------------------*/
	                      $salairebrut = $traitement_base + $anciennete + $fonction + $logement + $transport + $sujetion + $caisse + $allocation + $av_nat_log + $av_nat_trans + $guichet;


	                      $sal_brute = array('Rembulitem'=>array(
					        'rembulletin_id' => $bul_id,
					        'code' => 500,
					        'designation' => 'Salaire Brute',
						    'base' =>$salairebrut,
						    'taux' =>30,
						    'montant' => $salairebrut,
						    'avoir_ret' =>1
						   )
	                       );
			               $this->Rembulitem->save($sal_brute);


	                      /*----------Cotisation sociale----------------------*/
		                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
			              $cotisation = $contrat[0]['Agcontrat']['paramstructurecotsocial_id'];
			             
			               /*******************AUTRE RETENUE******************/  
                           $indemnites = $this->Agindemnite->find('all', array('conditions'=>array("Agindemnite.agcontrat_id='$contrat_id'","Agindemnite.code_paramind='1008'")));
                             $mnt_retenue =  $indemnites[0]['Agindemnite']['base_montant'];
                          
                        	$retenue = array('Rembulitem'=>array(
					            'rembulletin_id' => $bul_id,
						        'code' => 1008,
						        'designation' => 'Autre retenue',
							    'base' =>$traitement_base,
							    'taux' =>30,
							    'montant' => $mnt_retenue,
							    'avoir_ret' =>2
						          )
			                    );
					        $this->Rembulitem->save($retenue);
		                          
				            /*---------Fin AUTRE RETENUE---------*/
	                      
	                       /**************CNSS*******************/
	                       $cnss_prime = 0;


	                       /*$gratif = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='400'"), 'recursive'=>0));
			               $cnss_gratif = $gratif[0]['Rembulitem']['montant'];
			              

			               if($cnss_gratif == 0)
			               {
			               	  $cnss_prime = 0;
			               }

			               if($cnss_gratif > 0 && $cnss_gratif <= 33000)
			               {
			               	  $cnss_prime = 33000 - $cnss_gratif;
			               }
			               else{ $cnss_prime = 0; }*/

	                      
	                      $ret_cnss_carfo = 0;

	                       /*---------------------------*/
		                  $traitement_base = ($sal_base * $taux_prime) / 100;
		                  /*------------------*/

		                  if(isset($cotisation) && $cotisation == 1)
		                  {
		                     /**************CNSS*******************/
			                 $ret_cnss_carfo =  $cnss_prime;

				             $cnss = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 400,
						        'designation' => 'CNSS',
							    'base' =>$traitement_base,
							    'taux' =>30,
							    'montant' => $ret_cnss_carfo,
							    'avoir_ret' =>2
						      )
	                           );
			                  $this->Rembulitem->save($cnss);
			               }
			               elseif(isset($cotisation) && $cotisation == 2)
		                   {
		                      /**************CARFO*******************/
		                      $ret_cnss_carfo = 0;   

		                      $carfo = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 401,
						        'designation' => 'CARFO',
							    'base' =>$traitement_base,
							    'taux' =>30,
							    'montant' => $ret_cnss_carfo,
							    'avoir_ret' =>2
						      )
	                           );
			                  $this->Rembulitem->save($carfo);

		                    }else{$ret_cnss_carfo = 0;}
		                    /*----------Fin Cotisation sociale--------------------*/
		                    
	                        /***************SALAIRE IMPOSABLE***********************/
	                        $salaireImposable = $salairebrut - $ret_cnss_carfo;

	                        $salImpo = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 1000,
						        'designation' => 'Salaire imposable',
						        'montant' => $salaireImposable,
						        'avoir_ret' =>2
							    )
		                    );
				            $this->Rembulitem->save($salImpo);
	                        /*==================SALAIRE NET IMPOSABLE=====================*/

	                        $salnetImposable = $salaireImposable;

	                        $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);

				            $sal_net_imp = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 1001,
						        'designation' => 'Salaire Net Imposable',
						        'montant' => $netImp,
						        'montant2' => $salnetImposable,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($sal_net_imp);
		                        
					        /************* IUTS BRUTE**********************************/

	                        switch($netImp){
						        case $netImp<=30100;
						            /*---------------------------------------*/
						            //$iutsBrute = 0 * $netImp - 0;
						            $iutsBrute = $netImp;
				                    /*---------------------------------------*/
						            break;
						        case 30100<$netImp && $netImp<=50100;
						            /*---------------------------------------*/
						            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
						            $iutsBrute = 0 + (($netImp - 30100) *0.121);
		                            /*---------------------------------------*/
						            break;
						        case 50100 < $netImp && $netImp <= 80100;
						            /*---------------------------------------*/
						            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
						            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
						            /*---------------------------------------*/
						            break;
						        case 80100 < $netImp && $netImp <= 120100;
						            /*---------------------------------------*/
						            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
						            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
						            /*---------------------------------------*/
						            break;
						        case 120100 < $netImp && $netImp <=170100;
						            /*---------------------------------------*/
						            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
						            /*---------------------------------------*/
						            break;
						        case 170100 < $netImp && $netImp <= 250100;
						            /*---------------------------------------*/
						            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
						            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
						            /*---------------------------------------*/
						            break;
						        default: 
						             
	                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
						            break;
				            }
				            $iuts_brute = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 200,
						        'montant' => $iutsBrute,
						        'designation' => 'IUTS Brute',
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($iuts_brute);
					        /************* IUTS DEDUCTIBLE*****************************/

				            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$ag_dossier), 'recursive'=>0));
	                        $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

	                        

	                        if($nbcharge == 0){$iutsDeductible = 0;}
	                        if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
	                        if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
	                        if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
	                        if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
	                      
	                       
				            $iuts_deduc = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 201,
						        'montant' => $iutsDeductible,
						        'designation' => 'IUTS Deductible',
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($iuts_deduc);
	                       
				           /************* IUTS NET************************************/
	                        $net_iuts = $iutsBrute -  $iutsDeductible;
	                        
	                        $sal_net = $salairebrut - $ret_cnss_carfo - $net_iuts;

	                        $msg = 'BRUTE '.$salairebrut.' CNSS '.$ret_cnss_carfo.' Salaire imposable '.$salaireImposable.' Salaire netimposable '.$netImp.' IUTS BRUTE '.$iutsBrute.' IUTS Decductible '.$iutsDeductible.' IUTS NET '.$net_iuts.' Salaire NET '.$sal_net;
	                        //print_r('IUTS NET '.$net_iuts);
	                       $netIuts = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => '402',
						        'designation' => 'IUTS',
							    'base' =>$netImp,
							    'taux' =>'',
							    'montant' =>  $net_iuts,
							    'avoir_ret' =>2
							    )
							    
		                    );
				            $this->Rembulitem->save($netIuts);
				           /*********FIN*****UITS NET**********************************/

	                       /*************Impot sur salaire************************************/
	                       $impot_salaire = $net_iuts +  $ret_cnss_carfo;

	                         $impSal = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 501,
						        'designation' => 'Impot sur salaire',
						        'montant' => $impot_salaire,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($impSal);

				            /*************TPA************************************/
	                         $tpa = $salairebrut * 0.03;

	                         $tmp_tpa = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 700,
						        'designation' => 'TPA',
						        'montant' => $tpa,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($tmp_tpa);

				            /*************CNSS Patronal************************************/
	                        $cnss_pat = $salairebrut * 0.215;

	                         $tmp_cnss_pat = array('Rembulitem'=>array(
						        'rembulletin_id' => $bul_id,
						        'code' => 902,
						        'designation' => 'CNSS Patronal',
						        'montant' => $cnss_pat,
						        'avoir_ret' =>2
							    
							    )
							    
		                    );
				            $this->Rembulitem->save($tmp_cnss_pat);
		                    
		                }
		                else
		                {
		                	$msg = 'Le bulletin du mois de décembre n\'existe pas dans la base, calculer au préalable le bulletin du mois de décembre';
		                }
	    				
	                }
		            else
		            {
		            	$msg = 'La gratification du mois de décembre n\'existe pas dans la base, calculer au préalable le gratification du mois de décembre';
		            } 
	               /*++++++++++++++++FIN PRIME DE BILAN+++++++++++++++++++++++++++*/
                }
                elseif($type_bulletin == 4)
				{
				   $duree = $postData['Rembulletin']['duree'];
				   //print_r($duree);

				   /*++++++++++++++++BULLETIN SPECIALE OU PORATA+++++++++++++++++++++++++++++++*/
	                $contrats = $this->Agcontrat->find('all', array('conditions'=>array("Agcontrat.agdossier_id='{$postData['Rembulletin']['ag_dossier']}'","Agcontrat.statut ='1'"), 'recursive'=>0));
			
	                $i = 0;
	                foreach ($contrats as $index => $contrat){
	                	$hp = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.ag_dossier='{$postData['Rembulletin']['ag_dossier']}'","Rembulletin.paramtypesalaire_id='{$postData['Rembulletin']['paramtypesalaire_id']}'","Rembulletin.date_debut ='{$postData['Rembulletin']['date_debut']}'","Rembulletin.date_fin='{$postData['Rembulletin']['date_fin']}'"), 'recursive'=>0));
			            $nbr_bul = count($hp);
	                    if($nbr_bul > 0)
	                    {
	                      $msg = 'Bulletin existe dans la base';
	                    }
	                    else
	                    {
		                	$i++;
		                	/*-------------------------------------------*/
		                	$count = 0;
		                	$bulletins = $this->Rembulletin->find('all', array('recursive'=>0));
				            $count = count($bulletins);

				            if($count > 0){$count = $count + 1;}else{$count = 1;}
		                	/*-------------------------------------------*/
		                	$agent = array('Rembulletin'=>array(
						        'num_bull' => $count,
						        'date_debut' => $postData['Rembulletin']['date_debut'],
						        'date_fin' => $postData['Rembulletin']['date_fin'],
							    'agcontrat_id' => $contrat['Agcontrat']['id'],
							    'num_contrat' => $contrat['Agcontrat']['num_contrat'],
							    'ag_dossier' => $contrat['Agcontrat']['agdossier_id'],
							    'matricule' => $contrat['Agcontrat']['matricule'],
							    'paramtypesalaire_id' => $postData['Rembulletin']['paramtypesalaire_id'],
							    
								   )
					            );
							    $this->Rembulletin->save($agent);

							    $bulID = $this->Rembulletin->id;

							    $periode_fin_bul = $postData['Rembulletin']['date_fin'];
		                        /*-----------------Contrat de l'agent-----------------------------------*/
		                        $cont = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $date_embauche = $cont[0]['Agcontrat']['date_debut'];
		                        $agdossier = $cont[0]['Agcontrat']['agdossier_id'];
		                        /*-----------------Indemnites/Avantages/Retenues de l'agent------------*/
		                        $indemnites = $this->Agindemnite->find('all', array('conditions'=>array('Agindemnite.agcontrat_id='.$contrat['Agcontrat']['id'])));
		                        
		                        $ind = $this->Paramindemnite->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC'));
		                        /*---------------Affectation ou Mutation de l'agent-------------------*/
		                         $affec = $this->Agaffectmutation->find('all', array('conditions'=>array('Agaffectmutation.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                        $typefonction = $affec[0]['Agaffectmutation']['paramtypefonction_id'];
		                        /*-----------------------------------------------*/
		                        $base = 0;
		                        $montant = 0;
		                        foreach ($indemnites as $indemnite){
		                        	
		                        	$code_paramind =  $indemnite['Agindemnite']['code_paramind'];
		                        	

		                        	$ind = $this->Paramindemnite->find('all', array('conditions'=>array('Paramindemnite.code ='.$code_paramind), 'recursive'=>0));
		                        	$identifiant = $ind[0]['Paramindemnite']['id'];
				                    $code = $ind[0]['Paramindemnite']['code'];
				                    $libelle = $ind[0]['Paramindemnite']['libelle'];
		                            $avoiret = $ind[0]['Paramindemnite']['paramavoiret_id'];
		                            //$type = $ind[0]['Paramindemnite']['paramtypefonction_id'];
		                            /*-----------AVANCEMENT-----------------------------------*/
		                            $av = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                            $classification_id = $av[0]['Agavencement']['paramclassification_id'];
				                    $paramechelon_id = $av[0]['Agavencement']['paramechelon_id'];
				                    $year_anc = $av[0]['Agavencement']['anciennete'];

		                            /*---------TRAITEMENT DE BASE------------------------------*/
		                            if($code == 1)
		                            {
                                        $green = $this->Paramgrillesal->find('all', array('conditions'=>array("Paramgrillesal.paramclassification_id='{$classification_id}'","Paramgrillesal.paramechelon_id='{$paramechelon_id}'"), 'recursive'=>0));
		                                  
		                                $base = $green[0]['Paramgrillesal']['salaire'];

		                                $salaire_base = ($base / 30) * $duree;
		                                
		                                $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$salaire_base,
										    'taux' =>$duree,
										    'montant' => $salaire_base,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
							           
		                            }
		                           
		                            /*******************INDEMNITE FONCTION*************************/
		                            if(($code == 3 && $typefonction == 1) ||
		                        	   ($code == 3 && $typefonction == 2) ||
		                               ($code == 3 && $typefonction == 3))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];

			                                 $mnt_fct = ($montant / 30) * $duree;
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$mnt_fct,
										    'taux' =>$duree,
										    'montant' => $mnt_fct,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE LOGEMENT*************************/
		                            if($code == 4)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];

		                                 $mnt_log = ($montant / 30) * $duree;
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_log,
									    'taux' =>$duree,
									    'montant' => $mnt_log,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE TRANSPORT*************************/
		                            if($code == 5)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];

		                                 $mnt_trans = ($montant / 30) * $duree;
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_trans,
									    'taux' =>$duree,
									    'montant' => $mnt_trans,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE SUJETION*************************/
		                            if($code == 6)
		                            {
		                            	
		    						    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem. 	paramclassification_id='$classification_id'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];

		                                 $mnt_suj = ($montant / 30) * $duree;
		                               
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_suj,
									    'taux' =>$duree,
									    'montant' => $mnt_suj,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE CAISSE*************************/
		                            if(($code == 7 && $typefonction == 5) ||
		                        	   ($code == 7 && $typefonction == 7))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];

			                                $mnt_caiss = ($montant / 30) * $duree;
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$mnt_caiss,
										    'taux' =>$duree,
										    'montant' => $mnt_caiss,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************ALLOCATION FAMILLIALE*************************/
		                            if($code == 8)
		                            {
		                            	$alloc = 0;
		                            	
		                            	$montant =  $indemnite['Agindemnite']['base_montant'];

		                                $pegaz = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $pegaz[0]['Agdossier']['ag_charge'];
                                        /*
		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $montant = $mnt[0]['Paramindemitem']['montant'];*/
		                               
		                                $alloc = $nbcharge * $montant;

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$alloc,
									    'montant' => $alloc,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
                                    /*******************CONGES MATERNITE******************/
		                            if($code == 10)
		                            {
		                            	
		                            	$conge_mat =  $indemnite['Agindemnite']['base_montant'];
		                              
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$conge_mat,
									    'montant' => $conge_mat,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************ANCIENNETE*************************/
		                            if($code == 11)
		                            {
		                            	$sal = $this->Paramgrillesal->find('all', array('conditions'=>array("Paramgrillesal.paramclassification_id='$classification_id'",
		                                    "Paramgrillesal.paramechelon_id='$paramechelon_id'"), 'recursive'=>0));
		                                $base = $sal[0]['Paramgrillesal']['salaire'];

		                                $salaire_base = ($base / 30) * $duree;
		                        	    
		    						    $anciennete = $year_anc * ($salaire_base * 0.01);
		                               
		                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$salaire_base,
										    'taux' =>$year_anc,
										    'montant' => $anciennete,
										    'avoir_ret' => $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
								        
		                            }
		                            /*******************HEURE SUP******************/
		                            if($code == 12)
		                            {
		                            	
		                            	$heure_sup =  $indemnite['Agindemnite']['base_montant'];
		                                

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>'',
									    'montant' => $heure_sup,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
                                    /*******************CONGES PAYES******************/
		                            if($code == 23)
		                            {
		                            	
		                            	$conge_paye =  $indemnite['Agindemnite']['base_montant'];
		                              
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$conge_paye,
									    'montant' => $conge_paye,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************RETENUE PHARMACIE*************************/
		                            if($code == 29)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

		                                $affbontraite = $this->Affbontraite->find('all', array('conditions'=>array("Affbontraite.agdossier_id='$dossier'","Affbontraite.date_ret_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($affbontraite[0]['Affbontraite']['montant_ret_traite']))?$affbontraite[0]['Affbontraite']['montant_ret_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                            /*******************AVANTAGE NATURE FONCTION******************/
		                            if($code == 31)
		                            {
		                            	
		                            	$fonction =  $indemnite['Agindemnite']['base_montant'];
		                                $avg_nat_fct = ($fonction / 240);


		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_fct,
									    'montant' => $avg_nat_fct,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVANTAGE NATURE LOGEMENT******************/
		                            if($code == 32)
		                            {
		                            	
		                            	$logement =  $indemnite['Agindemnite']['base_montant'];
		                                $avg_nat_log = ($logement / 240);


		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_log,
									    'montant' => $avg_nat_log,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVANTAGE NATURE TRANSPORT******************/
		                            if($code == 33)
		                            {
		                            	
		                            	$transport =  $indemnite['Agindemnite']['base_montant'];
		                                $avg_nat_trans = ($transport / 240);


		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$avg_nat_trans,
									    'montant' => $avg_nat_trans,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVOIR 1*************************/
		                            if($code == 35)
		                            {
		                            	
		                            	$mnt_avoir1 =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir1,
									    'taux' =>'',
									    'montant' => $mnt_avoir1,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVOIR 2*************************/
		                            if($code == 36)
		                            {
		                            	
		                            	$mnt_avoir2 =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir2,
									    'taux' =>'',
									    'montant' => $mnt_avoir2,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                             /*******************AVOIR AVANCEMENT*************************/
		                            if($code == 37)
		                            {
		                            	
		                            	$mnt_avoir_av =  $indemnite['Agindemnite']['base_montant'];
		                                
		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$mnt_avoir_av,
									    'taux' =>'',
									    'montant' => $mnt_avoir_av,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************INDEMNITE GUICHET*************************/
		                           if(($code == 38 && $typefonction == 4)||
		                        	  ($code == 38 && $typefonction == 7))
		                            {
		                            	
		                                    $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'","Paramindemitem.paramtypefonction_id='$typefonction'"), 'recursive'=>0));
			                                $montant = $mnt[0]['Paramindemitem']['montant'];

			                                $mnt_gui = ($montant / 30) * $duree;
			                               
			                            	$item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$mnt_gui,
										    'taux' =>$duree,
										    'montant' => $mnt_gui,
										    'avoir_ret' =>  $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		                            }
		                            /*******************AVANCE SUR SALAIRE*************************/
		                            if($code == 98)
		                            {
		                            	$dossier = $contrat['Agcontrat']['agdossier_id'];

		                            	$date_debut = $postData['Rembulletin']['date_debut'];

	                                    $afftraite = $this->Afftraite->find('all', array('conditions'=>array("Afftraite.agdossier_id='{$dossier}'","Afftraite.date_traite ='$date_debut'"), 'recursive'=>0));
		                                $montant = (isset($afftraite[0]['Afftraite']['montant_traite']))?$afftraite[0]['Afftraite']['montant_traite']:'';
		                                if(isset($montant))
		                                {
			                                $item = array('Rembulitem'=>array(
										        'rembulletin_id' => $bulID,
										        'code' => $code,
										        'designation' => $libelle,
											    'base' =>'',
											    'taux' =>'',
											    'montant' => $montant,
											    'avoir_ret' => $avoiret
										          )
							                    );
									        $this->Rembulitem->save($item);
		    						    }
		                            }
		                            /******************SALAIRE BRUTE****************************/
		                            if($code == 500)
		                            {
		                            	
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
                                        $base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
	                                    $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:0;
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;
										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                $salaire_brute = 0;

		                                $salaire_brute = $trait_base + 
		                                               $indFonction +
		                                               $indLogement +
		                            	               $indTransport + 
		                            	               $indSujetion + 
		                            	               $anciennete +
		                             	               $allocation + 
		                             	               $indGuichet + 
		                             	               $indCaisse + 
		                             	               $avoir1 + 
		                             	               $avoir2 + 
		                             	               $avoirav  + 
		                             	               $avg_nat_trans +
		                             	               $avg_nat_fct + 
		                             	               $avg_nat_logement + 
		                             	               $sursalaire + 
		                             	               $heure_sup;
		                               
		                               /*--------------------*/
		                                $red = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'montant' => $salaire_brute,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($red);
		                            }
		                            /*******************CNSS*************************/
		                            if($code == 400)
		                            {
		                            	
		                            	$salairebrut = 0;
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
		                             	$base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
		                             	$fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:'';
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;

										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                               
		                                $salairebrut = $trait_base + $indFonction + $indLogement +
		                            	               $indTransport + $indSujetion + $anciennete +
		                             	               $allocation + $indGuichet + $indCaisse + 
		                             	               $avoir1 + $avoir2 + $avoirav  + $avg_nat_trans +
		                             	               $avg_nat_fct + $avg_nat_logement + $sursalaire + $heure_sup;

		                              
		                                $brute = 0.055 * $salairebrut;
		                                /*======5.5/100 salaire brute==========*/
						                $base = 0.08 * ($trait_base + $anciennete + $avoirav);
						                /*===============CNSS=========================*/
						                $cnss = MIN($brute,$base,44000);
		                                /*--------------------*/
		                                
		                                $ret_cnss = array('Rembulitem'=>array(
									        'rembulletin_id'=>$bulID,
									        'code'=>$code,
									        'designation'=>$libelle,
										    'base'=>$sal_brute,
										    'montant'=>$cnss,
										    'avoir_ret'=>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($ret_cnss);
		                            }
		                            /*******************CARFO*************************/
		                            if($code == 401)
		                            {
		                            	$carfo = 0;
		                            	$base_carfo =  $indemnite['Agindemnite']['base_montant'];
		                                

		                                $mnt = $this->Paramindemitem->find('all', array('conditions'=>array("Paramindemitem.paramindemnite_id='$identifiant'"), 'recursive'=>0));
		                                $taux = $mnt[0]['Paramindemitem']['taux'];
		                               
		                                $carfo = $base_carfo * ($taux / 100);

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$base_carfo,
									    'montant' => $carfo,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************IUTS*************************/
		                            if($code == 402)
		                            {
		                            	
		                            	$ret_cnss = 0;
		                            	$ret_carfo = 0;
		                            	$salaireImposable = 0;
                                        $exoPartiel =0;
                                        
                                        $heure_sup = 0;
		                            	

		                                $abat = 0;
		                                $salnetImposable = 0;
                                         $netImp = 0;

		                                $iutsBrute = 0;
		                                $iutsDeductible = 0;
                                        $net_iuts = 0;

		                                /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
		                                $salairebrut = 0;
		                            	$trait_base = 0;
		                            	$indFonction = 0;
		                            	$indLogement = 0;
		                            	$indTransport = 0;
		                            	$indSujetion = 0;
		                            	$anciennete = 0;
		                             	$allocation  = 0;
		                             	$indGuichet = 0;
		                             	$indCaisse = 0;
                                        
                                        $avoir1 = 0;
		                             	$avoir2 = 0;
		                             	$avoirav  = 0;
		                             	
		                             	$avg_nat_trans = 0;
		                             	$avg_nat_fct = 0;
		                             	$avg_nat_logement = 0;

		                             	$sursalaire  = 0;
		                             	$heure_sup = 0;
		                             	/**************Taitement de base******************************/
		                             	$base = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1'"), 'recursive'=>0));
		                                $trait_base = $base[0]['Rembulitem']['montant'];

		                             	/**************Indemnité de fonction*********************/
		                             	$fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='3'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 1 || $typefonction == 2 || $typefonction == 3)
		                             	{
		                                	$indFonction = $fct[0]['Rembulitem']['montant'];
		                                }else{$indFonction = 0;}
		                                /**************Indemnité de logement****************************/
		                             	$log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='4'"), 'recursive'=>0));
		                                $indLogement = $log[0]['Rembulitem']['montant'];
		                                /**************Indemnité de transport********************************/
		                             	$trans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='5'"), 'recursive'=>0));
		                                $indTransport = $trans[0]['Rembulitem']['montant'];
		                                /**************Indemnité de sujetion********************************/
		                             	$suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='6'"), 'recursive'=>0));
		                                $indSujetion = $suj[0]['Rembulitem']['montant'];
		                                /**************Indemnité de guichet********************************/
		                             	$gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='38'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 4 || $typefonction == 7)
		                             	{
		                                	$indGuichet = $gui[0]['Rembulitem']['montant'];
		                                }else{$indGuichet = 0;}
		                                /**************Indemnité de caisse********************************/
		                             	$cais = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='7'"), 'recursive'=>0));
		                             	
		                             	if($typefonction == 5 || $typefonction == 7)
		                             	{
		                                	$indCaisse = $cais[0]['Rembulitem']['montant'];
		                                }else{$indCaisse = 0;}
		                                
		                                /**************Anciennete********************************/
		                             	$anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='11'"), 'recursive'=>0));
		                                $anciennete = (isset($anc[0]['Rembulitem']['montant']))?$anc[0]['Rembulitem']['montant']:'';
		                               /**************Allocation********************************/
		                             	$alloc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='8'"), 'recursive'=>0));
		                               
		                                 $allocation = (isset($alloc[0]['Rembulitem']['montant']))?$alloc[0]['Rembulitem']['montant']:0;

		                                /**************Avoir1********************************/
		                             	$mouton = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='35'"), 'recursive'=>0));
		                               
		                                 $avoir1 = (isset($mouton[0]['Rembulitem']['montant']))?$mouton[0]['Rembulitem']['montant']:0;

		                                /**************Avoir2********************************/
		                             	$chevre = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='36'"), 'recursive'=>0));
		                               
		                                 $avoir2 = (isset($chevre[0]['Rembulitem']['montant']))?$chevre[0]['Rembulitem']['montant']:0;
		                                 
		                                /**************Avoir Avancement********************************/
		                             	$cadet = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='37'"), 'recursive'=>0));
		                               
		                                 $avoirav = (isset($cadet[0]['Rembulitem']['montant']))?$cadet[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                                  /**************Avantage nature transport *******************/
		                             	$avgtrans = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='33'"), 'recursive'=>0));
		                                $avg_nat_trans = (isset($avgtrans[0]['Rembulitem']['montant']))?$avgtrans[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature fonction *******************/
		                             	$avgfct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='31'"), 'recursive'=>0));
		                                $avg_nat_fct = (isset($avgfct[0]['Rembulitem']['montant']))?$avgfct[0]['Rembulitem']['montant']:0;

		                                  /**************Avantage nature logement *******************/
		                             	$avglog = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='32'"), 'recursive'=>0));
		                                $avg_nat_logement = (isset($avglog[0]['Rembulitem']['montant']))?$avglog[0]['Rembulitem']['montant']:0;

		                                /**************SUR SALAIRE *******************/
		                             	$tmp_sur_sal = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='1007'"), 'recursive'=>0));
										$sursalaire = (isset($tmp_sur_sal[0]['Rembulitem']['montant']))?$tmp_sur_sal[0]['Rembulitem']['montant']:0;
										/**************HEURE SUPLEMENTAIRE *******************/
		                             $tmp_heure = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='12'"), 'recursive'=>0));
										$heure_sup = (isset($tmp_heure[0]['Rembulitem']['montant']))?$tmp_heure[0]['Rembulitem']['montant']:0;
		                                /******************************************************/
		                               
		                                $salairebrut = $trait_base + $indFonction + $indLogement +
		                            	               $indTransport + $indSujetion + $anciennete +
		                             	               $allocation + $indGuichet + $indCaisse + 
		                             	               $avoir1 + $avoir2 + $avoirav  + $avg_nat_trans +
		                             	               $avg_nat_fct + $avg_nat_logement + $sursalaire + $heure_sup;
		                                /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
		                                $ret_cnss_carfo = 0;
		                                $cotisation = $contrat['Agcontrat']['paramstructurecotsocial_id'];
		                                if(isset($cotisation) && $cotisation == 1)
		                                {
                                           /**************CNSS*******************/
				                           $white = $this->Rembulitem->find('all', array('conditions'=>array(
				                                	"Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='400'"), 'recursive'=>0));
									       $ret_cnss_carfo = (isset($white[0]['Rembulitem']['montant']))?$white[0]['Rembulitem']['montant']:0;
				                        }
		                                elseif(isset($cotisation) && $cotisation == 2)
		                                {
		                                   /**************CARFO*******************/
	                                       $yelow = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulID'","Rembulitem.code='401'"), 'recursive'=>0));
		                                   $ret_cnss_carfo = (isset($yelow[0]['Rembulitem']['montant']))?$yelow[0]['Rembulitem']['montant']:0;

		                                }else{$ret_cnss_carfo = 0;}
		                                
		                                /***************SALAIRE IMPOSABLE***********************/
		                                $salaireImposable = $salairebrut - $ret_cnss_carfo;


		                                $salImpo = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1000,
									        'designation' => 'Salaire imposable',
									        'montant' => $salaireImposable,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($salImpo);

		                                /***************Exoneration logement***********************/
		                                $logement = 0.2 *  $salaireImposable;
		                                $plafond_log = 75000;
                                        $indLog = $indLogement + $avg_nat_logement;

		                                $exoLogement = min($logement,$plafond_log,$indLog);

		                                $exo_log = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1002,
									        'designation' => 'Exoneration logement',
									        'montant' => $exoLogement,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_log);


                                        /***************Exoneration fonction***********************/
		                                $fonction = 0.05 *  $salaireImposable;
		                                $plafond_fct = 50000;
                                        $indFct = $indFonction +  $avg_nat_fct + 
                                                  $indSujetion + $indGuichet + $indCaisse;

		                                $exoFonction = min($fonction,$plafond_fct,$indFct);
                                        

							            $exo_fonct = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1004,
									        'designation' => 'Exoneration fonction',
									        'montant' => $exoFonction,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_fonct);

		                                /***************Exoneration transport***********************/
		                                $transport = 0.05 *  $salaireImposable;
		                                $plafond_transp = 30000;
                                        $indFct = $indTransport +  $avg_nat_trans;

		                                $exoTransport = min($transport,$plafond_transp,$indFct);
		                               
		                                $exo_trans = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1003,
									        'designation' => 'Exoneration transport',
									        'montant' => $exoTransport,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($exo_trans);
                                        /**************EXONERATION PARTIEL************/

		                                $exoPartiel = $exoLogement + $exoFonction + $exoTransport;

                                        $exo_pat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1005,
									        'designation' => 'Exoneration partiel',
									        'montant' => $exoPartiel,
									        'avoir_ret' =>2
										    )
					                    );
					                    $this->Rembulitem->save($exo_pat);
		                              
		                                /*---------------Abattement---------------------*/
		                              
		                                $phi = $this->Agavencement->find('all', array('conditions'=>array('Agavencement.agcontrat_id='.$contrat['Agcontrat']['id']), 'recursive'=>0));
		                                $classification_id = $phi[0]['Agavencement']['paramclassification_id'];

		                               
		                                if($classification_id <= 4)
		                                {
		                                  $abat = 0.25 * ($trait_base + $anciennete + $heure_sup + $avoirav);
		                                }
                                        else
		                                {
		                                   $abat = 0.2 * ($trait_base + $anciennete + $heure_sup + $avoirav);
		                                }

                                        
                                        
		                                $tmp_abat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1006,
									        'designation' => 'Abattement',
									        'montant' => $abat,
									        'avoir_ret' =>2
										    )
					                    );
							            $this->Rembulitem->save($tmp_abat);
		                               
		                                /*==================SALAIRE NET IMPOSABLE=====================*/

		                                $salnetImposable = $salaireImposable - $exoPartiel - $abat;

		                                $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);
		                               

							            $item = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 1001,
									        'designation' => 'Salaire Net Imposable',
									        'montant' => $netImp,
									        'montant2' => $salnetImposable,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($item);
		                                

							            /************* IUTS BRUTE**********************************/

		                                switch($netImp){
									        case $netImp<=30100;
									            /*---------------------------------------*/
									            //$iutsBrute = 0 * $netImp - 0;
									            $iutsBrute = $netImp;
							                    /*---------------------------------------*/
									            break;
									        case 30100<$netImp && $netImp<=50100;
									            /*---------------------------------------*/
									            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
									            $iutsBrute = 0 + (($netImp - 30100) *0.121);
					                            /*---------------------------------------*/
									            break;
									        case 50100 < $netImp && $netImp <= 80100;
									            /*---------------------------------------*/
									            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
									            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
									            /*---------------------------------------*/
									            break;
									        case 80100 < $netImp && $netImp <= 120100;
									            /*---------------------------------------*/
									            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
									            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
									            /*---------------------------------------*/
									            break;
									        case 120100 < $netImp && $netImp <=170100;
									            /*---------------------------------------*/
									            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
									            /*---------------------------------------*/
									            break;
									        case 170100 < $netImp && $netImp <= 250100;
									            /*---------------------------------------*/
									            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
									            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
									            /*---------------------------------------*/
									            break;
									        default: 
									             
                                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
									            break;
							            }

							            $iuts_brute = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 200,
									        'montant' => $iutsBrute,
									        'designation' => 'IUTS Brute',
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($iuts_brute);
							            


							           /************* IUTS DEDUCTIBLE*****************************/
                                      

							            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$contrat['Agcontrat']['agdossier_id']), 'recursive'=>0));
		                                $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

		                                

		                                if($nbcharge == 0){$iutsDeductible = 0;}
		                                if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
		                                if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
		                                if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
		                                if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
		                              
		                               
							            $iuts_deduc = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 201,
									        'montant' => $iutsDeductible,
									        'designation' => 'IUTS Deductible',
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($iuts_deduc);
		                               
							           /************* IUTS NET************************************/
                                        $net_iuts = $iutsBrute -  $iutsDeductible;

                                        $netIuts = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => $code,
									        'designation' => $libelle,
										    'base' =>$netImp,
										    'taux' =>'',
										    'montant' =>  $net_iuts,
										    'avoir_ret' =>$avoiret
										    )
										    
					                    );
							            $this->Rembulitem->save($netIuts);
							           /*********FIN*****UITS NET**********************************/
 
                                       /*************Impot sur salaire************************************/
                                        $impot_salaire = $net_iuts +  $ret_cnss_carfo;

                                         $impSal = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 501,
									        'designation' => 'Impot sur salaire',
									        'montant' => $impot_salaire,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($impSal);

							            /*************TPA************************************/
                                        $tpa = $salairebrut * 0.03;

                                         $tmp_tpa = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 700,
									        'designation' => 'TPA',
									        'montant' => $tpa,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($tmp_tpa);

							            /*************CNSS Patronal************************************/
                                        $cnss_pat = $salairebrut * 0.215;

                                         $tmp_cnss_pat = array('Rembulitem'=>array(
									        'rembulletin_id' => $bulID,
									        'code' => 902,
									        'designation' => 'CNSS Patronal',
									        'montant' => $cnss_pat,
									        'avoir_ret' =>2
										    
										    )
										    
					                    );
							            $this->Rembulitem->save($tmp_cnss_pat);


                                      
		                            }
		                            /*******************Cotisation mtuelle*************************/
		                            if($code == 403)
		                            {
		                            	
		                            	$cot_mnt =  $indemnite['Agindemnite']['base_montant'];

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$cot_mnt,
									    'montant' => $cot_mnt,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                            /*******************AUTRE RETENUE******************/
		                            if($code == 1008)
		                            {
		                            	
		                            	$autre_ret =  $indemnite['Agindemnite']['base_montant'];
		                              

		                            	$item = array('Rembulitem'=>array(
								        'rembulletin_id' => $bulID,
								        'code' => $code,
								        'designation' => $libelle,
									    'base' =>$autre_ret,
									    'taux' => '30',
									    'montant' => $autre_ret,
									    'avoir_ret' =>  $avoiret
									          )
						                    );
								        $this->Rembulitem->save($item);
		                            }
		                           
		         
		                       }
		                    $msg = 'Bulletin généré avec succès';          
	                    }
	                }
                    /*++++++++++++++++BULLETIN NORMALE+++++++++++++++++++++++++++++++*/
                   /*++++++++++++++++BULLETIN SPECIALE+++++++++++++++++++++++++++++++*/
	            }
                else
                {
                	$this->Session->setFlash('Selectionner un type de bulletin','flash notice'); 
                }    
                $saveId = true;
                
                $this->redirect(array('controller'=>'Rembulletins', 'view'=>'index2'));
		        $this->Session->setFlash($msg.'','flash notice');   
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
				$this->data = $this->Rembulletin->read($id);
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index2'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index2'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION BULLETIN INDIVIDUEL':'MODIFICATION BULLETIN INDIVIDUEL'));
		$this->set('toolbar', $toolbar);

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('tauxprimebilans', $this->Paramtauxprimebilan->find('list', array('list'=>array('taux','taux'), 'order'=>'taux ASC')));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		
	}


	public function simulateur() {
		$this->requestAction('Users' ,'loggedIn');
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$id = $this->getGetParam('id');
		
		$postData = $this->postData();
		if(isset($postData['Rembulletin']['submit']) && isset($postData['Rembulletin']))
		{
            
            $msg = '';
            if($postData['Rembulletin']['ag_dossier']<>'' &&
               $postData['Rembulletin']['date_debut']<>'' &&
               $postData['Rembulletin']['date_fin']<>'' &&
               $postData['Rembulletin']['paramtypesalaire_id'] &&
               $postData['Rembulletin']['sal_base']<>'')
            {
				/*********************************************************************/
				$type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];;
				if($type_bulletin == 2)
	            {
					/*++++++++++++++++GRATIFICATION OU 13E MOIS+++++++++++++++++++++++++++++++*/
	                  $ag_dossier = $postData['Rembulletin']['ag_dossier'];
	                  $date_debut = $postData['Rembulletin']['date_debut'];
	                  $date_fin = $postData['Rembulletin']['date_fin'];
	                  $type_bulletin = $postData['Rembulletin']['paramtypesalaire_id'];


                    $decembre = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.ag_dossier='{$ag_dossier}'","Rembulletin.paramtypesalaire_id='1'","Rembulletin.date_debut ='$date_debut'","Rembulletin.date_fin='{$date_fin}'"), 'recursive'=>0));
                    $bulletinid = $decembre[0]['Rembulletin']['id'];
		            $nbr_bul = count($decembre);
	                if($nbr_bul > 0)
	                {
	                  //$msg = 'Bulletin existe dans la base';
	                  
	                  /*--------------Salaire de base gratification------------------------------*/
	                  /*$grat = $this->Paramsalbasegrat->find('all', array('conditions'=>array("Paramsalbasegrat.agdossier_id='{$ag_dossier}'"), 'recursive'=>0));
	                  $sal_base = $grat[0]['Paramsalbasegrat']['salaire_base_grat'];*/
	                  $sal_base =  $postData['Rembulletin']['sal_base'];
	                 
	                  /*-----------Traitement de base normale-------------*/
	                  $b = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='1'"), 'recursive'=>0));
	                  $base = (isset($b[0]['Rembulitem']['montant']))?$b[0]['Rembulitem']['montant']:0;
	                   /*-----------Fonction-------------*/
	                  $fct = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='3'"), 'recursive'=>0));
	                  $fonction = (isset($fct[0]['Rembulitem']['montant']))?$fct[0]['Rembulitem']['montant']:0;
	                   /*-----------Logement-------------*/
	                  $log = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='4'"), 'recursive'=>0));
	                  $logement =  (isset($log[0]['Rembulitem']['montant']))?$log[0]['Rembulitem']['montant']:0;
	                   /*-----------Transport-------------*/
	                  $trp = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='5'"), 'recursive'=>0));
	                  $transport = (isset($trp[0]['Rembulitem']['montant']))?$trp[0]['Rembulitem']['montant']:0;
	                   /*-----------Sujetion-------------*/
	                  $suj = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='6'"), 'recursive'=>0));
	                  $sujetion = (isset($suj[0]['Rembulitem']['montant']))?$suj[0]['Rembulitem']['montant']:0;
	                  /*-----------Caisse-------------*/
	                  $cai = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='7'"), 'recursive'=>0));
	                  $caisse = (isset($cai[0]['Rembulitem']['montant']))?$cai[0]['Rembulitem']['montant']:0;
	                  /*-----------Allocation-------------*/
	                  $alc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='8'"), 'recursive'=>0));
	                  $allocation = (isset($alc[0]['Rembulitem']['montant']))?$alc[0]['Rembulitem']['montant']:0;
	                  /*-----------Avantage nature logement-------------*/
	                  $anle = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='32'"), 'recursive'=>0));
	                  $av_nat_log= (isset($anle[0]['Rembulitem']['montant']))?$anle[0]['Rembulitem']['montant']:0;
	                  /*-----------Avantage nature transport-------------*/
	                  $ant = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='33'"), 'recursive'=>0));
	                  $av_nat_trans = (isset($ant[0]['Rembulitem']['montant']))?$ant[0]['Rembulitem']['montant']:0;
	                  /*-----------Guichet-------------*/
	                  $gui = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='38'"), 'recursive'=>0));
	                  $guichet = (isset($gui[0]['Rembulitem']['montant']))?$gui[0]['Rembulitem']['montant']:0;
	                  /*---------------------Anciennete------------------------------*/
	                  $anc = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='11'"), 'recursive'=>0));
	                  $taux = (isset($anc[0]['Rembulitem']['taux']))?$anc[0]['Rembulitem']['taux']:0;
	                  $anciennete = $sal_base * $taux * 0.01;

	                  /*----------Salaire brute---------------------------*/
                      $salairebrut = $sal_base + $anciennete + $fonction + $logement + $transport + $sujetion + $caisse + $allocation + $av_nat_log + $av_nat_trans + $guichet;


                      /*----------Cotisation sociale----------------------*/
	                  $contrat = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.agdossier_id='.$ag_dossier), 'recursive'=>0));
		              $cotisation = $contrat[0]['Agcontrat']['paramstructurecotsocial_id'];
                      
                      /**************CNSS*******************/
                      $cnss_gratif = 0;
                       $txt = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='{$bulletinid}'","Rembulitem.code='400'"), 'recursive'=>0));
		               $cnss_normal = $txt[0]['Rembulitem']['montant'];

		                if(isset($cnss_normal) && $cnss_normal >= 44000)
		                  {
		                  	$cnss_gratif = 0;
		                  }
		                  else
		                  {
		                  	$cnss_gratif = 44000 - $cnss_normal;
		                  }

                      $ret_cnss_carfo = 0;
	                  if(isset($cotisation) && $cotisation == 1)
	                  {
	                     /**************CNSS*******************/
		                 $ret_cnss_carfo =  $cnss_gratif;
		               }
		               elseif(isset($cotisation) && $cotisation == 2)
	                   {
	                      /**************CARFO*******************/
	                      $ret_cnss_carfo = 0;   

	                    }else{$ret_cnss_carfo = 0;}
	                    /*----------Fin Cotisation sociale--------------------*/
	                    
                        /***************SALAIRE IMPOSABLE***********************/
                        $salaireImposable = $salairebrut - $ret_cnss_carfo;

                        /*==================SALAIRE NET IMPOSABLE=====================*/

                        $salnetImposable = $salaireImposable;

                        $netImp = round($salnetImposable,-2,PHP_ROUND_HALF_DOWN);

				        /************* IUTS BRUTE**********************************/

                        switch($netImp){
					        case $netImp<=30100;
					            /*---------------------------------------*/
					            //$iutsBrute = 0 * $netImp - 0;
					            $iutsBrute = $netImp;
			                    /*---------------------------------------*/
					            break;
					        case 30100<$netImp && $netImp<=50100;
					            /*---------------------------------------*/
					            //$iutsBrute = 2408 + (($netImp - 30100) *0.121);
					            $iutsBrute = 0 + (($netImp - 30100) *0.121);
	                            /*---------------------------------------*/
					            break;
					        case 50100 < $netImp && $netImp <= 80100;
					            /*---------------------------------------*/
					            //$iutsBrute = 4156 + (($netImp - 50100) * 0.139);
					            $iutsBrute = 2408 + (($netImp - 50100) * 0.139);
					            /*---------------------------------------*/
					            break;
					        case 80100 < $netImp && $netImp <= 120100;
					            /*---------------------------------------*/
					            //$iutsBrute = 6264 + (($netImp - 80100) * 0.157);
					            $iutsBrute = 6564 + (($netImp - 80100) * 0.157);
					            /*---------------------------------------*/
					            break;
					        case 120100 < $netImp && $netImp <=170100;
					            /*---------------------------------------*/
					            $iutsBrute = 12828 + (($netImp - 120100) * 0.184);
					            /*---------------------------------------*/
					            break;
					        case 170100 < $netImp && $netImp <= 250100;
					            /*---------------------------------------*/
					            //$iutsBrute = 17338 + (($netImp - 170100) * 0.217);
					            $iutsBrute = 22010 + (($netImp - 170100) * 0.217);
					            /*---------------------------------------*/
					            break;
					        default: 
					             
                                $iutsBrute = 39348 + (($netImp - 250100) * 0.25);
					            break;
			            }
			           
				        /************* IUTS DEDUCTIBLE*****************************/

			            $xenon = $this->Agdossier->find('all', array('conditions'=>array('Agdossier.id='.$ag_dossier), 'recursive'=>0));
                        $nbcharge = $xenon[0]['Agdossier']['ag_charge'];

                        

                        if($nbcharge == 0){$iutsDeductible = 0;}
                        if($nbcharge == 1){$iutsDeductible = $iutsBrute * 0.08;}
                        if($nbcharge == 2){$iutsDeductible = $iutsBrute * 0.10;}
                        if($nbcharge == 3){$iutsDeductible = $iutsBrute * 0.12;}
                        if($nbcharge >= 4){$iutsDeductible = $iutsBrute * 0.14;}
                      
                      /************* IUTS NET************************************/
                        $net_iuts = $iutsBrute -  $iutsDeductible;
                        
                        $sal_net = $salairebrut - $ret_cnss_carfo - $net_iuts;

                        $msg = 'SALAIRE DE BASE : '.$sal_base.'<br> ANCIENNETE : '.$anciennete.'<br> SALAIRE BRUTE : '.$salairebrut.'<br> CNSS ou CARFO : '.$ret_cnss_carfo.'<br> SALAIRE IMPOSABLE : '.$salaireImposable.'<br> SALAIRE NET IMPOSABLE : '.$netImp.'<br> IUTS BRUTE : '.$iutsBrute.'<br> IUTS DEDUCTIBLE : '.$iutsDeductible.'<br> IUTS NET : '.$net_iuts.'<br> SALAIRE NET : '.$sal_net;

                       
	                }
	                else
	                {
	                	$msg = 'Le bulletin du mois de décembre n\'existe pas dans la base, calculer au préalable le bulletin du mois de décembre';
	                	
	                }
    				
                   /*++++++++++++++++FIN GRATIFICATION OU 13E MOIS+++++++++++++++++++++++++++*/
				}
				else
			    {
					$this->data = $postData;
					$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez selectionner la gratification', 'flash error');
		        }	
				/*********************************************************************/
            }
		    else
		    {
				$this->data = $postData;
				$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir des données valides', 'flash error');
	        }

            $this->data = $postData;
	        $this->Session->setFlash($msg.'','flash notice');
		}

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('tauxprimebilans', $this->Paramtauxprimebilan->find('list', array('list'=>array('taux','taux'), 'order'=>'taux ASC')));

		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
				
		
	}



	public function search2() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		$toolbar = array();
		if($this->Session->check('return')){
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index2', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toolbar['Retour'] = array(
				'url'=>array('controller'=>'Rembulletins', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . ('RECHERCHE BULLETIN'));
		$this->set('toolbar', $toolbar);
	}
	
	/*-----------------------------*/

	public function index3() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 

		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT GENERAL DES SALAIRES<span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
        #$this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		
	}
	


	public function index4() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 
		$typesal = (isset($tmp[0]['Rembulletin']['paramtypesalaire_id']))?$tmp[0]['Rembulletin']['paramtypesalaire_id']:'1'; 
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
	        $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ORDRE DE VIREMENT PAR PERIODE<span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
        #$this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		
	}



	public function index5() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 
		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		$banque = '1';
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
			$banque = $postData['Rembulletin']['banque'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETATDES VIREMENT PAR PERIODE/BANQUE<span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        //$this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		$this->set('banque', $banque);
		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		$this->set('datedebut', $datedebut);
		
	}
	

	public function index6() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
	    $datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 
		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT IUTS/TPA<span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		//$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		//$this->set('banque', $banque);
		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		
		
	}


	public function index7() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
	    $datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 
		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT COTISATION SECURITE SOCIALE - CNSS<span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		
		
	}


	public function index8() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
	    $datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 
		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT COTISATION SECURITE SOCIALE - CARFO<span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		
		
	}

	public function index9() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 
		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		$indemnite = '4';
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
			$indemnite = $postData['Rembulletin']['indemnite'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT DES INDEMNITES <span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        //$this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));
    
		$this->set('indemnite', $indemnite);
		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		$this->set('datedebut', $datedebut);

		//Paramindemnite

		$this->set('paramindemnites', $this->Paramindemnite->find('list', array('list'=>array('code', 'libelle'), 'conditions'=>array("Paramindemnite.code IN (3,4,5,6,7,8,11,12,11,29,32,33,35,36,37,38,98)"))));
		
	}
	
	/*-----------------------------------*/
	//cotisation mutuelle
	
	public function index11() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
	    $datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 
		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT DES COTISATION MUTUELLES <span class="pageTitle">'.$name . SEP . $username.'</span>');
	
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        
        $this->set('rembulletins', $this->paginate('Rembulletin'));
		
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		//$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		//$this->set('banque', $banque);
		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		
		
	}
	


	public function index12() {
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $postData = $this->postData();

        $tmp = $this->Rembulletin->find('all', array('recursive'=>0, 'order'=>'id desc'));
		$datedebut = (isset($tmp[0]['Rembulletin']['date_debut']))?$tmp[0]['Rembulletin']['date_debut']:'2023-01-01';
		$datefin = (isset($tmp[0]['Rembulletin']['date_fin']))?$tmp[0]['Rembulletin']['date_fin']:'2023-01-31'; 

		$typesal = (isset($tmp[0]['Rembulletin']['typesal']))?$tmp[0]['Rembulletin']['typesal']:'1';
		
        /******************************************************/
		if(isset($postData['Rembulletin']['valider'])){
			$datedebut = $postData['Rembulletin']['datedebut'];
			$datefin = $postData['Rembulletin']['datefin'];
			$typesal = $postData['Rembulletin']['typesal'];
		    $this->data = $postData;
		}
		/******************************************************/
       
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	  

		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'ETAT GENERAL ANNUEL DES SALAIRES <span class="pageTitle">'.$name . SEP . $username.'</span>');

		$rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut >= '{$datedebut}'","Rembulletin.date_debut <= '{$datefin}'","Rembulletin.paramtypesalaire_id = '{$typesal}'"), 'recursive'=>0));
	
        //$this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_debut'=>$datedebut);
       // $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.date_fin'=>$datefin);
	/*this->paginate['Rembulletin']['conditions'][] = array("Rembulletin.date_debut >= '{$datedebut}'");
		$this->paginate['Rembulletin']['conditions'][] = array("Rembulletin.date_debut <= '{$datefin}'");
        $this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        $this->set('rembulletins', $this->paginate('Rembulletin'));*/
		
        #$this->paginate['Rembulletin']['conditions'][] = array('Rembulletin.paramtypesalaire_id'=>$typesal);
        
		$this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

		$this->set('rembulletins', $rembulletins);

		$this->set('datedebut', $datedebut);
		$this->set('datefin', $datefin);
		$this->set('typesal', $typesal);
		
	}
	
	
	
	
	/*------------------------------*/
	public function del (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));

		$ids = explode('|', (string)$this->getGetParam('id'));
      

		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Rembulletin->find('all', array('conditions'=>array(array($this->Rembulletin->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Rembulletins';
			$dataList = array();
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Rembulletin']['id'];

				/*-----------------------------------*/
                $tmp = $this->Rembulitem->find('all', array('conditions'=>array('Rembulitem.rembulletin_id='.$d['Rembulletin']['id']), 'recursive'=>0));
		      
                    /*----->-------------------------------*/	
                foreach ($tmp as $alpha){
                   $this->Rembulitem->delete($alpha['Rembulitem']['id']);
                }
                
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Rembulletin->delete($ids);

			$this->Session->setFlash($log);			
		}
		$this->redirect(array('controller'=>'Rembulletins', 'view'=>'index'));
	}


	public function del2 (){
		$this->requestAction('Users' ,'loggedIn');				
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));

		$ids = explode('|', (string)$this->getGetParam('id'));
      

		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->Rembulletin->find('all', array('conditions'=>array(array($this->Rembulletin->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression Rembulletins';
			$dataList = array();

			
			
			foreach ($data as $d){
				$dataList[] = 'id:' . $d['Rembulletin']['id'];
				
				/*-----------------------------------*/
                $tmp = $this->Rembulitem->find('all', array('conditions'=>array('Rembulitem.rembulletin_id='.$d['Rembulletin']['id']), 'recursive'=>0));
		      
                    /*----->-------------------------------*/	
                foreach ($tmp as $alpha){
                   $this->Rembulitem->delete($alpha['Rembulitem']['id']);
                }
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->Rembulletin->delete($ids);

			$this->Session->setFlash($log);			
		}
		$this->redirect(array('controller'=>'Rembulletins', 'view'=>'index2'));
	}

	/*--------------------------------------*/
    public function bulletin() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
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
		$bulletinid = $this->getGetParam('bulletinid');

		/****************BULLETIN******************************/
        $data = $this->Rembulletin->find('all', array('conditions'=>array('Rembulletin.id='.$bulletinid), 'recursive'=>0));
        $bull_Id = $data[0]['Rembulletin']['id'];
		$num_bull = $data[0]['Rembulletin']['num_bull'];
		$date_debut = $data[0]['Rembulletin']['date_debut'];
		$date_fin = $data[0]['Rembulletin']['date_fin'];
		$agcontrat_id = $data[0]['Rembulletin']['agcontrat_id'];
		$num_contrat = $data[0]['Rembulletin']['num_contrat'];
		$ag_dossier = $data[0]['Rembulletin']['ag_dossier'];
		$matricule = $data[0]['Rembulletin']['matricule'];

		$typebul = $data[0]['Rembulletin']['paramtypesalaire_id'];
		$taux = $data[0]['Rembulletin']['taux_prime'];
		
	    $this->set('bull_Id', $bull_Id);
	    $this->set('num_bull', $num_bull);
	    $this->set('date_debut', $date_debut);
	    $this->set('date_fin', $date_fin);
	    $this->set('agcontrat_id', $agcontrat_id);
	    $this->set('num_contrat', $num_contrat);
	    $this->set('ag_dossier', $ag_dossier);
	    $this->set('matricule', $matricule);
        $this->set('typebul', $typebul);
        $this->set('taux', $taux);
	    
       

	    $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
       $this->set('charge',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /****************Contrat******************************/
        $cont = $this->Agcontrat->find('all', array('conditions'=>array('Agcontrat.id='.$agcontrat_id), 'recursive'=>0));

        $num_cotisation = $cont[0]['Agcontrat']['num_cotisation'];
        $modepaie_id = $cont[0]['Agcontrat']['parammodepaie_id'];
        $banque_id = $cont[0]['Agcontrat']['parambanque_id'];
        $num_comptebanq = $cont[0]['Agcontrat']['num_comptebanq'];
		$cotisation = $cont[0]['Agcontrat']['paramstructurecotsocial_id'];
        $this->set('num_cotisation', $num_cotisation);
        $this->set('modepaieid', $modepaie_id);
        $this->set('banqueid', $banque_id);
        $this->set('comptebanq', $num_comptebanq);
         $this->set('cotisation', $cotisation);

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

	/*--------------------------------------*/
    public function bulletingroupe() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'","Rembulletin.paramtypesalaire_id ='$typesal'"),'recursive'=>0, 'order'=>'id ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
        $this->set('rembulletins',$rembulletins);

       /****************Signataire DAFC******************************/
       $sign = $this->Signataire->find('all', array('conditions'=>array("Signataire.signature='18'","Signataire.statut='1'"), 'recursive'=>0));

        $signature = $sign[0]['Signataire']['agdossier_id'];
        $this->set('signature', $signature);
		//$this->helpers[] = 'Chiffrelettre';
		//$id = $this->getGetParam('id');
		
       

        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
        $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

        $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

		$this->set('style', '
			.breakAfter{
				page-break-after: always;
				
			}

			
			
		');
			
	}


	/*--------------------------------------*/
    public function etatsalaire() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'","Rembulletin.paramtypesalaire_id ='$typesal'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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

	public function etatsalaire2() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
       

        $postData = $this->postData();
        
        $date_debut ='2023-01-01';
        $date_fin ='2023-01-31';
        $typesal = '1';
    
        /*++++++++++++++++++++++++++++++++++++++*/
		if(isset($postData['Rembulletin']['submit'])){
			
			$date_debut = $postData['Rembulletin']['date_debut'];
			$date_fin = $postData['Rembulletin']['date_fin'];
			$typesal = $postData['Rembulletin']['typesal'];
			$this->data = $postData;

		}
		if(isset($postData['Rembulletin']['reinit']))$postData = array();
	     $this->data[0]=$postData;


       // $datedebut = $this->getGetParam('datedebut');
		//$datefin = $this->getGetParam('datefin');
		//$typesal = $this->getGetParam('typesal');

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$date_debut'","Rembulletin.date_fin ='$date_fin'","Rembulletin.paramtypesalaire_id ='$typesal'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$date_debut);
        $this->set('date_fin',$date_fin);
        $this->set('typesal',$typesal);
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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

        $this->set('paramtypesalaires', $this->Paramtypesalaire->find('list', array('list'=>array('id','libelle'), 'order'=>'libelle ASC')));

        $this->set('js', array('jsexport/jquery-1.12',
	                           'jsexport/jquery.dataTables',
	                           'jsexport/dataTables.buttons',
	                           'jsexport/buttons.html5',
	                           'jsexport/jszip',
	                           'jsexport/pdfmake',
	                           'jsexport/vfs_fonts'));

		$this->set('style', '
			.breakAfter{
				page-break-after: always;
				
			}

			
			
		');
			
	}

	 public function etatsalaire_an() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'","Rembulletin.paramtypesalaire_id ='$typesal'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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


    public function virementsalaire() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
        $this->helpers[] = 'Chiffrelettre';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');
		$banque = $this->getGetParam('banque');

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
        $this->set('banque',$banque);
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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



    public function etatindemnite() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
        $this->helpers[] = 'Chiffrelettre';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');
		$indemnite = $this->getGetParam('indemnite');

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
        $this->set('indemnite',$indemnite);
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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




    public function etatiutstpa() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
        $this->helpers[] = 'Chiffrelettre';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');
		

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
       
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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
/*-------------------------------------*/
//cotisation mutuelle

public function etatmutuelle() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
        $this->helpers[] = 'Chiffrelettre';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');
		

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
       
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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

/*------------------------------------------*/

    public function etatcnss() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
        $this->helpers[] = 'Chiffrelettre';

        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');
		

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
       
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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


    public function etatcarfo() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
        $this->layout = 'blank';
        $this->helpers[] = 'Chiffrelettre';


        $datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		$typesal = $this->getGetParam('typesal');
		

        $rembulletins = $this->Rembulletin->find('all', array('conditions'=>array("Rembulletin.date_debut ='$datedebut'","Rembulletin.date_fin ='$datefin'"),'recursive'=>0, 'order'=>'matricule ASC'));

     

		$this->set('date_debut',$datedebut);
        $this->set('date_fin',$datefin);
        $this->set('typesal',$typesal);
       
        
        $this->set('rembulletins',$rembulletins);
		
        $this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id','libelle'))));

        $this->set('agdossiers',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));
        
        $this->set('charges',$this->Agdossier->find('list',array('list'=>array('id','ag_charge'))));
        /*---------------------------*/
       // $this->set('cotisations', $this->Agcontrat->find('list', array('list'=>array('id','num_cotisation'))));
        $this->set('fonctid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramfonction_id'))));
        $this->set('directid', $this->Agaffectmutation->find('list', array('list'=>array('agcontrat_id','paramdirection_id'))));
        $this->set('modepaieid', $this->Agcontrat->find('list', array('list'=>array('id','parammodepaie_id'))));
        $this->set('numcomptebanq', $this->Agcontrat->find('list', array('list'=>array('id','num_comptebanq'))));
        $this->set('banqid', $this->Agcontrat->find('list', array('list'=>array('id','parambanque_id'))));

       $this->set('cotisation', $this->Agcontrat->find('list', array('list'=>array('id','paramstructurecotsocial_id'))));

        $this->set('classid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramclassification_id'))));
        $this->set('echeid', $this->Agavencement->find('list', array('list'=>array('agcontrat_id','paramechelon_id'))));
        /*---------------------*/
        $this->set('paramclassifications', $this->Paramclassification->find('list', array('list'=>array('id','code'), 'order'=>'id ASC')));

       $this->set('paramechelons', $this->Paramechelon->find('list', array('list'=>array('id','libelle'), 'order'=>'id ASC')));

        $this->set('directions', $this->Paramdirection->find('list', array('list'=>array('id','sigle'), 'order'=>'id ASC')));
		
        $this->set('fonctions', $this->Paramfonction->find('list', array('list'=>array('id','nom_fonction'), 'order'=>'id ASC')));

        $this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id',' 	nom_banque'), 'order'=>'id ASC')));

		$this->set('parammodepaies', $this->Parammodepaie->find('list', array('list'=>array('id',' 	libelle'), 'order'=>'id ASC')));

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
    /*--------------------------------------*/
   

	/*--------------------------------------*/

	public function calculCNSS($bulletinid,$code,$libelle,$avoiret) {
		
		/**************Salaire Brute********************************/
        $x = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulletinid'","Rembulitem.code='500'"), 'recursive'=>0));
        $a = $x[0]['Rembulitem']['montant'];
    
		/**************Taitement de base********************************/
        $alpha = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulletinid'","Rembulitem.code='1'"), 'recursive'=>0));
        $b = $alpha[0]['Rembulitem']['montant'];
        /**************Anciennete********************************/
		$rho = $this->Rembulitem->find('all', array('conditions'=>array("Rembulitem.rembulletin_id='$bulletinid'","Rembulitem.code='11'"), 'recursive'=>0));
        $anciennete = (isset($rho[0]['Rembulitem']['montant']))?$rho[0]['Rembulitem']['montant']:'';
        /*****************/
        $c = 0.055 * $a;
        /*======5.5/100 salaire brute==========*/
        $d = 0.08 * ($b + $anciennete);
        /*===============CNSS=========================*/
        $e = MIN($c,$d,33000);
          //print_r('Base : '.$salairebase.' Brute : '.$salairebrute.' Anc : '.$anciennete);
         // print_r('Brute 0.055 : '.$brute.' Base 0.08 : '.$base.' CNSS : '.$cnss);

        $ret_cnss = array('Rembulitem'=>array(
				        'rembulletin_id'=>$bulletinid,
				        'code'=>$code,
				        'designation'=>$libelle,
					    'base'=> $brute,
					    'montant'=>$e,
					    'avoir_ret'=>$avoiret
					    )		    
			        );
	    $this->Rembulitem->save($ret_cnss);

	    //return $cnss;
	}
    /*--------------------------------------*/
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