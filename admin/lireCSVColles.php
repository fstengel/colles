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

$ok = $session->controleAccesCode(racineSite."admin/lireCSVColles.php",array(Admin),PageDeBase);
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
	
	// Debug
	//echo "Fichier par d&eacute;faut : '$fichierParDefaut' <BR>";

	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixFichierCSV.tpl'));
	$t->assign_vars(array('classeDefaut'=>"PC", 'anneeDefaut'=>cetteAnnee(), 'fichierDefaut'=>'PC-2015.csv', 'extraFichier'=>"", 'quoi'=>"Colles"));
	$t->pparse('choix');
}

function choixInfos() {
	global $session;
	global $accesDB;
	global $fichierParDefaut;
	
	$classe = $_POST['classe'];
	$annee = $_POST['annee'];
	$nomFichier = $_POST['nomFichier'];
	// Debug
	//$nomFichier = $fichierParDefaut;
	$cheminFichier = uploadPath().$nomFichier;
	$code = codeAffichageCSV($cheminFichier);
	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixCSVColles.tpl'));
	$t->assign_vars(array('classe'=>$classe, 'annee'=>$annee, 'nomFichier'=>$nomFichier, 'csv'=>$code));
	$t->pparse('choix');
	
}

function verifierInfos() {
	global $accesDB;
	global $fichierParDefaut;
	
	// Reste ?
	//$reqSQL = "select * from Colles_Matiere";
	//$resultat = $accesDB->ExecRequete($reqSQL);
	
	// Je veux enlever les accents...
	//$transliterateur = Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC;');
	// Uilisation
	//$propre = $transliterateur->transliterate($sale);
	// Plus utilisé...
	
	//echo "Le post : <BR>";
	//print_r($_POST);
	//echo "<BR>";
	if (isset($_POST['valid'])) {
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
		// Debug
		echo "Fichier : '$nomFichier' <BR>";
		// On nettoye
		$req = "DELETE FROM ".PrefixeDB."Note WHERE idColloscope=$idColl";
		$res = $accesDB->ExecRequete($req);
		$req = "DELETE FROM ".PrefixeDB."Colle WHERE idColloscope=$idColl";
		$res = $accesDB->ExecRequete($req);
		$req = "DELETE FROM ".PrefixeDB."Crenau WHERE idColloscope=$idColl";
		$res = $accesDB->ExecRequete($req);
		// On charge le ficher complet
		$cheminFichier = uploadPath().$nomFichier;
		$lignes = chargerCSV($cheminFichier, TRUE);
		// On crée les crénaux
		// Debug
		//echo "Les intervenants/Crenaux <BR>";
		$nLignes = count($lignes);
		$ligne1 = $_POST['ligne1']-1;
		$nNom = $_POST['colleur']-1;
		$nMatiere = $_POST['matiere']-1;
		for ($n=$ligne1; $n<$nLignes; $n++) {
			$ligne = $lignes[$n];
			if ($ligne[$nNom]) {
				$nPers = $ligne[$nNom];
				$nMat = $ligne[$nMatiere];
				$reqSQL = "SELECT id_intervenant FROM (".PrefixeDB."Intervenant Join ".PrefixeDB."Personne AS pers ON Personne=id_personne) JOIN ".PrefixeDB."Matiere AS mat ON Matiere=id_matiere
					 WHERE  pers.Nom='$nPers' AND mat.Nom='$nMat'";
				$resultat = $accesDB->ExecRequete($reqSQL);
				$enr = $accesDB->LigneSuivante($resultat);
				$intervenant = $enr['id_intervenant'];
				$nomJour = $ligne[$_POST['jour']-1];
				$jour = jourVersNum($nomJour);
				$plage = $ligne[$_POST['heure']-1];
				$tab = fabriquerHeureDuree($plage);
				$debut = $tab[0];
				$fin = $tab[1];
				$lieu = $ligne[$_POST['salle']-1];
				$req = "INSERT INTO ".PrefixeDB."Crenau (idColloscope, Jour, Debut, Fin, Lieu, Intervenant, Ligne) VALUES ($idColl, $jour, '$debut', '$fin', '$lieu', $intervenant, $n)";
				$res = $accesDB->ExecRequete($req);
			}
		}
		// Gestion des semaines
		// On commence par charger les semaines correpondant à l'année dans une table
		$req = "SELECT * FROM ".PrefixeDB."Semaine WHERE Annee='$annee1'";
		// Debug
		//echo "$req<BR>";
		$res = $accesDB->ExecRequete($req);
		$lesSemainesBrut = $accesDB->ToutesLesLignes($res);
		$nbSemainesBrut = count($lesSemainesBrut);
		//print_r($lesSemainesBrut); echo "<BR>";
		//
		$col1 = $_POST['colonne1']-1;
		$nNum = $_POST['numero']-1;
		$nDebut = $_POST['debut']-1;
		$nFin = $_POST['fin']-1;
		$nCol = count($lignes[$nNom]);
		// Debug
		//echo "$nCol colonnes<BR>";
		$semaines = array();
		for ($col=$col1; $col<$nCol; $col++) {
			$deb = fabriquerDate($lignes[$nDebut][$col]);
			$deb1 = date_format($deb, "Y-m-d");
			$fin1 = date_format(fabriquerDate($lignes[$nFin][$col]),"Y-m-d");
			$sem = $lignes[$nNum][$col]." de ".$deb1." à ".$fin1;
			$nSem = $lignes[$nNum][$col];
			$ok = false;
			for ($j=0; $j<$nbSemainesBrut; $j++) {
				$semBrute = $lesSemainesBrut[$j];
				if ($semBrute['Debut']<=$deb1 and $semBrute['Fin']>=$fin1) {
					$semaines[$col] = $semBrute['id_semaine'];
					$ok = true;
					break;
				}
			}
			if (!$ok) {
				echo "Il y a un problème avec $sem<BR>";
				return;
			}
		}
		// On reprend les infos trouvées pour les crenaux
		$req = "SELECT id_crenau, Ligne FROM ".PrefixeDB."Crenau";
		// Debug
		//echo "$req <BR>";
		$res = $accesDB->ExecRequete($req);
		$crenaux = array();
		while ($ligne=$accesDB->ObjetSuivant($res)) {
			$crenaux[$ligne->Ligne] = $ligne->id_crenau;
		}
		//print_r($crenaux); echo "<BR>";
		//print_r($semaines); echo "<BR>";
		$nombreCrenaux = count($crenaux);
		$nombreSemaines = count($semaines);
		$nombreColles = 0;
		for ($cren=$ligne1; $cren<$nLignes; $cren++) {
			$ligne = $lignes[$cren];
			$nCol = count($ligne);
			$idCren = $crenaux[$cren];
			for ($sem=$col1; $sem<$nCol; $sem++) {
				$groupe = $ligne[$sem];
				if ($groupe) {
					$idSem = $semaines[$sem];
					$req = "INSERT INTO ".PrefixeDB."Colle (idColloscope, Semaine, Crenau, Groupe) VALUES ($idColl, $idSem, $idCren, '$groupe')";
					$res = $accesDB->ExecRequete($req);
					$nombreColles++;
					//echo "$req <BR>";
				}
			}
		}
		echo "On a crée/géré :<BR>";
		echo "<ul>";
		echo "<li>$nombreCrenaux crénaux</li>";
		echo "<li>$nombreSemaines semaines (sur un maximum de $nbSemainesBrut)</li>";
		echo "<li>$nombreColles colles</li>";
		echo "</ul>";
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
	/*
	echo "Action, nature : '$action', $nature<BR>";
	echo "<BR>";
	
	echo "Le post : <BR>";
	print_r($_POST);
	echo "<BR>";
	
	echo "<HR>\n";
	*/
		
	switch ($action) {
		case "":
			echo "Sélectionner un fichier<BR>";
			choixFichier();
			break;
		case "choix":
			echo "Choisir les lignes/colonnes des informations <BR>";
			choixInfos();
			break;
		case "verifier":
			echo "Finalisation des informations<BR>";
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