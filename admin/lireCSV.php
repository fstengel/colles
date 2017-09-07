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

$ok = $session->controleAccesCode("lireCSV.php",array(Admin),PageDeBase);
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

function gererCSV() {
}

$fichierParDefaut = uploadPath()."Colle.csv";
function chargerCSV($cheminCSV, $tout=False) {
	$fichier = fopen($cheminCSV, "r");
	$lignes = array();
	$nLignes = 0;
	if (!$fichier) {
		echo "Fichier $cheminCSV ($fichier) invalide ?<BR>";
		return;
	}
	
	while (!feof($fichier)) {
		$ligne = fgetcsv($fichier);
		if ($ligne) {
			$lignes[]=$ligne;
			$nLignes++;
		}
		if ((!$tout)&&($nLignes>MaxLignes)) break;
	}
	fclose($fichier);
	return $lignes;
}

function afficherCSV($fichier, $tout=False) {
	$code = codeAffichageCSV($fichier, $tout);
	echo $code;
}

function codeAffichageCSV($fichier, $tout=False) {
	$lignes = chargerCSV($fichier, $tout);
	$premiere = TRUE;
	$code = "";
	$code .= "<TABLE border='1'>";
	$code .= "<TBODY>";
	$nligne=1;
	foreach ($lignes as $ligne) {
		if ($ligne) {
			$n = count($ligne);
			if ($premiere) {
				$premiere = FALSE;
				$nCol = $n;
				if ($tout) $nColVis = $n; else  $nColVis = min($n, MaxColonnes);
				$code .= "<TR><TD></TD>";
				for ($col=1; $col<=$nColVis; $col++) {
					$code .= "<TD>$col</TD>";
				}
				$code .= "</TR>";
			} else {
				if ($n!=$nCol) {
					$code .= "?? : ";
					//var_dump($ligne);
					$code .= "<BR>";
				}
			}
			$code .= "<TR><TD>$nligne</TD>";
			for ($col = 0; $col <$nColVis; $col++) {
				$code .= "<TD>$ligne[$col]</TD>";
			}
			$code .= "</TR>\n";
			$nligne++;
			if ((! $tout)&&($nligne>MaxLignes)) break;
		}
	}
	$code .= "</TBODY>";
	$code .= "</TABLE>";
	return $code;
}


function choixFichier() {
	global $session;
	global $accesDB;
	global $fichierParDefaut;
	
	echo "Fichier par d&eacute;faut : '$fichierParDefaut' <BR>";

	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixFichierCSV.tpl'));
	$t->pparse('choix');
}

function choixInfos() {
	global $session;
	global $accesDB;
	global $fichierParDefaut;
	

	$classe = $_POST['classe'];
	$annee = $_POST['annee'];
	$nomFichier = $_POST['nomFichier'];
	// À changer !
	$nomFichier = $fichierParDefaut;
	$code = codeAffichageCSV($nomFichier);
	$t = new Template(tplPath());
	$t->set_filenames(array('choix'=>'formChoixCSV.tpl'));
	$t->assign_vars(array('classe'=>$classe, 'annee'=>$annee, 'nomFichier'=>$nomFichier, 'csv'=>$code));
	$t->pparse('choix');
}

function verifierInfos() {
	global $accesDB;
	global $fichierParDefaut;
	
	$reqSQL = "select * from Colles_Matiere";
	$resultat = $accesDB->ExecRequete($reqSQL);
	
	echo "Le post : <BR>";
	print_r($_POST);
	echo "<BR>";
	if (isset($_POST['valid'])) {
		echo "On continue !<BR>";
		$classe = $_POST['classe'];
		$annee = $_POST['annee'];
		$nomFichier = $_POST['nomFichier'];
		// À changer !
		$nomFichier = $fichierParDefaut;
		$idColl = 0;
		echo "Fichier : '$nomFichier' <BR>";
		$req = "DELETE FROM ".PrefixeDB."Crenau WHERE idColloscope=$idColl";
		$res = $accesDB->ExecRequete($req);
		// À revoir : une seule liste de semaines par année...
		// Page dédiée ?
		$req = "DELETE FROM ".PrefixeDB."Semaine WHERE idColloscope=$idColl";
		$res = $accesDB->ExecRequete($req);
		$req = "DELETE FROM ".PrefixeDB."Colle WHERE idColloscope=$idColl";
		$res = $accesDB->ExecRequete($req);
		echo "Les intervenants <BR>";
		// À changer !
		$lignes = chargerCSV($nomFichier, TRUE);
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
				//print_r($enr);
				$intervenant = $enr['id_intervenant'];
				$plage = $ligne[$_POST['heure']-1];
				$tab = fabriquerHeureDuree($plage);
				$debut = $tab[0];
				$fin = $tab[1];
				$lieu = $ligne[$_POST['salle']-1];
				echo "$req<BR>";
				$req = "INSERT INTO ".PrefixeDB."Crenau (idColloscope, Debut, Fin, Lieu, Intervenant, Ligne) VALUES ($idColl, '$debut', '$fin', '$lieu', $intervenant, $n)";
				$res = $accesDB->ExecRequete($req);
			}
		}
		// Test des semaines
		$col1 = $_POST['colonne1']-1;
		$nNum = $_POST['numero']-1;
		$nDebut = $_POST['debut']-1;
		$nFin = $_POST['fin']-1;
		$nCol = count($lignes[$nNom]);
		$annee1 = $annee."-01-01";
		// À modifier
		$annee1 = "2015-01-01";
		echo "$nCol colonnes<BR>";
		for ($col=$col1; $col<$nCol; $col++) {
			$deb = fabriquerDate($lignes[$nDebut][$col]);
			$deb1 = date_format($deb, "Y-m-d");
			$fin1 = date_format(fabriquerDate($lignes[$nFin][$col]),"Y-m-d");
			$sem = $lignes[$nNum][$col]." de ".$deb1." à ".$fin1;
			$nSem = $lignes[$nNum][$col];
			$req = "INSERT INTO ".PrefixeDB."Semaine (Nom, Annee, Debut, Fin, Colonne) VALUES ('$nSem', '$annee1', '$deb1', '$fin1', $col)";
			$res = $accesDB->ExecRequete($req);
			echo "Semaine : $sem, $req <BR>";
		}
		// On reprend les infos trouvées
		$req = "SELECT id_crenau, Ligne FROM ".PrefixeDB."Crenau";
		echo "$req <BR>";
		$res = $accesDB->ExecRequete($req);
		$crenaux = array();
		while ($ligne=$accesDB->ObjetSuivant($res)) {
			$crenaux[$ligne->Ligne] = $ligne->id_crenau;
		}
		print_r($crenaux); echo "<BR>";
		$req = "SELECT id_semaine, Colonne FROM ".PrefixeDB."Semaine";
		echo "$req <BR>";
		$res = $accesDB->ExecRequete($req);
		$semaines = array();
		while ($ligne=$accesDB->ObjetSuivant($res)) {
			$semaines[$ligne->Colonne] = $ligne->id_semaine;
		}
		print_r($semaines); echo "<BR>";
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
					echo "$req <BR>";
				}
			}
		}
	}
}

function main() {
	global $session;
	global $accesDB;
	
	//echo "<HR>\n";

	if (isset($_GET['action']))
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

main();

piedDePage();

restoreRoot();
?>