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

// Va mourir !
function fabriquerListeType($t) {
	$tEl = "";
	$tProf = "";
	$tAdmin = "";
	switch ($t) {
		case 'eleve':
			$tEl = " selected";
			break;
		case 'prof':
			$tProf = " selected";
			break;
		case 'admin':
			$tAdmin = " selected";
			break;
	}
	$pop = '<option value="eleve"'.$tEl.'>&Eacute;l&egrave;ve</option>';
	$pop .= '<option value="prof"'.$tProf.'>Prof</option>';
	$pop .= '<option value="admin"'.$tAdmin.'>Admin</option>';
	return $pop;
}

function actionListePersonnes() {
	global $accesDB;
	global $session;
	
	if (isset($_POST['valid'])) {
		switch ($_POST['valid']) {
			case 'Modifier':
				if (isset($_POST['modifier'])) {
					$modifier = $_POST['modifier'];
					//print_r($_POST); echo "<BR>";
					//echo "<BR>";
					foreach ($modifier as $nlig=>$id_personne) {
						// On modifie via un UPDATE !
						echo "Modification $nlig, $id_personne<BR>";
						$_POST = nettoyerTexte($_POST);
						// Attention PAS DE VALIDATION !!!!
						$Nom = $_POST['Nom'][$nlig];
						$Prenom = $_POST['Prenom'][$nlig];
						$Mail = $_POST['Mail'][$nlig];
						$NomDUtilisateur = $_POST['NomDUtilisateur'][$nlig];
						$MotDePasse = $_POST['MotDePasse'][$nlig];
						$Type = $_POST['Type'][$nlig];
						$Nature = 0;
						if (isset($_POST['eleve'][$nlig])) { $Nature = $Nature | Eleve;}
						if (isset($_POST['prof'][$nlig])) { $Nature = $Nature | Prof;}
						if (isset($_POST['responsable'][$nlig])) { $Nature = $Nature | Responsable;}
						if (isset($_POST['admin'][$nlig])) { $Nature = $Nature | Admin;}
						$reqSQL = "UPDATE ".PrefixeDB."Personne SET Nom='$Nom', Prenom='$Prenom', Mail='$Mail', MotDePasse='$MotDePasse', Nature='$Nature', NomDUtilisateur='$NomDUtilisateur' WHERE id_personne='$id_personne'";
						//echo "$reqSQL<BR>";
						$accesDB->ExecRequete($reqSQL);
						echo '<meta http-equiv="Refresh" content="0; url='."GererPersonnes.php".'">';
					}
				}
				break;
			case 'Supprimer':
				if (isset($_POST['modifier']) && isset($_POST['supprimer'])) {
					$modifier = $_POST['modifier'];
					$supprimer = $_POST['supprimer'];
					foreach ($modifier as $nlig=>$id_personne) {
						if (isset($supprimer[$nlig])) {
							// On supprime via un DELETE !
							echo "Suppression $nlig, $id_personne<BR>";
							$reqSQL = "DELETE FROM ".PrefixeDB."Personne WHERE id_personne='$id_personne'";
							//echo "$reqSQL<BR>";
							$accesDB->ExecRequete($reqSQL);
							echo '<meta http-equiv="Refresh" content="0; url='."GererPersonnes.php".'">';
						}
					}
				}
				break;
			case 'Ajouter':
				if (isset($_POST['ajouter'])) {
					$ajouter = $_POST['ajouter'];
					print_r($ajouter); echo "<BR>";
					//print_r($_POST); echo "<BR>";
					foreach ($ajouter as $nlig=>$dummy) {
						// On valide l'ajout : a priori, rien a faire...
						// Puis on ajoute avec un INSERT
						echo "Ajout : $nlig, $dummy<BR>";
						$_POST = nettoyerTexte($_POST);
						// Attention PAS DE VALIDATION !!!!
						$Nom = $_POST['Nom'][$nlig];
						$Prenom = $_POST['Prenom'][$nlig];
						$Mail = $_POST['Mail'][$nlig];
						$NomDUtilisateur = $_POST['NomDUtilisateur'][$nlig];
						$MotDePasse = $_POST['MotDePasse'][$nlig];
						$Nature = 0;
						if (isset($_POST['eleve'][$nlig])) { $Nature = $Nature | Eleve;}
						if (isset($_POST['prof'][$nlig])) { $Nature = $Nature | Prof;}
						if (isset($_POST['responsable'][$nlig])) { $Nature = $Nature | Responsable;}
						if (isset($_POST['admin'][$nlig])) { $Nature = $Nature | Admin;}
						$reqSQL = "INSERT INTO ".PrefixeDB."Personne (Nom, Prenom, Mail, NomDUtilisateur, MotDePasse, Nature) Values ('$Nom', '$Prenom', '$Mail', '$NomDUtilisateur', '$MotDePasse', '$Nature')";
						//echo "$reqSQL<BR>";
						$accesDB->ExecRequete($reqSQL);
						echo '<meta http-equiv="Refresh" content="0; url='."GererPersonnes.php".'">';
					}
				}
				break;
			default:
				break;
		}
	} else {
		$reqSQL = "SELECT * FROM ".PrefixeDB."Personne";
		$res = $accesDB->ExecRequete($reqSQL);
		$nlig=0;
		$t = new Template(tplPath());
		$t->set_filenames(array('liste'=>"listePersonnes.tpl"));
		while($ligne=$accesDB->LigneSuivante($res)) {
			//print_r($ligne); echo "<BR>";
			$nlig++;
			$ligne['nlig'] = $nlig;
			//$ligne['popupType'] = fabriquerListeType($ligne['Type']);
			if ($nlig%2) $ligne['eo']="odd"; else $ligne['eo']="even";
			if ($ligne['Nature']&Eleve) {$ligne['eleveCheck']="checked";} else {$ligne['eleveCheck']="";}
			if ($ligne['Nature']&Prof) {$ligne['profCheck']="checked";} else {$ligne['profCheck']="";}
			if ($ligne['Nature']&Responsable) {$ligne['responsableCheck']="checked";} else {$ligne['responsableCheck']="";}
			if ($ligne['Nature']&Admin) {$ligne['adminCheck']="checked";} else {$ligne['adminCheck']="";}
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
	actionListePersonnes();
	
}

entete("Gestion des personnes");

menu($session,$accesDB);

main();


piedDePage();

restoreRoot();
?>