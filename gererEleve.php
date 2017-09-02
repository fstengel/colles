<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

require("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
require(libPath()."Session.php");

require_once(libPath()."LibNotes.php");

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."gererEleve.php?action=acceuil",array(Eleve),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

require_once(libPath()."LibSemaines.php");

function monIdColloscope() {
	global $session;
	global $accesDB;
	
	$moi = $session->utilisateur;
	$idMoi = $moi->id_personne;
	$annee=cetteAnnee()."-01-01";

	$req = "select distinct id from ".PrefixeDB."IdColloscope as IC join ".PrefixeDB."Groupement as G on id=idColloscope where annee='$annee' and Eleve=$idMoi";
	$res = $accesDB->ExecRequete($req);
	$mesIdColl = $accesDB->ToutesLesLignes($res);
	if (count($mesIdColl)>1) {
		echo "Y'a pb : je suis dans trop de colloscopes !<BR>";
		print_r($mesIdColl); echo "<BR>";
	}
	$monIdColl = $mesIdColl[0]['id'];
	return $monIdColl;
}

function infos() {
	global $session;
	global $accesDB;
	
	$action = ""; $quand="";
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
		$quand = $_GET['quand'];
	}

	echo "action = '$action' : '$quand'<BR>";

	$auj = aujourdhui();
	$an = cetteAnnee();
	echo "Aujourd'hui $auj ($an)<BR>";

	$sem = cetteSemaine();
	echo "Cette Semaine : $sem->Nom (id : $sem->id_semaine)<BR>";

	$moi = $session->utilisateur;
	echo "Je suis : $moi->Nom $moi->Prenom (id : $moi->id_personne, nature : $moi->Nature)<BR>";
	
	$idMoi = $moi->id_personne;

	$monIdColl = monIdColloscope();
	echo "Mon id Colloscope : $monIdColl<BR>";

	/*
	$req = "select * from ".PrefixeDB."Groupement as G where idColloscope=$monIdColl and Eleve=$idMoi";
	$res = $accesDB->ExecRequete($req);
	$mesGroupes = $accesDB->ToutesLesLignes($res);
	// Debug
	//print_r($mesGroupes); echo "<BR>";
	*/
	
	$sem = cetteSemaine();
	$idSem = $sem->id_semaine;
	echo "Cette semaine id : $idSem<BR>";
	
	/* @obsolete
	$req = "select * from ".PrefixeDB."Colle as C join ".PrefixeDB."Groupement as G on C.idColloscope=G.idColloscope and C.Groupe=G.Groupe where G.idColloscope=$monIdColl and Eleve=$idMoi and C.Semaine=$idSem";
	$res = $accesDB->ExecRequete($req);
	$mesColles = $accesDB->ToutesLesLignes($res);
	// Debug
	//print_r($mesColles); echo "<BR>";
	*/
	
	$req = "SELECT Personne, P.Nom, Prenom, Civilite, Intervenant, id_matiere, M.Nom as Matiere, id_Crenau, C.idColloscope, Jour, Lieu, Debut, Fin FROM ".PrefixeDB."Personne as P join ".PrefixeDB."Intervenant on id_personne=Personne
		join ".PrefixeDB."Crenau on Intervenant=id_intervenant join ".PrefixeDB."Matiere as M on id_Matiere=Matiere 
		join ".PrefixeDB."Colle as C on C.Crenau=id_Crenau join ".PrefixeDB."Groupement as G on C.Groupe=G.Groupe and C.idColloscope=G.idColloscope
		where C.idColloscope=$monIdColl and Semaine=$idSem and Eleve=$idMoi order by Jour, Debut;";
	$res = $accesDB->ExecRequete($req);
	$mesColles = $accesDB->ToutesLesLignes($res);
	// Debug
	//print_r($mesColles); echo "<BR>";
	$t = new Template(tplPath());
	$t->set_filenames(array('liste'=>'listeCollesEleve.tpl'));
	foreach ($mesColles as $i=>$colle) {
		if ($i%2) $colle['eo']="odd"; else $colle['eo']="even";
		$colle['Colleur'] = $colle['Civilite']." ".$colle['Nom'];
		$colle['nJour'] = ucfirst(numVersJour($colle['Jour']));
		$colle['Debut'] = substr($colle['Debut'],0,-3);
		$colle['Fin'] = substr($colle['Fin'],0,-3);
		$t->assign_block_vars('ligne',$colle);
	}
	$t->pparse('liste');
	
	
}

