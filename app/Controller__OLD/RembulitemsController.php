<?php 
/**
 * 
 * 
 * 
 */

class RembulitemsController extends AppController{	
	var $paginate = array(
		'Rembulitem'=>array(
			'model'=>'Rembulitem','sort'=>'id', 'rembulitem'=>'DESC',
			'page'=>1, 'recursive'=>0, 'limit'=>15
		),
		
	
	);	
	
	var $uses = array('User','Agdossier','Rembulletin','Parambanque');
	
	

	public function etat() {

		$this->requestAction('Users' ,'loggedIn');
		include_once '../boot/params.php';	
		$accessLevel = $this->requestAction('Users' ,'access', array('Rembulletins'));
		if($accessLevel['view']){
			$this->set('accessLevel', $accessLevel);
		}

		$datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		//$typesal = $this->getGetParam('typesal');
        
		
		$this->layout = 'blank';
		//$this->set('salaires',$salaires);	

		$this->set('datedebut',$datedebut);
		$this->set('datefin',$datefin);
		//$this->set('typesal',$typesal);

		$this->set('nomprenoms',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('matricules',$this->Agdossier->find('list',array('list'=>array('id','ag_matricule')
		,  'order'=>'Agdossier.ag_matricule ASC')));
		
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

		$datedebut = $this->getGetParam('datedebut');
		$datefin = $this->getGetParam('datefin');
		//$typesal = $this->getGetParam('typesal');
        
		
		$this->layout = 'blank';
		//$this->set('salaires',$salaires);	

		$this->set('datedebut',$datedebut);
		$this->set('datefin',$datefin);
		//$this->set('typesal',$typesal);

		$this->set('parambanques', $this->Parambanque->find('list', array('list'=>array('id','nom_banque'), 'order'=>'id ASC')));

		$this->set('nomprenoms',$this->Agdossier->find('list',array('list'=>array('id','VirtualFields.name')
		, 'fields'=>'id, CONCAT(ag_nom," ",ag_prenom) as name',  'order'=>'Agdossier.ag_nom ASC')));

		$this->set('matricules',$this->Agdossier->find('list',array('list'=>array('id','ag_matricule')
		,  'order'=>'Agdossier.ag_matricule ASC')));
		
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

	
	



}
?>