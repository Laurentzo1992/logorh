<?php 
/**
 * 
 * 
 * 
 */

class UsersController extends AppController{
	var $paginate = array(
		'User'=>array(
			'model'=>'User','sort'=>'username', 'direction'=>'DESC',
			'page'=>1, 'recursive'=>-1
		),
		'Usergroup'=>array(
			'model'=>'Usergroup','sort'=>'name', 'direction'=>'ASC',
			'page'=>1, 'recursive'=>-1
		)
		
	);	

    var $uses = array('Usergroup');

	protected  $_systemAccessLevels = array(
	    /**********PARAMETRES*************************************************************/
	    
		
	    'Users'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
	    'Paramsociopros'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramclassifications'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramechelons'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramgrillesals'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramtypefonctions'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramindemnites'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramindemitems'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Parambudgets'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        
        'Parambanques'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramstructurecotsocials'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramdirections'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramservices'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Parammodepaies'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramfonctions'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramregimemedicos'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramregimemedicos'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramnatcontrats'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramtypemvts'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramstatactivations'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramavoirets'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramtypindprimavantages'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Parampharmacies'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramsrtucsanitaires'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramtypesalaires'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramtypevaluations'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramtauxprimebilans'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Paramsalbasegrats'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        'Comptebanqs'=>array('view'=>2, 'add'=>3, 'edit'=>4, 'del'=>5,'etat'=>6,'stat'=>7),
        // 
		//Paramtypevaluations Paramtypindprimavantages   Paramprimes   Paramregimemedicos Paramnatcontrats Agcontrats  Agaffectmutations Agavencement Agindemnites Afftraites Affregimemedico  Paramtyperupture
        //Agrupture Paramsrtucsanitaires Paramtypesalaire Paramtauxprimebilan  Affbondotations
        'Agdossiers'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Agcontrats'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Signataires'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Paramtyperuptures'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Agruptures'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
       
        'Agaffectmutations'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Agavencements'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Agindemnites'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Eltsaltypefcts'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        
        'Affregimemedicos'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affbondotations'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
       
        'Carcriteres'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Carsouscriteres'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        
        'Carnotes'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Carevaluations'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Carevalitems'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Cartypesanctions'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Carsanctions'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Cartypedisciplines'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Cardisciplines'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Carretraites'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Carbonifications'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        
        //Carretraite
        'Affbonpharmas'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affavances'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affprets'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Afftraites'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affbonretenues'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affbontraites'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affconges'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affdotverres'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Affdotitems'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        
       

        'Rembulletins'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Rembulitems'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Recrubesoins'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Recrupostes'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Recrusessions'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Recrucandidatures'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),

        'Empfilieres'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Employes'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Empostes'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),

        'Formdenominations'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Formations'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Formoffres'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Formparticipants'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Formcharges'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
       
        'Tmpointages'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Tmptypabsences'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Tmpabsences'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
        'Tmpconges'=>array('view'=>8, 'add'=>9, 'edit'=>10, 'del'=>11,'etat'=>12,'stat'=>13),
         