function toutesMesNotes() {
	global $session;
	global $accesDB;
	
	$moi = $session->utilisateur;
	$idMoi = $moi->id_personne;
	$monIdColl = monIdColloscope();	
	
	$req = "SELECT * From Colles_Matiere ORDER BY Nom";
	$res = $accesDB->ExecRequete($req);
	$lesMatieres = $accesDB->ToutesLesLignes($res);
	$lesMatieresParId = array();
	foreach ($lesMatieres as $i=>$matiere) {
		$id = $matiere['id_matiere'];
		$matiereParId[$id] = $matiere;
	}

	$lesSemaines = semainesJusquAAujourdhui();
	$lesIdSemaines = listeIDSemaines($lesSemaines);
	$lesSemainesParId = array();
	foreach ($lesSemaines as $i=>$semaine) {
		$id = $semaine['id_semaine'];
		$lesSemainesParId[$id] = $semaine;
	}

	$req = "SELECT C.Semaine, Matiere, Valeur from Colles_Personne as P Join Colles_Groupement as G  on id_personne=Eleve 
		join Colles_Note as N on N.Groupement=G.id_groupement Join Colles_Colle as C on N.Colle=C.id_colle join Colles_Crenaucomplet as Cr on Cr.id_crenau=C.Crenau 
		where Semaine in $lesIdSemaines and G.idColloscope=$monIdColl and P.id_personne=$idMoi";
	echo "$req<BR>";
	$res = $accesDB->ExecRequete($req);
	$mesNotes = $accesDB->ToutesLesLignes($res);
	//print_r($mesNotes); echo "<BR>";
	
	$tableauNotes = array();
	foreach ($lesMatieres as $i=>$matiere) {
		$id = $matiere['id_matiere'];
		$tableauNotes[$id] = array();
	}
	foreach ($mesNotes as $i=>$note) {
		$sem = $note['Semaine'];
		$mat = $note['Matiere'];
		$val = $note['Valeur'];
		$tableauNotes[$mat][$sem] = $val;
	}
	//print_r($tableauNotes); echo "<BR>";
	foreach ($lesMatieres as $i=>$matiere) {
		$nom = $matiere['Nom'];
		$mat = $matiere['id_matiere'];
		echo "$nom ($mat) : ";
		$notes = $tableauNotes[$mat];
		foreach ($lesSemaines as $semaine) {
			$sem = $semaine['id_semaine'];
			if (array_key_exists($sem, $notes)) {
				$note = parseValeur($notes[$sem]);
				echo "$note ($sem)  ";
			}
		}
		echo "<BR>";
	}
	$t = new Template(tplPath());
	$t-> set_filenames(array('liste'=>'listeNotesEleve.tpl'));
	foreach ($lesSemaines as $i=>$semaine) {
		$t->assign_block_vars('semaine', array('Nom'=>$semaine['Nom']));
	}
	foreach ($lesMatieres as $i=>$matiere) {
		$nom = $matiere['Nom'];
		$mat = $matiere['id_matiere'];
		if ($i%2) $matiere['eo']='odd'; else $matiere['eo']='even';
		$t->assign_block_vars('ligne',$matiere);
		$notes = $tableauNotes[$mat];
		foreach ($lesSemaines as $j=>$semaine) {
			$sem = $semaine['id_semaine'];
			if (array_key_exists($sem, $notes)) {
				$note = parseValeur($notes[$sem]);
			} else {
				$note = "";
			}
			//echo "$i, $id, $note<BR>";
			$t->assign_block_vars('ligne.note',array('Valeur'=>$note));
		}
	}
	$t->pparse('liste');
}

function main() {
	global $session;
	global $accesDB;
	
	// Debug
	infos();
	toutesMesNotes();
	
	$action = ""; $quand="";
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
		$quand = $_GET['quand'];
	}
	
	switch ($action) {
		case "":
		case "lister":
		
		break;
		
			
		default:
		echo "rien à dire ($action, $quand)<BR>";
	}
}

entete("Gestion des Colles &mdash; PC Fabert ".Annee." (lire CSV)");

menu($session,$accesDB);

/** 
* @todo  Améliorer le template
*/
if (($session->identifie) && ($session->motDePasseAChanger)) {
	//print_r($session);
	$t = new Template(tplPath());
	$t->set_filenames(array('mess'=>"message.tpl"));
	$mess = "Il FAUT changer le mot de passe !";
	$t->assign_vars(array('message'=>$mess));
	$t->pparse('mess');
}



main();

piedDePage();

restoreRoot();
?>