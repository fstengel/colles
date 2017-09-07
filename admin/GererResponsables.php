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

$ok = $session->controleAccesCode(racineSite."admin/GererResponsables.php",array(Admin),PageDeBase);
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
	$nat = Responsable;
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
	if ($i=='') {
		$p = '<option value "-1" selected>NULL</option>';
	}
	foreach ($tp as $lp) {
		$ip = $lp['id_personne'];
		$np = $lp['Nom']." ".$lp['Prenom'];
		$p .= '<option value="'.$ip.'"';
		if ($i==$ip) $p .= " selected ";
		$p .= '>'.$np.'</option>';
	}
	return $p;
}



function actionListeResponsables() {
	global $accesDB;
	global $session;
	
	$tMat = tableauMatieres();
	$tProf = tableauProfs();
	
	if (isset($_POST['valid'])) {
		print_r($_POST); echo "<BR>";
		switch ($_POST['valid']) {
			case "Modifier":
				
				if (isset($_POST['modifier'])) {
					$modifier = $_POST['modifier'];
					print_r($modifier); echo "<BR>";
					foreach ($modifier as $nlig=>$id_matiere) {
						// On modifie via un UPDATE !
						//echo "Modification $nlig, $id_matiere<BR>";
						// Attention PAS DE VALIDATION !!!!
						$np = $_POST['Personne'][$nlig];
						$nr = $_POST['Responsable'][$nlig];
						$nd = $_POST['Division'][$nlig];
						$nm = $id_matiere;
						if ($nr=='') {
							//echo "Cas où il faut créer la ligne : ";
							$reqSQL = "INSERT INTO ".PrefixeDB."Responsable (Personne, Matiere, Division) VALUES ('$np', '$nm', '$nd')";
						} else {
							//echo "La ligne est à modifier : ";
							$reqSQL = "UPDATE ".PrefixeDB."Responsable SET Personne='$np', Matiere='$nm' WHERE id_responsable = '$nr'";
						}
						echo "$reqSQL<BR>";
						$accesDB->ExecRequete($reqSQL);
						echo '<meta http-equiv="Refresh" content="0; url='."GererResponsables.php".'">';
					}
				}
				break;
			/*
			case "Supprimer":
				// On ne feut rien faire : le code va mourir...
				break;
				if (isset($_POST['modifier']) && isset($_POST['supprimer'])) {
					
					$supprimer = $_POST['supprimer'];
					foreach ($modifier as $nlig=>$id_intervenant) {
						if (isset($supprimer[$nlig])) {
							// On supprime via un DELETE !
							echo "Suppression $nlig, $id_personne<BR>";
							$reqSQL = "DELETE FROM ".PrefixeDB."Responsable WHERE id_intervenant='$id_intervenant'";
							//echo "$reqSQL<BR>";
							$accesDB->ExecRequete($reqSQL);
							echo '<meta http-equiv="Refresh" content="0; url='."GererIntervenants.php".'">';
						}
					}
				}
				break;
			case "Ajouter":
			// On ne feut rien faire : le code va mourir...
			break;
			if (isset($_POST['ajouter'])) {
				$ajouter = $_POST['ajouter'];
				foreach ($ajouter as $nlig=>$dummy) {
					echo "Ajout : $nlig";
					$np = $_POST['Personne'][$nlig];
					$nm = $_POST['Matiere'][$nlig];
					echo "Ajout : $np, $nm";
					$reqSQL = "INSERT INTO ".PrefixeDB."Responsable (Personne, Matiere) VALUES ('$np', '$nm')";
					$res = $accesDB->ExecRequete($reqSQL);
					echo '<meta http-equiv="Refresh" content="0; url='."GererIntervenants.php".'">';
				}
			}
				break;
			*/
			default:
			echo "Requète non prévue !<BR>";
				break;
		}
	} else {
		$reqSQL = "SELECT D.nom as NomDivision, D.id_division, M.Nom as Nom, M.id_matiere, R.Personne 
			FROM ".PrefixeDB."Division AS D JOIN ".PrefixeDB."Matiere AS M LEFT JOIN ".PrefixeDB."Responsable AS R ON  R.Division=D.id_division and R.Matiere=M.id_matiere
			 WHERE D.id_parent IS NULL order by D.nom, M.Nom";
		$res = $accesDB->ExecRequete($reqSQL);
		$nlig = 0;
		$t = new Template(tplPath());
		$t->set_filenames(array('liste'=>'listeResponsables.tpl'));
		while($ligne=$accesDB->LigneSuivante($res)) {
			//print_r($ligne); echo "<BR>";
			$nlig++;
			$ligne['nlig'] = $nlig;
			$ligne['popupPersonne'] = popupProfs($tProf,$ligne['Personne']);
			$ligne['Matiere'] = $ligne['Nom'];
				if ($nlig%2) $ligne['eo']="odd"; else $ligne['eo']="even";
				$t->assign_block_vars('ligne',$ligne);
			}
			$t->pparse('liste');
	}
}

function main() {
	// echo "<HR>\n";
	actionListeResponsables();
	
}

entete("Gestion des responsables");

menu($session,$accesDB);

main();


piedDePage();

restoreRoot();
?>