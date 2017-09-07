<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.1 2017-09-05
 *
 */

require("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
//Changé de require vers require_once
require_once(libPath()."Session.php");

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."gererResponsable.php?action=lister&quand=cette",array(Responsable),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

require_once(libPath()."LibSemaines.php");
require_once(libPath()."LibNotes.php");

/**
 * Crée le popup (select) HTML pour choisir la classe/matière
 *
 * @param array(array) $lesResponsables la liste des reponsables/idColloscopes concernés
 * @param int|string $no le numéro ou id de la ligne sélectionnée
 * @param boolean $idPop vrai si $no est l'id
 * @param string $form="formSemaines" le nom du formulaire
 *
 * @return string le code HTML du <select....
 */
function popupClasseMatiere($lesResponsables, $no=-1, $idPop=False, $form="formSemaines") {
	global $session;
	global $accesDB;
	
	if (count($lesResponsables)==0) {
		return "(Rien !)";
	}
	
	$req = "SELECT * FROM ".PrefixeDB."Division WHERE id_parent is NULL ORDER BY nom";
	$res = $accesDB->execRequete($req);
	$lesClasses = $accesDB->ToutesLesLignes($res);
	$lesClassesParId = array();
	foreach ($lesClasses as $i=>$classe) {
		$id = $classe['id_division'];
		$lesClassesParId[$id] = $classe;
	}
	$req = "SELECT * FROM ".PrefixeDB."Matiere ORDER BY nom";
	$res = $accesDB->execRequete($req);
	$lesMatieres = $accesDB->ToutesLesLignes($res);
	$lesMatieresParId = array();
	foreach ($lesMatieres as $i=>$matiere) {
		$id = $matiere['id_matiere'];
		$lesMatieresParId[$id] = $matiere;
	}
	
	// Debug
	//echo "Classes : "; print_r($lesClassesParId);  echo "<BR>";
	//echo "Matieres : "; print_r($lesMatieresParId);  echo "<BR>";
	//echo "Resp : "; print_r($lesResponsables);  echo "<BR>";
	
	$pop = "<select name=\"popClasse\" onchange=\"document.$form.filtrer.click()\" >";
	if ($no==-1) {
		$pop .= "<option value=\"-1\" selected>Toutes</option>";
	} else {
		$pop .= "<option value=\"-1\">Toutes</option>";
	}
	foreach ($lesResponsables as $i=>$responsable) {
		$idMatiere = $responsable['Matiere'];
		$matiere = $lesMatieresParId[$idMatiere]['Nom'];
		$idDivision = $responsable['Division'];
		$division = $lesClassesParId[$idDivision]['nom'];
		$idColl = $responsable['id'];
		// Debug
		//echo "Info : $idDivision, $idMatiere, $idColl<BR>";
		$valeur = "$idColl,$idMatiere";
		$texte = "$division : $matiere";
		
		$selected = False;
		if ($idPop) {
			$selected = ($valeur===$no);
		} else {
			$selected = ($no==$i);
		}
		if ($selected) {
			$pop .= "<option value=\"$valeur\" selected>$texte</option>";
		} else {
			$pop .= "<option value=\"$valeur\">$texte</option>";
		}
	}
	$pop .= "</select>";
	return $pop;
}

/**
 * @deprecated
 *
 * Pour debug.
 */
function afficherNotes($semaines, $idColl, $matiere) {
	echo HTMLPourAfficherNotes($semaines, $idColl, $matiere);
}

/**
 * Produit le code pour afficher les notes d'un colloscope dans une matière. Organisé par nom d'élève.
 *
 * @param array(array) $semaines le tableau des semaines concernées
 * @param int $idColl l'id du colloscope
 * @param int $matiere l'id de la matiere
 *
 * @return le code HTML du tableau
 */
function HTMLPourAfficherNotes($semaines, $idColl, $matiere) {
	global $accesDB;
	
	if (count($semaines)==0) {
		return;
	}
	$idSemaines = listeIDSemaines($semaines);
	
	$req = "SELECT Nom FROM ".PrefixeDB."Matiere WHERE id_matiere=$matiere";
	$res = $accesDB->ExecRequete($req);
	$nomMatiere = ($accesDB->ObjetSuivant($res))->Nom;
	$req = "SELECT nom FROM ".PrefixeDB."Division JOIN ".PrefixeDB."IdColloscope ON id_division=division WHERE id=$idColl";
	$res = $accesDB->ExecRequete($req);
	$nomClasse = ($accesDB->ObjetSuivant($res))->nom;
	$req = "SELECT * FROM ".PrefixeDB."Classe WHERE idColloscope=$idColl ORDER BY Nom, Prenom";
	$res = $accesDB->ExecRequete($req);
	$classe = $accesDB->ToutesLesLignes($res);
	// Debug
	//print_r($classe); echo "<BR>";
	$groupementClasse = array();
	$notesClasse = array();
	foreach ($classe as $i=>$eleve) {
		$gr = $eleve['Groupement'];
		$groupementClasse[$gr] = $i;
		$notesClasse[$i] = array();
	}
	$req = "SELECT * FROM ".PrefixeDB."Note AS N JOIN ".PrefixeDB."Colle AS C ON N.Colle=C.id_colle 
		JOIN ".PrefixeDB."CrenauComplet AS Cr ON  C.Crenau=Cr.id_crenau where C.idColloscope=$idColl AND Matiere = $matiere AND Semaine IN $idSemaines";
	//echo "Req : $req<BR>";
	$res = $accesDB->ExecRequete($req);
	$notes = $accesDB->ToutesLesLignes($res);
	foreach ($notes as $i=>$note) {
		$idEleve = $groupementClasse[$note['Groupement']];
		$semaine = $note['Semaine'];
		$notesClasse[$idEleve][$semaine]=$note['Valeur'];
	}
	// Debug
	//print_r($semaines); echo "<BR>";
	$t = new Template(tplPath());
	$t-> set_filenames(array('liste'=>'listeNotesResponsable.tpl'));
	$t->assign_vars(array('classe'=>"$nomClasse - $nomMatiere"));
	foreach ($semaines as $i=>$semaine) {
		$t->assign_block_vars('semaine', array('Nom'=>$semaine['Nom']));
	}
	foreach ($classe as $i=>$eleve) {
		if ($i%2) $eleve['eo']='odd'; else $eleve['eo']='even';
		$t->assign_block_vars('ligne',$eleve);
		//print_r($notesClasse[$i]); echo "<BR>";
		foreach ($semaines as $j=>$semaine) {
			$id = $semaine['id_semaine'];
			if (array_key_exists($id, $notesClasse[$i])) {
				$note = parseValeur($notesClasse[$i][$id]);
			} else {
				$note = "";
			}
			//echo "$i, $id, $note<BR>";
			$t->assign_block_vars('ligne.note',array('Valeur'=>$note));
		}
	}
	return $t->HTML_for_handle('liste');
}

/**
 * Liste les notes pour tous les couples (classe,matiere) de la semaine $debut à la semaine $fin.
 *
 * @param int $debut le numero ou l'id de la semaine de début. 0=la première. -1=la dernière semaine
 * @param int $fin le numero ou l'id de la semaine de fin. -1=la dernière semaine
 * @param int $classe Soit le numero d'ordre dans le popup, soit -1 pour dire tous les couples, soit la chaîne "idColl,classe"
 * @param boolean $afficher=True Si vrai affiche le formulaire de choix puis les notes, sinon n'affiche que le formulaire
 * @param boolean $idPop=False Si vrai les tropis premiers paramètres sont des id.
 *
 * @return void
 */
function listerMesNotesChoixDesSemaines($debut=0, $fin=-1, $classe=-1, $afficher=True, $idPop=False) {
	global $session;
	global $accesDB;
	
	$lesSemaines = semainesJusquAAujourdhui();
	if (count($lesSemaines)==0) {
		echo "Il n'y a pas de semaines de colles. Probl&egrave;me ?<BR>";
		return;
	}	

	$moi = $session->utilisateur;
	$monId = $moi->id_personne;
	$annee = cetteAnnee()."-01-01";
	$req = "SELECT * FROM ".PrefixeDB."Responsable AS R JOIN ".PrefixeDB."IdColloscope AS IC ON R.Division=IC.division WHERE Personne=$monId AND IC.annee='$annee'";
	$res = $accesDB->ExecRequete($req);
	$lesResponsables = $accesDB->ToutesLesLignes($res);


	$code = "";
	if ($afficher) {
		if ($idPop) {
			$nDebut=0;
			$nFin=count($lesSemaines)-1;
			foreach ($lesSemaines as $i=>$semaine) {
				$id = $semaine['id_semaine'];
				if ($id==$debut) $nDebut=$i;
				if ($id==$fin) $nFin=$i;
			}
			$debut=$nDebut;
			$fin=$nFin;
		} else {
			$n = count($lesSemaines);
			if ($fin<0) $fin=$n+$fin;
			if ($fin<0) $fin=0;
			if ($fin>=$n) $fin=$n-1;			
		}
		if ($debut>$fin) $debut=$fin;
		$long = $fin-$debut+1;
		$semainesAffichees = array_slice($lesSemaines, $debut, $long);
		
		$popDebut = popupSemaines($lesSemaines, $debut, "popDebut");
		$popFin = popupSemaines($lesSemaines, $fin, "popFin");
		//À faire !
		$popClasse = popupClasseMatiere($lesResponsables, $classe, $idPop);
		//$popClasse = "(pour l'instant toutes)";
		
		$t = new Template(tplPath());
		$t->set_filenames(array('liste'=>'formSemainesResponsable.tpl'));
		$t->assign_vars(array('action'=>'filtrer', 'quand'=>'?', 'classe'=>$popClasse, 'debut'=>$popDebut, 'fin'=>$popFin) );
		
		$tous = $classe==-1;
		$idCollMatiere = explode(",",$classe);
		// Debug
		//echo "Tous : $tous -  $idCollMatiere[0] : $idCollMatiere[1] <BR>";
		foreach ($lesResponsables as $i=>$responsable) {
			$matiere = $responsable['Matiere'];
			$idColl = $responsable['id'];
			// Debug
			//echo "($idColl,$matiere) : ($idCollMatiere[0],$idCollMatiere[1])<BR>";
			//$ok = ($idCollMatiere[0]==$idColl and $idCollMatiere[1]==$matiere);
			//echo "OK : $ok<BR>";
			if ($tous or ($idCollMatiere[0]==$idColl and $idCollMatiere[1]==$matiere)) {
				$code = HTMLPourAfficherNotes($semainesAffichees, $idColl, $matiere);
				$t->assign_block_vars('notes', array('classe'=>$code) );
			}
		}
		$t->pparse('liste');
	} else {
		$popDebut = popupSemaines($lesSemaines, $debut, "popDebut");
		$popFin = popupSemaines($lesSemaines, $fin, "popFin");
		//À faire !
		$popClasse = popupClasseMatiere($lesResponsables);
		//$popClasse = "(pour l'instant toutes)";
		
		$t = new Template(tplPath());
		$t->set_filenames(array('liste'=>'formSemainesResponsable.tpl'));
		$t->assign_vars(array('action'=>'filtrer', 'quand'=>'?', 'classe'=>$popClasse, 'debut'=>$popDebut, 'fin'=>$popFin) );
		
	}
}

/**
 * @deprecated
 *
 * Pour debug
 */
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
	if ($sem) {
		echo "Cette Semaine : $sem->Nom (id : $sem->id_semaine)<BR>";
	} else {
		echo "Pas de semaine courante !<BR>";
	}

	$moi = $session->utilisateur;
	echo "Je suis : $moi->Nom $moi->Prenom (id : $moi->id_personne, nature : $moi->Nature)<BR>";
	
	$moi->matiere =1;
	echo "mat : $moi->matiere<BR>";
	
}