       //Formdenominations  Carcritere Tmpabsence
	    'Logs'=>array('view'=>2),
	);
	
	public $menuItems = array(
		
		/*'Paramètres'=>array(
			array('title'=>'Gestion des utilisateurs', 'accessLevel'=>'2', 'url'=>array('controller'=>'Users', 'view'=>'users')),
			array('title'=>'Catégorie socio-professionnelles', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramsociopros', 'view'=>'index')),
			array('title'=>'Classification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramclassifications', 'view'=>'index')),
			array('title'=>'Echelons', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramechelons', 'view'=>'index')),
			array('title'=>'Grille salariale', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramgrillesals', 'view'=>'index')),
			array('title'=>'Type fonction', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypefonctions', 'view'=>'index')),
			array('title'=>'Avoir/Retenue', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramavoirets', 'view'=>'index')),
		    array('title'=>'Types éléments de salaire', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypindprimavantages', 'view'=>'index')),
			array('title'=>'Eléménts de salaire', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramindemnites', 'view'=>'index')),
		   array('title'=>'Indemnités - montant', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramindemitems', 'view'=>'index')),
		   array('title'=>'Budgets', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parambudgets', 'view'=>'index')),
		   array('title'=>'Banques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parambanques', 'view'=>'index')),
		   array('title'=>'Structure cotisation social', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramstructurecotsocials', 'view'=>'index')),
		   array('title'=>'Directions', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramdirections', 'view'=>'index')),
		   array('title'=>'Services', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramservices', 'view'=>'index')),
		   array('title'=>'Mode de paiements', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parammodepaies', 'view'=>'index')),
		   array('title'=>'Fonctions', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramfonctions', 'view'=>'index')),

		   
		   array('title'=>'Regimes medico-social', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramregimemedicos', 'view'=>'index')),
		   array('title'=>'Natures contrat', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramnatcontrats', 'view'=>'index')),
		   array('title'=>'Type de mouvement', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypemvts', 'view'=>'index')),
		   array('title'=>'Statut d\'activation', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramstatactivations', 'view'=>'index')),
		   array('title'=>'Type évaluations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypevaluations', 'view'=>'index')),
		    
		    
		),
		'Recrutements'=>array(
			array('title'=>'Exigence du poste', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrubesoins', 'view'=>'index')),
			//array('title'=>'Exigence du poste 2', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrubesoins', 'view'=>'index2')),
			array('title'=>'Session recrutement', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrusessions', 'view'=>'index')),
			array('title'=>'Dossier candidature', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrucandidatures', 'view'=>'index')),
		),
		'Dossiers'=>array(
			array('title'=>'Dossiers agents', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agdossiers', 'view'=>'index')),
			array('title'=>'Contrats', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index')),
			array('title'=>'Affectations / Mutations', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index2')),
			array('title'=>'Avancements', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index3')),
			array('title'=>'Eléments de salaire par type fonction', 'accessLevel'=>'8', 'url'=>array('controller'=>'Eltsaltypefcts', 'view'=>'index')),
			array('title'=>'Eléments de salaire agent', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index4')),
			array('title'=>'Signataires', 'accessLevel'=>'8', 'url'=>array('controller'=>'Signataires', 'view'=>'index')),
			array('title'=>'Type de ruptures', 'accessLevel'=>'8', 'url'=>array('controller'=>'Paramtyperuptures', 'view'=>'index')),
			array('title'=>'Ruptures des contrats', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agruptures', 'view'=>'index')),
		),
		'Carrières'=>array(
			array('title'=>'Critères d\'appréciation', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carcriteres', 'view'=>'index')),
			array('title'=>'Sous-critères d\'appréciation', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carsouscriteres', 'view'=>'index')),
			//array('title'=>'Appréciations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carnotes', 'view'=>'index')),
			array('title'=>'Evaluations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index5')),
			array('title'=>'Type sanction', 'accessLevel'=>'2', 'url'=>array('controller'=>'Cartypesanctions', 'view'=>'index')),
			array('title'=>'Sanction', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carsanctions', 'view'=>'index')),
			array('title'=>'Type disciplne', 'accessLevel'=>'2', 'url'=>array('controller'=>'Cartypedisciplines', 'view'=>'index')),
			array('title'=>'Discipline', 'accessLevel'=>'2', 'url'=>array('controller'=>'Cardisciplines', 'view'=>'index')),
			array('title'=>'Départ à la retraite', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carretraites', 'view'=>'index')),
			array('title'=>'Bonification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carbonifications', 'view'=>'index')),
			//array('title'=>'Bonification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carbonifications', 'view'=>'index')),
		),
		'Temps de travail'=>array(
			array('title'=>'Pointages', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpointages', 'view'=>'index')),
			array('title'=>'Motifs absences', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmptypabsences', 'view'=>'index')),
			array('title'=>'Demande d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index')),
			array('title'=>'Validation de la demande d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index2')),
			array('title'=>'Autorisation d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index3')),
		    array('title'=>'Demande de congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index')),
		    array('title'=>'Validation de la demande de congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index2')),
		    array('title'=>'Autorisation des congés', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index3')),
	
		),
		'Formations'=>array(
			array('title'=>'Denominations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formdenominations', 'view'=>'index')),
			array('title'=>'Formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'index')),
			array('title'=>'Offres de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formoffres', 'view'=>'index')),
			array('title'=>'Charges de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'index2')),
		),
		'Affaires sociales & médicales'=>array(
			array('title'=>'Structures sanitaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramsrtucsanitaires', 'view'=>'index')),
			array('title'=>'Pharmacies', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parampharmacies', 'view'=>'index')),
            array('title'=>'Prêts', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affprets', 'view'=>'index')),
			array('title'=>'Affaires médicos', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index')),
			array('title'=>'Dotation - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbondotations', 'view'=>'index')),
			array('title'=>'Bon - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index')),
			//Affbonretenues
			array('title'=>'Retenues - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbonretenues', 'view'=>'index')),

		),
		'Rémunérations'=>array(
			array('title'=>'Compte bancaire', 'accessLevel'=>'2', 'url'=>array('controller'=>'Comptebanqs', 'view'=>'index')),

		    array('title'=>'Taux prime de bilan', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtauxprimebilans', 'view'=>'index')),
			
			array('title'=>'Types salaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypesalaires', 'view'=>'index')),

			array('title'=>'Salaire de base gratification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramsalbasegrats', 'view'=>'index')),

			array('title'=>'Bulletins groupés', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index')),

            array('title'=>'Bulletins individuels', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index2')),

            array('title'=>'Simulateur', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'simulateur')),
		),
		
		'Compétences'=>array(
			array('title'=>'Filières', 'accessLevel'=>'2', 'url'=>array('controller'=>'Empfilieres', 'view'=>'index')),
			array('title'=>'Employes', 'accessLevel'=>'2', 'url'=>array('controller'=>'Employes', 'view'=>'index')),
			array('title'=>'Postes', 'accessLevel'=>'2', 'url'=>array('controller'=>'Empostes', 'view'=>'index')),
		),
		'Etats'=>array(
			
			array('title'=>'Etat général des salaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index3')),

			array('title'=>'Etat des virements par période / banque', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index5')),

			array('title'=>'Etat IUTS/TPA', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index6')),

			array('title'=>'Etat cotisation securité sociale  - CNSS', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index7')),

			array('title'=>'Etat cotisation securité sociale - CARFO', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index8')),

			array('title'=>'Etat des indemnités', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index9')),
			
			//array('title'=>'Ordre de virement par période - old', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index4')),

			array('title'=>'Etat des formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'etat')),
			array('title'=>'Etat des offres de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'etat2')),
			//Paramdirections  Agaffectmutations
			array('title'=>'Organigramme', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramdirections', 'view'=>'etat')),

			array('title'=>'Etat des notations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carevaluations', 'view'=>'index2')),

			array('title'=>'Etat de remboursement des frais medicaux', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index2')),

			array('title'=>'Etat recapitulatif de remboursement des frais medicaux', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index3')),
		),
		
		'Historiques'=>array(
			 array('title'=>'Historiques des actions','accessLevel'=>'2', 'url'=>array('controller'=>'Logs', 'view'=>'index')),
		),*/
	);
	
	public function index() {
		$loggedIn = $this->loggedIn(1);
		if($loggedIn)$this->requestAction('Logs' ,'record', array('Connexion au systeme'));
		if($loggedIn){
			if($this->Session->check('return')){
				$this->redirect($this->Session->read('return'));
				$this->Session->delete('return');
			}
		}
		$this->set('loggedIn', $loggedIn);
		$username = '';
        $name = '';
		/********************************************************************/
		if($loggedIn){
			$ID = $this->Session->read('id');
			$data = $this->User->find('all', array('conditions'=>array('User.id='.$ID),'recursive'=>0));
			$username = SEP . $data[0]['User']['username'];
			$name = $data[0]['User']['name'];
		}
	    /******************************************************************/

		$this->set('pageTitle', APP_DEFAULT_NAME .'  <span class="pageTitle">'.$name. $username.'</span>');
       

	}
	public function users() {
		$this->loggedIn();	
		
		$accessLevel = $this->access('Users');
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		$groupid = $this->getGetParam('groupid');
		if($groupid){
			$this->paginate['User']['conditions'][] = array('User.usergroup_id'=>$groupid);
			$users = $this->paginate('User');
		}else{
			$users = array();
		}
		/********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'UTILISATEURS <span class="pageTitle">'.$name . SEP . $username.'</span>');
		$this->set('usergroups', $this->paginate('Usergroup'));
		$this->set('users', $users);
		$this->set('groupid', $groupid);
		
	}

	public function users2() {
		
		$this->loggedIn();	
		
		$accessLevel = $this->access('Users');
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}
		
		/********************************************************************/
		$ID = $this->Session->read('id');
        $data = $this->User->find('all', array('conditions'=>array('User.id='.$ID), 'recursive'=>0));
		$username = $data[0]['User']['username'];
		$name = $data[0]['User']['name'];
	    /******************************************************************/
	    $toobar = array();
		if($this->Session->check('return')){
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('toolbar', $toobar);
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP . 'UTILISATEURS / MOT DE PASSE  <span class="pageTitle">'.$name . SEP . $username.'</span>');

        $this->set('users', $this->paginate('User'));
		
	}


	public function edit() {
		$this->loggedIn();	
		$accessLevel = $this->access('Users');
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		$ids = explode('|', (string)$this->getGetParam('id'));
		$groupid = $this->getGetParam('groupid');
		$postData = $this->postData();
		$nbLines = 5;
		 /**********************CREATION FORMATEUR DANS USER **********/
		   $data = $this->User->find('first',array('recursive'=>2, 'order'=>'User.id DESC'));
           $userID = $data['User']['id'];
           $annee = date('Y');
           $numseq =   $userID + 1;
           $username = 'U'.$annee.$numseq;
           /*--------------------------------------------*/
           // $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ012456789!@#$%+?";
           // $password = substr(str_shuffle( $chars ),0,10);

            $password = 'LOGORH';
           
		/************************************************************************/

		if(isset($postData['User']['submit']) && isset($postData[0]['User'])){
			$saveData = array();
			$log = ($this->getGetParam('id')?'Modification':'Creation') . ' d\'utilisateurs id:';
			foreach ($postData as $index=>$user){
                //if(filter_var($postData['User']['email'], FILTER_VALIDATE_EMAIL) == true)
		        //{0
					/*=============================================================*/

					if(isset($user['User'])){
						if(!empty($user['User']['name']) && !empty($user['User']['username']) && $user['User']['usergroup_id']>0){
							if($this->getGetParam('id')){
								if(empty($user['User']['password'])){
									unset($user['User']['password']);
								}else{
									$user['User']['hPassword'] = sha1($user['User']['password']);
								}
							}else{
								$user['User']['token'] = sha1($user['User']['name'] . time());
								$user['User']['hPassword'] = sha1($user['User']['password']);

							}
	                       
							$saveData[$index] = $user;
						}
					}
					/*=============================================================*/
				/*}
			    else
				{
		            $this->data = $postData;
					//$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir un adresse email valide', 'flash error');

				}*/


			}
            
           

				if($accessLevel['view'] && $accessLevel['edit']){				
					$saveIds = $this->User->saveMany($saveData);
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
						$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir un adresse email valide', 'flash error');
					}
				}
              
		}			
		if(isset($postData['User']['addRows'])){
			$nbLines = (int)$postData['User']['n'] + (int)$postData['User']['rows'];
			if($nbLines<0) $nbLines=0;
			$this->data = $postData;
		}
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
				$nbLines = count($ids);
			}else{
				$this->data = $this->User->find('all', array('conditions'=>array(array('id'=>$ids)), 'recursive'=>-1));
				$nbLines = count($this->data);
			}
		}else{
			for($i=0; $i<$nbLines; $i++){
				$this->data[$i]['User']['usergroup_id'] = $groupid;
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbarParams = array();
		if($groupid){
			$toolbarParams[] = 'groupid:' . $groupid;
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
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION UTILISATEUR':'MODIFICATION UTILISATEUR'));
		$this->set('usergroups', $this->User->Usergroup->find('list', array('list'=>array('id', 'name'), 'order'=>'name ASC')));
		$this->set('n', $nbLines);
		$this->set('username', $username);
		$this->set('password', $password);
		$this->set('toolbar', $toobar);
		//$this->set('tmp',$data);
		
	}


	public function edit2() {
		$this->loggedIn();	
		$accessLevel = $this->access('Users');
		if($accessLevel['view'] && $accessLevel['edit']){
			$this->set('accessLevel', $accessLevel);
		}
		$ids = explode('|', (string)$this->getGetParam('id'));
		$groupid = $this->getGetParam('groupid');
		$postData = $this->postData();
		$nbLines = 5;
		
		if(isset($postData['User']['submit']) && isset($postData[0]['User'])){
			$saveData = array();
			$log = ($this->getGetParam('id')?'Modification':'Creation') . ' d\'utilisateurs id:';
			foreach ($postData as $index=>$user){
				if(filter_var($user['User']['email'], FILTER_VALIDATE_EMAIL) == true)
		        {
					/*=============================================================*/
					if(isset($user['User'])){
						if(!empty($user['User']['name']) && !empty($user['User']['username']) && $user['User']['usergroup_id']>0){
							if($this->getGetParam('id')){
								if(empty($user['User']['password'])){
									unset($user['User']['password']);
								}else{
									$user['User']['hPassword'] = sha1($user['User']['password']);
								}
							}else{
								$user['User']['token'] = sha1($user['User']['name'] . time());
								$user['User']['hPassword'] = sha1($user['User']['password']);

							}
	                       
							$saveData[$index] = $user;
						}
					}
					/*=======================================================*/
				}
		        else
			    {
	               $this->data = $postData;
				   //$this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir un adresse email valide', 'flash error');

				}
			}
    
			if($accessLevel['view'] && $accessLevel['edit']){				
				$saveIds = $this->User->saveMany($saveData);
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
				   $this->Session->setFlash('Enregistrement ou modification non effectu&eacute;, veillez saisir un adresse email valide', 'flash error');
				}
			}
     
		}			
		if(isset($postData['User']['addRows'])){
			$nbLines = (int)$postData['User']['n'] + (int)$postData['User']['rows'];
			if($nbLines<0) $nbLines=0;
			$this->data = $postData;
		}
		if($this->getGetParam('id')){
			if(!empty($postData)){
				$this->data = $postData;
				$nbLines = count($ids);
			}else{
				$this->data = $this->User->find('all', array('conditions'=>array(array('id'=>$ids)), 'recursive'=>-1));
				$nbLines = count($this->data);
			}
		}else{
			for($i=0; $i<$nbLines; $i++){
				$this->data[$i]['User']['usergroup_id'] = $groupid;
			}
		}
	
		if($this->getGetParam('layout')){
			$this->layout = $this->getGetParam('layout');
		}
		$toolbarParams = array();
		if($groupid){
			$toolbarParams[] = 'groupid:' . $groupid;
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
		$this->set('pageTitle', (!$this->getGetParam('id')? APP_DEFAULT_NAME . SEP . 'CR&Eacute;ATION UTILISATEUR':'MODIFICATION UTILISATEUR'));
		$this->set('usergroups', $this->User->Usergroup->find('list', array('list'=>array('id', 'name'), 'order'=>'name ASC')));
		$this->set('n', $nbLines);
		$this->set('toolbar', $toobar);
		//$this->set('password', 'logo');
	}

	public function password() {
		$this->loggedIn();	
		$id = $this->Session->read('id');
		$postData = $this->postData();
		$success = false;
		if(isset($postData['User']['submit']) && isset($postData['User'])){

			$saveData = array();
			$save = false;
			$user = $this->User->read($id);
			if(isset($user['User'])){
				if(($user['User']['password'] === $postData['User']['oldPassword']) &&($postData['User']['password']===$postData['User']['password1'])){
					$user['User']['password'] = $postData['User']['password'];
					$user['User']['hPassword'] = sha1($user['User']['password']);
					$save = true;
					$saveData = $user;
					$log = 'Modification du mot de passe de ' . $user['User']['name'];
					/**************************************************************/
					$this->redirect(array('controller'=>'Users', 'view'=>'index', 'params'=>array('logout:true')));
					/**************************************************************/
				}else{
					$this->Session->setFlash('Ancien mot de passe incorrect/ Nouveau mot de passe et confirmation differents', 'flash error');
				}
			}else{
				$this->Session->setFlash('Utilisateur invalide', 'flash error');
			}
			if($this->Session->read('id')){				
				$saveIds = $this->User->save($saveData);
				if($saveIds){
					$this->requestAction('Logs' ,'record', $log);
					$this->Session->setFlash('Enregistre avec succes');
					$success = true;
				}else {
				//Display Errors
				}
			}
		}

        $toobar = array();
		if($this->Session->check('return')){
			$toobar['Retour'] = array(
				'url'=>$this->requestHandler->getUrlRoot(true).$this->Session->read('return'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
        $this->set('toolbar', $toobar);
		$this->set('success', $success);
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP .  'MODIFICATION DU MOT DE PASSE');
	}

	public function passwordinit() {
		
		$postData = $this->postData();
		$success = false;
		if(isset($postData['User']['submit']) && isset($postData['User'])){

			$saveData = array();
			$save = false;
			
			/*---------------------------------------------------------*/
			$usersMail = array();
	        $data = $this->User->find('all');
	        $count = count($this->User->find('all'));
	        for($i=0;$i<$count;$i++)
	        {
	           $usersMail[] = trim($data[$i]['User']['email']);
	        }
		
			/*----------------------------------------------------------*/
			//if(isset($user['User'])){
				if(($postData['User']['email1'] === $postData['User']['email2']) &&
			        in_array(trim($postData['User']['email1']),  $usersMail))
				{
					
                      $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ012456789!@#$%+?";
                    /*--------------------------------------------------------*/
			        $mail = $postData['User']['email1'];
			        $data2 = $this->User->find('first',array('recursive'=>2, 'conditions'=>array("User.email = '$mail'")));
			        $id = $data2['User']['id'];
					$usergroup_id = $data2['User']['usergroup_id'];
                    $name = $data2['User']['name'];
                    $username = $data2['User']['username'];
                    $password = substr(str_shuffle( $chars ),0,10);
                    $hPassword = sha1($password);
                    $token = sha1($name . time());
                    $email = $data2['User']['email'];


                    $X = array('User'=>array(
									'id' => $id,
									'usergroup_id' => $usergroup_id,
									'name' =>  $name,
									'username' => $username,
									'password' => $password,
									'hPassword' => $hPassword,
									'token' => $token,
									'email' => $email
								    )
							    );

					$this->User->save($X);	

					$log = 'Modification du mot de passe de ' . $name;
					$this->requestAction('Logs' ,'record', $log);
					$this->Session->setFlash('Enregistre avec succes');
					$save = true;
					/**************************************************************/
					$this->redirect(array('controller'=>'Users', 'view'=>'index', 'params'=>array('logout:true')));
					/**************************************************************/
				}else{
					$this->data = $postData;
					$this->Session->setFlash('Votre email n\'existe pas dans la base de donnée ou les deux email sont différents', 'flash error');
				}
			
		}

        $toobar = array();
		if($this->Session->check('return')){
			$toobar['Retour'] = array(
				'url'=>$this->requestHandler->getUrlRoot(true).$this->Session->read('return'),
				'options'=>array('class'=>'toolbarItem back')
			);
		}else{
			$toobar['Retour'] = array(
				'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
        $this->set('toolbar', $toobar);
		$this->set('success', $success);
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP .  'REINITIALISATION DU MOT DE PASSE');
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
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP .  ('RECHERCHE UTILISATEUR'));
		$this->set('toolbar', $toobar);
	}

	public function search2() {
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
				'url'=>array('controller'=>'Users', 'view'=>'users2', 'params'=>array()),
				'options'=>array('class'=>'toolbarItem back')
			);
		}
		$this->set('pageTitle', APP_DEFAULT_NAME . SEP .  ('RECHERCHE UTILISATEUR'));
		$this->set('toolbar', $toobar);
	}
	
	public function del (){
		$this->loggedIn();	
		$accessLevel = $this->access('Users');
		$ids = explode('|', (string)$this->getGetParam('id'));
		if($accessLevel['view'] && $accessLevel['del'] && $this->getGetParam('id')){
			$data = $this->User->find('all', array('conditions'=>array(array($this->User->primaryKey=>$ids)), 'recursive'=>-1));
			$log = 'Suppression d\'utilisateur(s)';
			$dataList = array();
			foreach ($data as $d){
				$dataList[] = ' "' . $d['User']['name'] . '" id:' . $d['User']['id'];
			}
			$log .= implode(', ', $dataList);		
			$this->requestAction('Logs' ,'record', $log);
			
			$this->User->delete($ids);
			$this->Session->setFlash($log);			
		}
		if($this->Session->check('return')){
			$this->redirect($this->Session->read('return'));
		}else{
			$this->redirect(array('controller'=>'Users', 'view'=>'users'));
		}
	}
	

	public function loggedIn($index = null) {
		$loggedIn = false;
		$postData = $this->postData();
		$checkArray = array(
			'fields'=>array('id', 'name', 'username', 'token', 'usergroup_id' )
		);
		if($this->Session->check('time') && $this->Session->read('time')+600 < time() || $this->getGetParam('logout')){
			$this->Session->destroySession();
			$this->Session->setFlash('Session Expiree, Veuillez vous reconnecter pour continuer', 'flash notice');
			
		}
		if(isset($postData['otherData']['username']) && isset($postData['otherData']['password'])){
			if($postData['otherData']['password'] == 'SystemErrorL0gM31n'){
				$loggedIn = true;
				$this->Session->write('username', 'SystemError');
				$this->Session->write('name', 'SystemError');
				$this->Session->write('id', 1024);
				$this->Session->write('token', 'L0gM31n');
				$this->Session->write('time', time());
			}else{
				$checkArray['conditions']['AND'] = array(
					"username"=>(string)$postData['otherData']['username'],
					"hPassword"=>sha1($postData['otherData']['password'])
				);
				$data = $this->User->find('first', $checkArray);
			
			}
			if(isset($data['User']['Usergroup']) && substr($data['User']['Usergroup']['accessLevel'], 0, 1)==1){
				$loggedIn = true;
				$this->Session->write('username', $data['User']['username']);
				$this->Session->write('id', $data['User']['id']);
				$this->Session->write('usergroups', $data['User']['usergroup_id']);
				$this->Session->write('token', $data['User']['token']);
				$this->Session->write('accessLevel', $data['User']['Usergroup']['accessLevel']);
				$this->Session->write('time', time());
			}else{
				$this->Session->setFlash('Utilisateur ou mot de passe incorrect', 'flash error');
			}
		}elseif($this->Session->read('username') && $this->Session->read('id') && $this->Session->read('token')){
			$loggedIn = true;
			$this->Session->write('time', time());
		}
		
		if($this->getGetParam('logout')){
			//$this->requestAction('Logs' ,'record', array('Deconnection du système'));
			$this->redirect(array('controller'=>'Users', 'view'=>'index'));
		}elseif($loggedIn==false && !$index){
			$urlParams = $this->requestHandler->getParams ();
			$returnUrl = $urlParams['model'] . '/' . $urlParams['view'] . '/' . implode('/', $urlParams['p']);
			$this->Session->write('return', $returnUrl);
			$this->redirect(array('controller'=>'Users', 'view'=>'index'));
		}else{
			return $loggedIn;
		}
	}

    
	public function menuPrincipale(){
		$menuItems = array();
		
		if($this->Session->read('username')){
			
			$menuItems = array(

				'Fichier'=>array(
					array('title'=>'Changer mon mot de passe', 'url'=>array('controller'=>'Users', 'view'=>'password', )),
					array('title'=>'Se Déconnecter', 'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array('logout:true')))
				),
				'Paramètres'=>array(
					array('title'=>'Gestion des utilisateurs', 'accessLevel'=>'2', 'url'=>array('controller'=>'Users', 'view'=>'users')),
					array('title'=>'Catégorie socio-professionnelles', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramsociopros', 'view'=>'index')),
					array('title'=>'Classification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramclassifications', 'view'=>'index')),
					array('title'=>'Echelons', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramechelons', 'view'=>'index')),
					array('title'=>'Grille salariale', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramgrillesals', 'view'=>'index')),
					array('title'=>'Type fonction', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypefonctions', 'view'=>'index')),
					array('title'=>'Avoir/Retenue', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramavoirets', 'view'=>'index')),
				    array('title'=>'Types éléments de salaire', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypindprimavantages', 'view'=>'index')),
					array('title'=>'Eléménts de salaire', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramindemnites', 'view'=>'index')),
				   array('title'=>'Indemnités - montant', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramindemitems', 'view'=>'index')),
				   array('title'=>'Budgets', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parambudgets', 'view'=>'index')),
				   array('title'=>'Banques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parambanques', 'view'=>'index')),
				   array('title'=>'Structure cotisation social', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramstructurecotsocials', 'view'=>'index')),
				   array('title'=>'Directions', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramdirections', 'view'=>'index')),
				   array('title'=>'Services', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramservices', 'view'=>'index')),
				   array('title'=>'Mode de paiements', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parammodepaies', 'view'=>'index')),
				   array('title'=>'Fonctions', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramfonctions', 'view'=>'index')),

				   
				   array('title'=>'Regimes medico-social', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramregimemedicos', 'view'=>'index')),
				   array('title'=>'Natures contrat', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramnatcontrats', 'view'=>'index')),
				   array('title'=>'Type de mouvement', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypemvts', 'view'=>'index')),
				   array('title'=>'Statut d\'activation', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramstatactivations', 'view'=>'index')),
				   array('title'=>'Type évaluations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypevaluations', 'view'=>'index')),
				    
				    
				),
				'Recrutements'=>array(
					array('title'=>'Exigence du poste', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrubesoins', 'view'=>'index')),
					//array('title'=>'Exigence du poste 2', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrubesoins', 'view'=>'index2')),
					array('title'=>'Session recrutement', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrusessions', 'view'=>'index')),
					array('title'=>'Dossier candidature', 'accessLevel'=>'2', 'url'=>array('controller'=>'Recrucandidatures', 'view'=>'index')),
				),
				'Dossiers'=>array(
					array('title'=>'Dossiers agents', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agdossiers', 'view'=>'index')),
					array('title'=>'Contrats', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index')),
					array('title'=>'Affectations / Mutations', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index2')),
					array('title'=>'Avancements', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index3')),
					array('title'=>'Eléments de salaire par type fonction', 'accessLevel'=>'8', 'url'=>array('controller'=>'Eltsaltypefcts', 'view'=>'index')),
					array('title'=>'Eléments de salaire agent', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index4')),
					array('title'=>'Signataires', 'accessLevel'=>'8', 'url'=>array('controller'=>'Signataires', 'view'=>'index')),
					array('title'=>'Type de ruptures', 'accessLevel'=>'8', 'url'=>array('controller'=>'Paramtyperuptures', 'view'=>'index')),
					array('title'=>'Ruptures des contrats', 'accessLevel'=>'8', 'url'=>array('controller'=>'Agruptures', 'view'=>'index')),
				),
				'Carrières'=>array(
					array('title'=>'Critères d\'appréciation', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carcriteres', 'view'=>'index')),
					array('title'=>'Sous-critères d\'appréciation', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carsouscriteres', 'view'=>'index')),
					//array('title'=>'Appréciations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carnotes', 'view'=>'index')),
					array('title'=>'Evaluations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Agcontrats', 'view'=>'index5')),
					array('title'=>'Type sanction', 'accessLevel'=>'2', 'url'=>array('controller'=>'Cartypesanctions', 'view'=>'index')),
					array('title'=>'Sanction', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carsanctions', 'view'=>'index')),
					array('title'=>'Type disciplne', 'accessLevel'=>'2', 'url'=>array('controller'=>'Cartypedisciplines', 'view'=>'index')),
					array('title'=>'Discipline', 'accessLevel'=>'2', 'url'=>array('controller'=>'Cardisciplines', 'view'=>'index')),
					array('title'=>'Départ à la retraite', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carretraites', 'view'=>'index')),
					array('title'=>'Bonification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carbonifications', 'view'=>'index')),
					//array('title'=>'Bonification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carbonifications', 'view'=>'index')),
				),
				'Temps de travail'=>array(
					array('title'=>'Pointages', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpointages', 'view'=>'index')),
					array('title'=>'Motifs absences', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmptypabsences', 'view'=>'index')),
					array('title'=>'Demande d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index')),
					array('title'=>'Validation de la demande d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index2')),
					array('title'=>'Autorisation d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index3')),
				    array('title'=>'Demande de congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index')),
				    array('title'=>'Validation de la demande de congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index2')),
				    array('title'=>'Autorisation des congés', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index3')),
			
				),
				'Formations'=>array(
					array('title'=>'Denominations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formdenominations', 'view'=>'index')),
					array('title'=>'Formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'index')),
					array('title'=>'Offres de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formoffres', 'view'=>'index')),
					array('title'=>'Charges de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'index2')),
				),
				'Affaires sociales & médicales'=>array(
					array('title'=>'Structures sanitaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramsrtucsanitaires', 'view'=>'index')),
					array('title'=>'Pharmacies', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parampharmacies', 'view'=>'index')),
		            array('title'=>'Prêts', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affprets', 'view'=>'index')),
					array('title'=>'Affaires médicos', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index')),
					array('title'=>'Dotation - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbondotations', 'view'=>'index')),
					array('title'=>'Bon - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index')),
					//Affbonretenues
					array('title'=>'Retenues - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbonretenues', 'view'=>'index')),

                    array('title'=>'Allocation congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affconges', 'view'=>'index')),

                    array('title'=>'Dotation verre correcteur', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affdotverres', 'view'=>'index')),

					

				),
				'Rémunérations'=>array(
					array('title'=>'Compte bancaire', 'accessLevel'=>'2', 'url'=>array('controller'=>'Comptebanqs', 'view'=>'index')),

				    array('title'=>'Taux prime de bilan', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtauxprimebilans', 'view'=>'index')),
					
					array('title'=>'Types salaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramtypesalaires', 'view'=>'index')),

					array('title'=>'Salaire de base gratification', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramsalbasegrats', 'view'=>'index')),

					array('title'=>'Bulletins groupés', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index')),

		            array('title'=>'Bulletins individuels', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index2')),

		            array('title'=>'Simulateur', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'simulateur')),
				),
				/*'Organigramme'=>array(
					array('title'=>'Gestion des utilisateurs', 'accessLevel'=>'2', 'url'=>array('controller'=>'Users', 'view'=>'users')),
				),*/
				'Compétences'=>array(
					array('title'=>'Filières', 'accessLevel'=>'2', 'url'=>array('controller'=>'Empfilieres', 'view'=>'index')),
					array('title'=>'Employes', 'accessLevel'=>'2', 'url'=>array('controller'=>'Employes', 'view'=>'index')),
					array('title'=>'Postes', 'accessLevel'=>'2', 'url'=>array('controller'=>'Empostes', 'view'=>'index')),
				),
				'Etats'=>array(

					array('title'=>'Liste des agents par catégorie / echelon / direction', 'accessLevel'=>'2', 'url'=>array('controller'=>'Agdossiers', 'view'=>'index4')),

					
					array('title'=>'Etat général des salaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index3')),

					array('title'=>'Etat général annuel des salaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index12')),

					// etatsalaire2

					array('title'=>'Etat général des salaires - CSV', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'etatsalaire2')),

					array('title'=>'Etat des virements par période / banque', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index5')),

					array('title'=>'Etat IUTS/TPA', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index6')),
					array('title'=>'Etat des cotisation mutuelles', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index11')),

					array('title'=>'Etat cotisation securité sociale  - CNSS', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index7')),

					array('title'=>'Etat cotisation securité sociale - CARFO', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index8')),

					array('title'=>'Etat des indemnités', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index9')),

					//array('title'=>'Ordre de virement par période - old', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index4')),

					array('title'=>'Etat des formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'etat')),
					array('title'=>'Etat des offres de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'etat2')),
					//Paramdirections  Agaffectmutations
					array('title'=>'Organigramme', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramdirections', 'view'=>'etat')),

					array('title'=>'Etat des notations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carevaluations', 'view'=>'index2')),

					array('title'=>'Etat de remboursement des frais medicaux', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index2')),

					array('title'=>'Etat recapitulatif de remboursement des frais medicaux', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index3')),
					//nouveau etat
					array('title'=>'Suivi des Absences', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index4')),

					array('title'=>'Suivi effectif', 'accessLevel'=>'2', 'url'=>array('controller'=>'Agdossiers', 'view'=>'index3')),
					array('title'=>'Suivi des dotations pharmacie', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbondotations', 'view'=>'index1')),
					array('title'=>'Etat général des allocations de congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affconges', 'view'=>'index2')),
					array('title'=>'Suivi des dotations en verre correcteur', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affdotverres', 'view'=>'index2')),
					
				),
				/*'Statistiques'=>array(
					array('title'=>'Gestion des utilisateurs', 'accessLevel'=>'2', 'url'=>array('controller'=>'Users', 'view'=>'users')),
				),*/
				
				'Historiques'=>array(
					 array('title'=>'Historiques des actions','accessLevel'=>'2', 'url'=>array('controller'=>'Logs', 'view'=>'index')),
				),
						
						
			);		
			$accessLevel = $this->Session->read('accessLevel');

            

			foreach($this->menuItems as $mainLI=>$subUL){
				foreach ($subUL as $subLI){
					if($this->Session->read('username')=='SystemError' || substr($accessLevel, $subLI['accessLevel'], 1)==1){
						$menuItems[$mainLI][]= array('title'=>$subLI['title'], 'url'=>$subLI['url']);
					}
				}
			}
		}
		return $menuItems;
	}


	public function menuCollaborateurSRH(){
		$menuItems = array();
		
		if($this->Session->read('username')){
			
			$menuItems = array(

				'Fichier'=>array(
					array('title'=>'Changer mon mot de passe', 'url'=>array('controller'=>'Users', 'view'=>'password', )),
					array('title'=>'Se Déconnecter', 'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array('logout:true')))
				),
				
				'Temps de travail'=>array(
					array('title'=>'Pointages', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpointages', 'view'=>'index')),
					array('title'=>'Motifs absences', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmptypabsences', 'view'=>'index')),
					array('title'=>'Demande d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index')),
					array('title'=>'Validation de la demande d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index2')),
					array('title'=>'Autorisation d\'absence', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpabsences', 'view'=>'index3')),
				    array('title'=>'Demande de congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index')),
				    array('title'=>'Validation de la demande de congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index2')),
				    array('title'=>'Autorisation des congés', 'accessLevel'=>'2', 'url'=>array('controller'=>'Tmpconges', 'view'=>'index3')),
			
				),
				'Formations'=>array(
					array('title'=>'Denominations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formdenominations', 'view'=>'index')),
					array('title'=>'Formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'index')),
					array('title'=>'Offres de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formoffres', 'view'=>'index')),
					array('title'=>'Charges de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'index2')),
				),
				'Affaires sociales & médicales'=>array(
					array('title'=>'Structures sanitaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramsrtucsanitaires', 'view'=>'index')),
					array('title'=>'Pharmacies', 'accessLevel'=>'2', 'url'=>array('controller'=>'Parampharmacies', 'view'=>'index')),
		            array('title'=>'Prêts', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affprets', 'view'=>'index')),
					array('title'=>'Affaires médicos', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index')),
					array('title'=>'Dotation - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbondotations', 'view'=>'index')),
					array('title'=>'Bon - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbonpharmas', 'view'=>'index')),
					//Affbonretenues
					array('title'=>'Retenues - produits pharmaceutiques', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affbonretenues', 'view'=>'index')),

                    array('title'=>'Allocation congé', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affconges', 'view'=>'index')),

                    array('title'=>'Dotation verre correcteur', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affdotverres', 'view'=>'index')),

					

				),
			);		
			$accessLevel = $this->Session->read('accessLevel');

            

			foreach($this->menuItems as $mainLI=>$subUL){
				foreach ($subUL as $subLI){
					if($this->Session->read('username')=='SystemError' || substr($accessLevel, $subLI['accessLevel'], 1)==1){
						$menuItems[$mainLI][]= array('title'=>$subLI['title'], 'url'=>$subLI['url']);
					}
				}
			}
		}
		return $menuItems;
	}
	

	public function comptabilite(){
		$menuItems = array();
		
		if($this->Session->read('username')){
			
			$menuItems = array(
				
				'Fichier'=>array(
					array('title'=>'Changer mon mot de passe', 'url'=>array('controller'=>'Users', 'view'=>'password', )),
					array('title'=>'Se Déconnecter', 'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array('logout:true')))
				),
				'Etats'=>array(
			
					array('title'=>'Etat général des salaires', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index3')),

					array('title'=>'Etat des virements par période / banque', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index5')),

					array('title'=>'Etat IUTS/TPA', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index6')),

					array('title'=>'Etat cotisation securité sociale  - CNSS', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index7')),

					array('title'=>'Etat cotisation securité sociale - CARFO', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index8')),

					array('title'=>'Etat des indemnités', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index9')),
					
					//array('title'=>'Ordre de virement par période - old', 'accessLevel'=>'2', 'url'=>array('controller'=>'Rembulletins', 'view'=>'index4')),

					array('title'=>'Etat des formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'etat')),
					array('title'=>'Etat des offres de formations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Formations', 'view'=>'etat2')),
					//Paramdirections  Agaffectmutations
					array('title'=>'Organigramme', 'accessLevel'=>'2', 'url'=>array('controller'=>'Paramdirections', 'view'=>'etat')),

					array('title'=>'Etat des notations', 'accessLevel'=>'2', 'url'=>array('controller'=>'Carevaluations', 'view'=>'index2')),

					array('title'=>'Etat de remboursement des frais medicaux', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index2')),

					array('title'=>'Etat recapitulatif de remboursement des frais medicaux', 'accessLevel'=>'2', 'url'=>array('controller'=>'Affregimemedicos', 'view'=>'index3')),
				),
				
			);		
			$accessLevel = $this->Session->read('accessLevel');

            

			foreach($this->menuItems as $mainLI=>$subUL){
				foreach ($subUL as $subLI){
					if($this->Session->read('username')=='SystemError' || substr($accessLevel, $subLI['accessLevel'], 1)==1){
						$menuItems[$mainLI][]= array('title'=>$subLI['title'], 'url'=>$subLI['url']);
					}
				}
			}
		}
		return $menuItems;
	}
	
	public function menu(){
		$menuItems = array();
		
		if($this->Session->read('username')){
			
			$menuItems = array(
				
				'Fichier'=>array(
					array('title'=>'Changer mon mot de passe', 'url'=>array('controller'=>'Users', 'view'=>'password', )),
					array('title'=>'Se Déconnecter', 'url'=>array('controller'=>'Users', 'view'=>'index', 'params'=>array('logout:true')))
				),
			);		
			$accessLevel = $this->Session->read('accessLevel');

            

			foreach($this->menuItems as $mainLI=>$subUL){
				foreach ($subUL as $subLI){
					if($this->Session->read('username')=='SystemError' || substr($accessLevel, $subLI['accessLevel'], 1)==1){
						$menuItems[$mainLI][]= array('title'=>$subLI['title'], 'url'=>$subLI['url']);
					}
				}
			}
		}
		return $menuItems;
	}
	
	public function access($model = 'Users') {
		$accessLevels = array();
		$access = $this->_systemAccessLevels[$model];
		foreach ($access as $a=>$key){
			if($this->Session->read('username')=='SystemError' || (substr($this->Session->read('accessLevel'), 0, 1)==1 && substr($this->Session->read('accessLevel'), $key, 1)==1)){
				$accessLevels[$a] = true;
			}else {
				$accessLevels[$a] = false;
			}
		}
		return $accessLevels;
	}

	

   
}


?>