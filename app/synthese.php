<?php

	   echo '<h1>La fiche d\'evalution synthèse</h1>';
	   echo '<table  width="70%" cellspacing="0" class="table trHover borderBottom" border="1">';	   
                       echo'<thead>
                          <tr>
                            <th>Appréciation</th>
                            <th colspan="3">Les formateurs</th>
                            <th colspan="4">Environnement et moyens pédagogiques</th>
                            <th colspan="3">La formation</th>
                          </tr>
                          <tr>
                            <td></td>
                            <td>Matrise du sujet(%)</td>
                            <td>Qualités pédagogiques(%)</td>
                            <td>Compréhension du sujet(%)</td>
                            <td>Accueil (%)</td>
                            <td>Salle de formation(%)</td>
                            <td>Matériel informatique(%)</td>
                            <td>Support de formation(%)</td>
                            <td>Adaptée à mon niveau(%)</td>
                            <td>Conforme à la demande(%)</td>
                            <td>Correspond à mon besoin(%)</td>
                          </tr>
                        </thead>';
                            echo'<tr "class"="tr1">
                                <td>Très bien</td>
                                <td>'.(($maitriseTB / $nbrePart) * 100).'</td>
                                <td>'.(($qualiteTB / $nbrePart) * 100).'</td>
                                <td>'.(($comprehensionTB / $nbrePart) * 100).'</td>
                                <td>'.(($accueilTB / $nbrePart) *100).'</td>
                                <td>'.(($salleTB / $nbrePart) * 100).'</td>
                                <td>'.(($materielTB / $nbrePart) * 100).'</td>
                                <td>'.(($supportTB / $nbrePart) * 100).'</td>
                                <td>'.(($adapteTB/ $nbrePart) * 100).'</td>
                                <td>'.(($conformeTB / $nbrePart) * 100).'</td>
                                <td>'.(($correspondTB / $nbrePart) * 100).'</td>
                            </tr>';
                            echo'<tr>
                                <td>Bien</td>
                                <td>'.(($maitriseB / $nbrePart) * 100).'</td>
                                <td>'.(($qualiteB / $nbrePart) * 100).'</td>
                                <td>'.(($comprehensionB / $nbrePart) * 100).'</td>
                                <td>'.(($accueilB / $nbrePart) *100).'</td>
                                <td>'.(($salleB / $nbrePart) * 100).'</td>
                                <td>'.(($materielB / $nbrePart) * 100).'</td>
                                <td>'.(($supportB / $nbrePart) * 100).'</td>
                                <td>'.(($adapteB/ $nbrePart) * 100).'</td>
                                <td>'.(($conformeB / $nbrePart) * 100).'</td>
                                <td>'.(($correspondB / $nbrePart) * 100).'</td>
                            </tr>';
                            echo'<tr>
                                <td>Moyen</td>
                                <td>'.(($maitriseM / $nbrePart) * 100).'</td>
                                <td>'.(($qualiteM / $nbrePart) * 100).'</td>
                                <td>'.(($comprehensionM / $nbrePart) * 100).'</td>
                                <td>'.(($accueilM / $nbrePart) *100).'</td>
                                <td>'.(($salleM / $nbrePart) * 100).'</td>
                                <td>'.(($materielM / $nbrePart) * 100).'</td>
                                <td>'.(($supportM / $nbrePart) * 100).'</td>
                                <td>'.(($adapteM/ $nbrePart) * 100).'</td>
                                <td>'.(($conformeM / $nbrePart) * 100).'</td>
                                <td>'.(($correspondM / $nbrePart) * 100).'</td>
                            </tr>';
                            echo'<tr>
                                <td>Faible</td>
                                <td>'.(($maitriseF / $nbrePart) * 100).'</td>
                                <td>'.(($qualiteF / $nbrePart) * 100).'</td>
                                <td>'.(($comprehensionF / $nbrePart) * 100).'</td>
                                <td>'.(($accueilF / $nbrePart) *100).'</td>
                                <td>'.(($salleF / $nbrePart) * 100).'</td>
                                <td>'.(($materielF / $nbrePart) * 100).'</td>
                                <td>'.(($supportF / $nbrePart) * 100).'</td>
                                <td>'.(($adapteF/ $nbrePart) * 100).'</td>
                                <td>'.(($conformeF / $nbrePart) * 100).'</td>
                                <td>'.(($correspondF / $nbrePart) * 100).'</td>
                            </tr>';
                           
                             echo'<tr>
                                <td>Insufisant</td>
                                <td>'.(($maitriseI / $nbrePart) * 100).'</td>
                                <td>'.(($qualiteI / $nbrePart) * 100).'</td>
                                <td>'.(($comprehensionI / $nbrePart) * 100).'</td>
                                <td>'.(($accueilI / $nbrePart) *100).'</td>
                                <td>'.(($salleI / $nbrePart) * 100).'</td>
                                <td>'.(($materielI / $nbrePart) * 100).'</td>
                                <td>'.(($supportI / $nbrePart) * 100).'</td>
                                <td>'.(($adapteI/ $nbrePart) * 100).'</td>
                                <td>'.(($conformeI / $nbrePart) * 100).'</td>
                                <td>'.(($correspondI / $nbrePart) * 100).'</td>
                            </tr>';
					        /**************************************************************************/
						  $fomateur = (((($maitriseTB / $nbrePart) +($qualiteTB / $nbrePart) +  ($comprehensionTB / $nbrePart) +  ($maitriseB / $nbrePart) + ($qualiteB / $nbrePart)  +  ($comprehensionB / $nbrePart)   +  ($maitriseM / $nbrePart) + ($qualiteM / $nbrePart)   + ($comprehensionM / $nbrePart) 
                              +  ($maitriseF / $nbrePart) + ($qualiteF / $nbrePart)  +  ($comprehensionF / $nbrePart) 
                              +  ($maitriseI / $nbrePart) + ($qualiteI / $nbrePart)   + ($comprehensionI / $nbrePart)) * 100)/15);
			  
			                  $environ = (((($accueilTB / $nbrePart)+($salleTB / $nbrePart)+($materielTB / $nbrePart)+($supportTB / $nbrePart) +   ($accueilB / $nbrePart)+($salleB / $nbrePart)+($materielB / $nbrePart)+($supportB / $nbrePart)      +   ($accueilM / $nbrePart)+($salleM / $nbrePart)+($materielM / $nbrePart)+($supportM / $nbrePart)     +   ($accueilF / $nbrePart)+($salleF / $nbrePart)+($materielF / $nbrePart)+($supportF / $nbrePart)     +   ($accueilI / $nbrePart)+($salleI / $nbrePart)+($materielI / $nbrePart)+($supportI / $nbrePart)) * 100)/20);
                           
			                  $formation = (((($adapteTB / $nbrePart) +($conformeTB / $nbrePart) +  ($correspondTB / $nbrePart)   +   ($adapteB / $nbrePart) + ($conformeB / $nbrePart)  +  ($correspondB / $nbrePart)    +   ($adapteM / $nbrePart) + ($conformeM / $nbrePart)   + ($correspondM / $nbrePart) 
                              +    ($adapteF / $nbrePart) + ($conformeF / $nbrePart)  +  ($correspondF / $nbrePart) 
                              +    ($adapteI / $nbrePart) + ($conformeI / $nbrePart)   + ($correspondI / $nbrePart)) * 100)/15);
                            
							/***************************************************************************/
                  
				  echo' </table>';
     			   
				  echo'<table id="tab2" width="70%" cellspacing="0" class="table trHover borderBottom" border="1">';
                       echo'<thead>
                          <tr>
                            <th></th>
                            <th>Les formateurs (%)</th>
                            <th>Environnement et moyens pédagogiques (%)</th>
                            <th>La formation (%)</th>
                          </tr>
                        </thead>';
                         echo'<tr>
                            <td>Moyenne</td>
                            <td>'.$fomateur.'</td>
                            <td>'.$environ.'</td>
                            <td>'.$formation.'</td>
                        </tr>';
						
                    echo'</table>';
					
?>
<h1 style="margin:15px auto 30px auto; color:#fff;">Note</h1>
<canvas id="gauge1" width="200" height="200"></canvas>
<?php
         
			  $taux = ($fomateur + $environ + $formation) / 3;	
              if($taux>0 && $taux <=10){$color = '#FF0912';}
			  elseif($taux>10 && $taux <=25){$color = '#ffa500';}
			  elseif($taux>25 && $taux <=45){$color = '#FFD700';}
			  elseif($taux>45 && $taux <60){$color = '#E7F00D';}
			  elseif($taux>=60 && $taux <=90){$color = '#32CD32';}
              else{$color = '#0C810D';}	
?>			  
<script>
$(document).ready(function (){
	$("#gauge1").gauge(<?php echo number_format($taux,2); ?>,{color: "<?php echo $color;?>"});
    $('#example').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excelHtml5',
					'csvHtml5',
					'pdfHtml5'
				]
			}); 
	});
</script>