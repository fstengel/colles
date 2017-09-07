<?php

require("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
//Changé de require vers require_once
require_once(libPath()."Session.php");

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAcces(racineSite."admin/GererCrenaux.php",array("admin"),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

function tableauPersonnes() {
	global $accesDB;
	
	$prof = array();
	$reqSQL = "SELECT Concat(Nom,' ',Prenom) as NP, id_personne FROM ".PrefixeDB."Personne ORDER BY concat(Nom,Prenom) ASC";
	$res = $accesDB->ExecRequete($reqSQL);
	while ($ligne=$accesDB->LigneSuivante($res)) {
		$prof[] = $ligne;
	}
	return $prof;	
}

function tableauIntervenants() {
	global $accesDB;
	
	$inter = array();
	$reqSQL = "SELECT Concat(p.Nom,' ',p.Prenom) as NP, m.Nom as NM, i.id_intervenant FROM "
		.PrefixeDB."Personne as p, "
		.PrefixeDB."Matiere as m, "
		.PrefixeDB."Intervenant as i"
		." WHERE i.Personne=p.id_personne AND i.Matiere=m.id_matiere ORDER BY Concat(NP,NM) ASC";
	$res = $accesDB->ExecRequete($reqSQL);
	while ($ligne=$accesDB->LigneSuivante($res)) {
		$inter[] = $ligne;
	}
	return $inter;	
	
}

function popupIntervenant($ti,$i=-1) {
	$p = "";
	foreach ($ti as $li) {
		$ii = $li['id_intervenant'];
		$np = $li['NP']." (".$li['NM'].")";
		$p .= '<option value="'.$ii.'"';
		if ($i==$ii) $p .= " selected ";
		$p .= '>'.$np.'</option>\n';
	}
	return $p;
}

function popupPersonne($tp, $i=-1) {
	$p = "";
	foreach ($tp as $lp) {
		$ip = $lp['id_personne'];
		$np = $lp['NP'];
		$p .= '<option value="'.$ip.'"';
		if ($i==$ip) $p .= " selected ";
		$p .= '>'.$np.'</option>\n';
	}
	return $p;	
}

function popupPris($pp) {
	$p = "<option value='libre'"; if ($pp=='libre') $p .= " selected";
	$p .= ">Libre</option>\n";
	$p .= "<option value='pris'"; if ($pp=='pris') $p .= " selected";
	$p .= ">Pris</option>\n";
	$p .= "<option value='reserve'"; if ($pp=='reserve') $p .= " selected";
	$p .= ">R&eacute;serv&eacute;</option>\n";
	return $p;
}


function actionListeCrenaux() {
	global $accesDB;
	global $session;
	
	$tInter = tableauIntervenants();
	$tPers = tableauPersonnes();
	
	//var_dump($tInter); echo "<BR>";
	
	if (isset($_POST['valid'])) {
		//print_r($_POST); echo "<BR>";
		switch ($_POST['valid']) {
			case "Modifier":
				if (isset($_POST['modifier'])) {
					$modifier = $_POST['modifier'];
					echo "On modifie ";
					foreach ($modifier as $nlig=>$id_crenau) {
						// On modifie via un UPDATE !
						//echo "Modification $nlig, $id_crenau<BR>";
						$reqSQL = "UPDATE ".PrefixeDB."Crenau SET ";
						foreach (array("Jour","Debut","Duree","Salle","Intervenant","Pris","Eleve","Info","Commentaire") as $etiquette) {
							$ligne[$etiquette]=$_POST[$etiquette][$nlig];
							$reqSQL .= $etiquette."='".$_POST[$etiquette][$nlig]."', ";
						}
						$reqSQL = substr($reqSQL,0,-2)." WHERE id_crenau='$id_crenau'";
						//print_r($ligne); echo "<BR>";
						//echo "$reqSQL<BR>";
						$accesDB->ExecRequete($reqSQL);
						echo ".";
					}
					echo "<BR>Succes !<BR>";
					//echo '<meta http-equiv="Refresh" content="0; url='."GererCrenaux.php".'">';
				}
				break;
			case "Supprimer":
				if (isset($_POST['modifier']) && isset($_POST['supprimer'])) {
					$modifier = $_POST['modifier'];
					$supprimer = $_POST['supprimer'];
					echo "On supprime ";
					foreach ($modifier as $nlig=>$id_crenau) {
						if (isset($supprimer[$nlig]) && isset($modifier[$nlig])) {
							// On supprime via un DELETE !
							//echo "Suppression $nlig, $id_crenau ; ";
							$reqSQL = "DELETE FROM ".PrefixeDB."Crenau WHERE id_crenau='$id_crenau'";
							//echo "$reqSQL<BR>";
							$accesDB->ExecRequete($reqSQL);
							echo ".";
						}
					}
					echo "<BR>Succes !<BR>";
					//echo '<meta http-equiv="Refresh" content="0; url='."GererCrenaux.php".'">';
				}
				break;
			case "Ajouter":
			if (isset($_POST['ajouter'])) {
				$ajouter = $_POST['ajouter'];
				foreach ($ajouter as $nlig=>$dummy) {
					echo "Ajout : $nlig";
					$reqSQL = "";
					//$res = $accesDB->ExecRequete($reqSQL);
					//echo '<meta http-equiv="Refresh" content="0; url='."GererIntervenants.php".'">';
				}
			}
				break;
			default:
				break;
		}
	} else {
		$reqSQL = "SELECT * FROM ".PrefixeDB."Crenau ORDER BY Concat(Jour,Debut) ASC";
		$res = $accesDB->ExecRequete($reqSQL);
		$nlig = 0;
		$t = new Template(tplPath());
		$t->set_filenames(array('liste'=>'listeCrenauxAdmin.tpl'));
		while($ligne=$accesDB->LigneSuivante($res)) {
			//print_r($ligne); echo "<BR>";
			$nlig++;
			$ligne['nlig'] = $nlig;
			if ($nlig%2) $ligne['eo']="odd"; else $ligne['eo']="even";
			$ligne['Pris'] = popupPris($ligne['Pris']);
			$ligne['Inter'] = popupIntervenant($tInter,$ligne['Intervenant']);
			$ligne['NE'] = popupPersonne($tPers, $ligne['Eleve']);
			$t->assign_block_vars('ligne',$ligne);
		}
			for ($nlig=0; $nlig<4; $nlig++) {
				$ligne['nlig'] = $nlig;
				if (($nlig+1)%2) $ligne['eo']="odd"; else $ligne['eo']="even";
				$t->assign_block_vars('ajout',$ligne);
			}
			$t->pparse('liste');
	}
}

function main() {
	//echo "<HR>\n";
	actionListeCrenaux();
	
}

entete("Gestion des cr&eacute;neaux");

menu($session,$accesDB);

main();


piedDePage();

restoreRoot();
?>