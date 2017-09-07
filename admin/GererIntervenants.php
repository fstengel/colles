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

$ok = $session->controleAccesCode(racineSite."admin/GererIntervenants.php",array(Admin),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

function tableauMatieres() {
	global $accesDB;
	
	$mat = array();
	$reqSQL = "SELECT * FROM ".PrefixeDB."Matiere as m ORDER BY m.Nom ASC";
	$res = $accesDB->ExecRequete($reqSQL);
	while ($ligne=$accesDB->LigneSuivante($res)) {
		$mat[] = $ligne;
	}
	return $mat;
}

function tableauProfs() {
	global $accesDB;
	
	$prof = array();
	$nat = Prof;
	$reqSQL = "SELECT * FROM ".PrefixeDB."Personne WHERE Nature&$nat ORDER BY concat(Nom,Prenom) ASC";
	$res = $accesDB->ExecRequete($reqSQL);
	while ($ligne=$accesDB->LigneSuivante($res)) {
		$prof[] = $ligne;
	}
	return $prof;	
}

function popupMatieres($tm, $i=-1) {
	$p = "";
	foreach ($tm as $lm) {
		$im = $lm['id_matiere'];
		$nm = $lm['Nom'];
		$p .= '<option value="'.$im.'"';
		if ($i==$im) $p .= " selected ";
		$p .= '>'.$nm.'</option>';
	}
	return $p;
}

function popupProfs($tp,$i=-1) {
	$p = "";
	foreach ($tp as $lp) {
		$ip = $lp['id_personne'];
		$np = $lp['Nom']." ".$lp['Prenom'];
		$p .= '<option value="'.$ip.'"';
		if ($i==$ip) $p .= " selected ";
		$p .= '>'.$np.'</option>';
	}
	return $p;
}



function actionListeIntervenants() {
	global $accesDB;
	global $session;
	
	$tMat = tableauMatieres();
	$tProf = tableauProfs();
	
	if (isset($_POST['valid'])) {
		//print_r($_POST); echo "<BR>";
		switch ($_POST['valid']) {
			case "Modifier":
				if (isset($_POST['modifier'])) {
					$modifier = $_POST['modifier'];
					foreach ($modifier as $nlig=>$id_intervenant) {
						// On modifie via un UPDATE !
						echo "Modification $nlig, $id_intervenant<BR>";
						// Attention PAS DE VALIDATION !!!!
						$np = $_POST['Personne'][$nlig];
						$nm = $_POST['Matiere'][$nlig];
						$reqSQL = "UPDATE ".PrefixeDB."Intervenant SET Personne='$np', Matiere='$nm' WHERE id_intervenant = '$id_intervenant'";
						//echo "$reqSQL<BR>";
						$accesDB->ExecRequete($reqSQL);
						echo '<meta http-equiv="Refresh" content="0; url='."GererIntervenants.php".'">';
					}
				}
				break;
			case "Supprimer":
				if (isset($_POST['modifier']) && isset($_POST['supprimer'])) {
					$modifier = $_POST['modifier'];
					$supprimer = $_POST['supprimer'];
					foreach ($modifier as $nlig=>$id_intervenant) {
						if (isset($supprimer[$nlig])) {
							// On supprime via un DELETE !
							echo "Suppression $nlig, $id_personne<BR>";
							$reqSQL = "DELETE FROM ".PrefixeDB."Intervenant WHERE id_intervenant='$id_intervenant'";
							//echo "$reqSQL<BR>";
							$accesDB->ExecRequete($reqSQL);
							echo '<meta http-equiv="Refresh" content="0; url='."GererIntervenants.php".'">';
						}
					}
				}
				break;
			case "Ajouter":
			if (isset($_POST['ajouter'])) {
				$ajouter = $_POST['ajouter'];
				foreach ($ajouter as $nlig=>$dummy) {
					echo "Ajout : $nlig";
					$np = $_POST['Personne'][$nlig];
					$nm = $_POST['Matiere'][$nlig];
					echo "Ajout : $np, $nm";
					$reqSQL = "INSERT INTO ".PrefixeDB."Intervenant (Personne, Matiere) VALUES ('$np', '$nm')";
					$res = $accesDB->ExecRequete($reqSQL);
					echo '<meta http-equiv="Refresh" content="0; url='."GererIntervenants.php".'">';
				}
			}
				break;
			default:
				break;
		}
	} else {
		$reqSQL = "SELECT * FROM ".PrefixeDB."Intervenant JOIN ".PrefixeDB."Personne ON Personne=id_personne ORDER BY CONCAT(Nom, Prenom) ASC";
		$res = $accesDB->ExecRequete($reqSQL);
		$nlig = 0;
		$t = new Template(tplPath());
		$t->set_filenames(array('liste'=>'listeIntervenants.tpl'));
		while($ligne=$accesDB->LigneSuivante($res)) {
			$nlig++;
			$ligne['nlig'] = $nlig;
			$ligne['popupPersonne'] = popupProfs($tProf,$ligne['Personne']);
			$ligne['popupMatiere'] = popupMatieres($tMat,$ligne['Matiere']);
				if ($nlig%2) $ligne['eo']="odd"; else $ligne['eo']="even";
				$t->assign_block_vars('ligne',$ligne);
			}
			for ($nlig=0; $nlig<4; $nlig++) {
				$ligne['nlig'] = $nlig;
				if (($nlig+1)%2) $ligne['eo']="odd"; else $ligne['eo']="even";
				$ligne['popupPersonne'] = popupProfs($tProf);
				$ligne['popupMatiere'] = popupMatieres($tMat);
				$t->assign_block_vars('ajout',$ligne);
			}
			$t->pparse('liste');
	}
}

function main() {
	//echo "<HR>\n";
	actionListeIntervenants();
	
}

entete("Gestion des intervenants");

menu($session,$accesDB);

main();


piedDePage();

restoreRoot();
?>