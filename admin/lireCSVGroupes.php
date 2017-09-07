<?php

require("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
require(libPath()."Session.php");

// Pour la lecture des CSV
require(libPath()."LibCSV.php");

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."admin/lireCSVGroupes.php",array(Admin),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

$fichierParDefaut = uploadPath()."Colle.csv";

function choixFichier() {
	global $session;
	global $accesDB;
	global $fichierParDefaut;
	
	// Debug
	//echo "Fichier par d&eacute;faut : '$fichierParDefaut' <BR>";

	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixFichierCSV.tpl'));
	$t->assign_vars(array('classeDefaut'=>"PC", 'anneeDefaut'=>cetteAnnee(), 'fichierDefaut'=>'PC-2015-groupes.csv', 'extraFichier'=>"-groupes", 'quoi'=>"Groupes"));
	$t->pparse('choix');
}

function choixInfos() {
	global $session;
	global $accesDB;
	global $fichierParDefaut;
	

	$classe = $_POST['classe'];
	$annee = $_POST['annee'];
	$nomFichier = $_POST['nomFichier'];
	$cheminFichier = uploadPath().$nomFichier;
	echo "Chemin : $cheminFichier<BR>";
	$code = codeAffichageCSV($cheminFichier);
	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixCSVGroupes.tpl'));
	$t->assign_vars(array('classe'=>$classe, 'annee'=>$annee, 'nomFichier'=>$nomFichier, 'csv'=>$code));
	$t->pparse('choix');
}

function verifierInfos() {
	global $accesDB;
	global $fichierParDefaut;
	
	//$reqSQL = "select * from Colles_Matiere";
	//$resultat = $accesDB->ExecRequete($reqSQL);
	
	echo "Le post : <BR>";
	print_r($_POST);
	echo "<BR>";
	if (isset($_POST['valid'])) {
		echo "On continue !<BR>";
		$classe = $_POST['classe'];
		$annee = $_POST['annee'];
		// L'année normalisée : au premier janvier...
		$annee1 = $annee."-01-01";
		$nomFichier = $_POST['nomFichier'];
		// Trouver/fabriquer l'id correspondant à l'année/division
		$req = "SELECT * FROM ".PrefixeDB."Division WHERE nom='$classe'";
		$res = $accesDB->ExecRequete($req);
		$oDivision = $accesDB->ObjetSuivant($res);
		if ($oDivision->id_parent) {
			$division = $oDivision->id_parent;
		} else {
			$division = $oDivision->id_division;
		}
		$req = "SELECT id FROM ".PrefixeDB."IdColloscope WHERE annee = '$annee1' and division = '$division'";
		$res = $accesDB->ExecRequete($req);
		$oID = $accesDB->ObjetSuivant($res);
		if (!$oID) {
			$req1 = "INSERT INTO ".PrefixeDB."IdColloscope (division, annee) VALUES ('$division', '$annee1')";
			$res1 = $accesDB->ExecRequete($req1);
			$req = "SELECT id FROM ".PrefixeDB."IdColloscope WHERE annee = '$annee1' and division = '$division'";
			$res = $accesDB->ExecRequete($req);
			$oID = $accesDB->ObjetSuivant($res);
			$idColl = $oID->id;
		} else {
			$idColl = $oID->id;
		}
		// Debug
		echo "ID Colloscope : $idColl ($classe, $annee1)<BR>";
		// A modifier
		//$idColl = 0;
		// On nettoye
		$req = "DELETE FROM ".PrefixeDB."Groupement WHERE idColloscope=$idColl";
		$accesDB->ExecRequete($req);
		$nNom = $_POST['nom']-1;
		$nPrenom = $_POST['prenom']-1;
		$nGroupe = $_POST['groupe']-1;
		$ligne1 = $_POST['ligne1']-1;
		// On charge le ficher complet
		$cheminFichier = uploadPath().$nomFichier;
		$groupes = chargerCSV($cheminFichier, TRUE);
		$nGroupes = count($groupes);
		$nAjout = 0;
		for ($i=$ligne1; $i<$nGroupes; $i++) {
			$gr = $groupes[$i];
			$nom = $gr[$nNom];
			$prenom = $gr[$nPrenom];
			$nomGroupe = $gr[$nGroupe];
			$req = "SELECT * FROM ".PrefixeDB."Personne WHERE Nom='$nom' AND Prenom='$prenom'";
			$res = $accesDB->ExecRequete($req);
			$tab = $accesDB->ToutesLesLignes($res);
			if (count($tab)>1) {
				echo "Problème : homonymie sur $Nom $Prenom !<BR>";
				return;
			}
			$idPers = ($tab[0])['id_personne'];
			$req = "INSERT INTO ".PrefixeDB."Groupement (idColloscope, Groupe, Eleve) VALUES ($idColl, '$nomGroupe', $idPers)";
			// Debug
			//echo "$req<BR>";
			$accesDB->ExecRequete($req);
			$nAjout++;
		}
		echo "On a ajouté $nAjout personnes regroupées.<BR>";
	}
}

function main() {
	global $session;
	global $accesDB;
	
	if (isset($_GET['action']) and isset($_POST['valid']))
		$action = $_GET['action'];
	else
		$action = "";
	$util = $session->lUtilisateur();
	$nature = $util->Nature;
	$actionAFaire = "action".ucfirst($action).ucfirst($nature);
	
	// Debug
	//echo "Action, nature : '$action', $nature<BR>";
	//echo "<HR>\n";
	
	switch ($action) {
		case "":
			echo "Sélectionner un fichier<BR>";
			choixFichier();
			break;
		case "choix":
			echo "Choix <BR>";
			choixInfos();
			break;
		case "verifier":
			echo "Vérifier infos<BR>";
			verifierInfos();
			break;
	}
	//afficherCSV();

}


entete("Gestion des Colles &mdash; PC Fabert ".Annee." (lire CSV)");

menu($session,$accesDB);

main();

piedDePage();

restoreRoot();
?>