/**
 * Liste les notes
 *
 * @param string $quand indique quoi aficher. "", "cettte" : cette semaine, "une" (deprecated) , "toutes" pour les afficher toutes.
 *
 * @return void
 */
function listerNotes($quand) {
	switch ($quand) {
		case "":
		case"cette":
		$sem = cetteSemaine();
		if ($sem) {
			listerMesNotesChoixDesSemaines(-1,-1);
		}
		break;
		case "toutes":
		case "une":
		listerMesNotesChoixDesSemaines();
		break;
		default:
		echo "Rien à faire (pour l'instant)<BR>";
	}
}

/**
 * Filtre les notes
 *
 * @param string $quand N'a aucune influence. Est là pour des raisons d'uniformité.
 *
 * @return void
 */
function filtrerNotes($quand) {
	$debut = $_POST['popDebut'];
	$fin = $_POST['popFin'];
	$classe = $_POST['popClasse'];
	
	//Debug
	//echo "Filtrer  :"; print_r($_POST); echo "<BR>";
	listerMesNotesChoixDesSemaines($debut, $fin, $classe, True, True);
}

/**
 * Aiguille en fonction de $_GET
 *
 * @return void
 */
function main() {
	global $session;
	global $accesDB;
	
	// Debug
	//infos();
	
	$action = ""; $quand="";
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
		$quand = $_GET['quand'];
	}
	
	// Debug
	//echo "action : $action, quand : $quand<BR>";

	//test();
	switch ($action) {
		case "":
		case "lister":
		listerNotes($quand);
		break;
		case "filtrer":
		filtrerNotes($quand);
		break;
		default:
		echo "Rien a faire pour $action, $quand ?<BR>";
		break;
	}
	
}

entete("Gestion des Colles &mdash; PC Fabert ".Annee." (lire CSV)");

menu($session,$accesDB);

main();

piedDePage();

restoreRoot();
?>