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

$ok = $session->controleAccesCode(racineSite."admin/lireCSVSemaines.php",array(Admin),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

function fabriquerDate($d, $annee = AnneeDefaut) {
	$dt = date_create_from_format("d/m",$d);
	//print_r($dt); echo ", $d<BR>";
	$mois = date_format($dt,"m");
	if ($mois<8) {
		$annee = $annee+1;
	}
	$dt1 = date_create_from_format("d/m/Y",$d."/".$annee);
	return $dt1;
}

function fabriquerHeureDuree($plage) {
	$tab = explode("-",$plage);
	$debut = $tab[0].":00:00";
	$fin = $tab[1].":00:00";
	$retour = array($debut, $fin);
	return $retour;
}

function choixFichier() {
	global $session;
	global $accesDB;
	global $fichierParDefaut;
	
	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixFichierCSV.tpl'));
	$t->assign_vars(array('classeDefaut'=>"PC", 'anneeDefaut'=>cetteAnnee(), 'fichierDefaut'=>'PC-2015-semaines.csv', 'extraFichier'=>"-semaines", 'quoi'=>"Semaines"));
	$t->pparse('choix');
}

function choixInfos() {
	global $session;
	global $accesDB;
	global $fichierParDefaut;
	

	$classe = $_POST['classe'];
	$annee = $_POST['annee'];
	$annee1 = $annee."-01-01";
	$nomFichier = $_POST['nomFichier'];
	$cheminFichier = uploadPath().$nomFichier;
	$code = codeAffichageCSV($cheminFichier);
	$req = "SELECT count(*) as nb FROM ".PrefixeDB."Colle JOIN ".PrefixeDB."IdColloscope ON id=idColloscope WHERE annee='$annee1'";
	$res = $accesDB->ExecRequete($req);
	$nb = ($accesDB->ObjetSuivant($res))->nb;
	if ($nb>0) {
		$msg="<p class='attention'>Il y a déjà des colles associées à ces semaines : il faudra les importer à nouveau...</p>";
	} else {
		$msg = "";
	}
	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixCSVSemaines.tpl'));
	$t->assign_vars(array('classe'=>$classe, 'annee'=>$annee, 'nomFichier'=>$nomFichier, 'csv'=>$code, 'message'=>$msg));
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
		$annee1 = $annee."-01-01";
		$nomFichier = $_POST['nomFichier'];
		$cheminFichier = uploadPath().$nomFichier;
		$lignes = chargerCSV($cheminFichier, TRUE);
		$idColl = 0;
		echo "Fichier : '$nomFichier' <BR>";
		$req = "DELETE FROM ".PrefixeDB."Semaine WHERE annee='$annee1'";
		$res = $accesDB->ExecRequete($req);
		// Suppression, si nécessaire des colles
		$req = "SELECT count(*) as nb FROM ".PrefixeDB."Colle JOIN ".PrefixeDB."IdColloscope ON id=idColloscope WHERE annee='$annee1'";
		$res = $accesDB->ExecRequete($req);
		$nb = ($accesDB->ObjetSuivant($res))->nb;
		if ($nb>0) {
			$req = "DELETE FROM ".PrefixeDB."Note WHERE idColloscope IN (SELECT id FROM ".PrefixeDB."IdColloscope  WHERE annee='$annee1')";
			$accesDB->ExecRequete($req);
			$req = "DELETE FROM ".PrefixeDB."Colle WHERE idColloscope IN (SELECT id FROM ".PrefixeDB."IdColloscope  WHERE annee='$annee1')";
			$accesDB->ExecRequete($req);
			$req = "DELETE FROM ".PrefixeDB."Crenau WHERE idColloscope IN (SELECT id FROM ".PrefixeDB."IdColloscope  WHERE annee='$annee1')";
			$accesDB->ExecRequete($req);
		}
		// Test des semaines
		$col1 = $_POST['colonne1']-1;
		$nNum = $_POST['numero']-1;
		$nDebut = $_POST['debut']-1;
		$nFin = $_POST['fin']-1;
		$nCol = count($lignes[$nNum]);
		echo "$nCol colonnes<BR>";
		for ($col=$col1; $col<$nCol; $col++) {
			$deb = fabriquerDate($lignes[$nDebut][$col]);
			$deb1 = normaliseDateTimeVersJour($deb,Lundi)->format('Y-m-d');
			$fin1 = normaliseDateTimeVersJour(fabriquerDate($lignes[$nFin][$col]),Vendredi)->format("Y-m-d");
			$sem = $lignes[$nNum][$col]." de ".$deb1." à ".$fin1;
			$nSem = $lignes[$nNum][$col];
			$req = "INSERT INTO ".PrefixeDB."Semaine (Nom, Annee, Debut, Fin, Colonne) VALUES ('$nSem', '$annee1', '$deb1', '$fin1', $col)";
			$res = $accesDB->ExecRequete($req);
			// Debug
			//echo "Semaine : $sem, $req <BR>";
		}
	}
}

function main() {
	global $session;
	global $accesDB;
	
	echo "<HR>\n";

	if (isset($_GET['action']) and isset($_POST['valid']))
		$action = $_GET['action'];
	else
		$action = "";
	$util = $session->lUtilisateur();
	$nature = $util->Nature;
	$actionAFaire = "action".ucfirst($action).ucfirst($nature);
	
	echo "Action, nature : '$action', $nature<BR>";
	echo "<BR>";
	
	switch ($action) {
		case "":
			echo "Action par défaut<BR>";
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

verifierQualiteMotDePasse();

main();

piedDePage();

restoreRoot();
?>