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

$ok = $session->controleAccesCode(racineSite."gererEleve.php?action=lister&quand=cette",array(Eleve),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

require_once(libPath()."LibSemaines.php");

/**
 * Détermine l'id du (seul) colloscope auquel j'ai accès
 *
 * @return int l'id de mon colloscope
 */
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

/**
 * Produit le code HTML des colles de cette semaine (s'il y en a)
 *
 * Utilise le template listeCollesEleve.tpl
 *
 * @return string le code HTML.
 */
function HTMLMesCollesCetteSemaine() {
	global $session;
	global $accesDB;
	
	$moi = $session->utilisateur;
	$idMoi = $moi->id_personne;
	$monIdColl = monIdColloscope();
	
	$sem = cetteSemaine();
	
	if (!$sem) {
		return "Il n'y a pas de colles pr&eacute;vues cette semaine<BR>";
	}
	$idSem = $sem->id_semaine;
		
	$req = "SELECT Personne, P.Nom, Prenom, Civilite, Intervenant, id_matiere, M.Nom as Matiere, id_Crenau, C.idColloscope, Jour, Lieu, Debut, Fin FROM ".PrefixeDB."Personne as P join ".PrefixeDB."Intervenant on id_personne=Personne
		join ".PrefixeDB."Crenau on Intervenant=id_intervenant join ".PrefixeDB."Matiere as M on id_Matiere=Matiere 
		join ".PrefixeDB."Colle as C on C.Crenau=id_Crenau join ".PrefixeDB."Groupement as G on C.Groupe=G.Groupe and C.idColloscope=G.idColloscope
		where C.idColloscope=$monIdColl and Semaine=$idSem and Eleve=$idMoi order by Jour, Debut;";
	$res = $accesDB->ExecRequete($req);
	$mesColles = $accesDB->ToutesLesLignes($res);
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
	return $t->HTML_for_handle('liste');	
}

/**
 * @deprecated Pour debug !
 */
function mesCollesCetteSemaine() {
	$code = HTMLMesCollesCetteSemaine();
	echo $code;
}

/**
 * @deprecated
* DEBUG ?
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
/**
 * Produit le code HTML de la liste de semaines passées en argument.
 *`
 * Utilise le template listeNotesEleve.tpl
 *
 * @param array(array()) liste des semaines tirées de la table Semaines.
 *
 * @return string le code HTML produit.
 */
function HTMLMesNotesDesSemaines($lesSemaines) {
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

	$lesIdSemaines = listeIDSemaines($lesSemaines);
	$lesSemainesParId = array();
	foreach ($lesSemaines as $i=>$semaine) {
		$id = $semaine['id_semaine'];
		$lesSemainesParId[$id] = $semaine;
	}

	$req = "SELECT C.Semaine, Matiere, Valeur from Colles_Personne as P Join Colles_Groupement as G  on id_personne=Eleve 
		join Colles_Note as N on N.Groupement=G.id_groupement Join Colles_Colle as C on N.Colle=C.id_colle join Colles_Crenaucomplet as Cr on Cr.id_crenau=C.Crenau 
		where Semaine in $lesIdSemaines and G.idColloscope=$monIdColl and P.id_personne=$idMoi";
	$res = $accesDB->ExecRequete($req);
	$mesNotes = $accesDB->ToutesLesLignes($res);
	
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
	$code = $t->HTML_for_handle('liste');
	return $code;
}

/**
 * Produit le code HTML du formulaire permettant d'afficher et de choisir les notes de semaines.
 * @param int $debut=0 le no ou l'id de la colle de début. -1 si dernière accessible.
 * @param int $fin=-1 le no ou l'id de la colle de fin. -1 si dernière accessible.
 * @param boolean $idPop=False si vrai les paramètre $debut et $fin sont des id de semaines.
 *
 * @return string le code HTML produit
 */
function HTMLMesNotesChoixSemaine($debut=0, $fin=-1, $idPop=False) {
	global $session;
	global $accesDB;
	
	$lesSemaines = semainesJusquAAujourdhui();
	if (count($lesSemaines)==0) {
		echo "Il n'y a pas de semaines de colles. Probl&egrave;me ?<BR>";
		return;
	}

	$code = "";
	
	if ($idPop) {
		$nDebut=0;
		$nFin=count($lesSemaines)-1;
		foreach ($lesSemaines as $i=>$semaine) {
			$id = $semaine['id_semaine'];
			// Debug
			//echo "$i, $id, $debut ($nDebut), $fin($nFin)<BR>";
			if ($id==$debut) $nDebut=$i;
			if ($id==$fin) $nFin=$i;
		}
		// Debug
		//echo "$i, $id, $debut ($nDebut), $fin($nFin)<BR>";
		$debut=$nDebut;
		$fin=$nFin;
		// Debug
		//echo "D, F : $debut, $fin<BR>";
	} else {
		$n = count($lesSemaines);
		if ($fin<0) $fin=$n+$fin;
		if ($fin<0) $fin=0;
		if ($fin>=$n) $fin=$n-1;			
	}
	if ($debut>$fin) $debut=$fin;
	$long = $fin-$debut+1;
	$semainesAffichees = array_slice($lesSemaines, $debut, $long);
	$code = HTMLMesNotesDesSemaines($semainesAffichees);
	
	$popDebut = popupSemaines($lesSemaines, $debut, "popDebut");
	$popFin = popupSemaines($lesSemaines, $fin, "popFin");

	$t = new Template(tplPath());
	$t->set_filenames(array('liste'=>'formSemainesEleve.tpl'));
	$t->assign_vars( array('action'=>'filtrer', 'quand'=>'?', 'debut'=>$popDebut, 'fin'=>$popFin, 'listeColles'=>$code) );
	
	$html = $t->HTML_for_handle('liste');
	return $html;
}

/**
 * @deprecated
 * Code mort... gardé pour histoire. À commenter !
 */
/*
function mesNotesChoixSemaine($debut=0, $fin=-1, $idPop=False) {
	$code = HTMLMesNotesChoixSemaine($debut, $fin, $idPop);
	echo $code;
}
*/

/**
 * @deprecated
 * @see mesNotesChoixSemaine, HTMLMesNotesChoixSemaine
 * va disparaître au profit de mesNotesChoixSemaine, HTMLMesNotesChoixSemaine
 */
/*
function toutesMesNotes() {
	global $session;
	global $accesDB;
	
	//$lesSemaines = semainesJusquAAujourdhui();
	//$code = HTMLMesNotesDesSemaines($lesSemaines);
	$code = HTMLMesNotesChoixSemaine();
	echo $code;
}
*/
/**
 * @deprecated
 * Code mort. À commenter.
 */
/*
function toutesMesNotesOLD() {
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
*/

/**
 * Fabrique le coeur de la page pour un élève.
 *
 * À savoir : les colles de cette semaine puis un formulaire pour lister les notes des semaines passées.
 * 
 * @param int $debut=0 le no ou l'id de la colle de début. -1 si dernière accessible.
 * @param int $fin=-1 le no ou l'id de la colle de fin. -1 si dernière accessible.
 * @param boolean $idColl=False si vrai les paramètre $debut et $fin sont des id de semaines.
 *
 * @return void
 */
function page($debut=0, $fin=-1, $idColl=False) {
	global $session;
	global $accesDB;
	
	$t = new Template(tplPath());
	$t-> set_filenames(array('page'=>'pageEleve.tpl'));
	
	$cette = HTMLMesCollesCetteSemaine();
	$notes = HTMLMesNotesChoixSemaine($debut, $fin, $idColl);
	
	$t->assign_vars( array('cette'=>$cette, 'notes'=>$notes) );

	$t->pparse('page');
}

/**
 * Liste les notes
 *
 * @param string $quand indique quoi aficher. "", "cettte" : cette semaine, "toutes" pour les afficher toutes.
 *
 * @return void
 */
function listerNotes($quand) {
	switch ($quand) {
		case "":
		case"cette":
		page(-1, -1);
		break;
		case "toutes":
		page();
		break;
		default:
		echo "Rien à faire pour '$quand'(pour l'instant)<BR>";
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
	// Debug
	//print_r($_POST); echo "<BR>";
	$debut = $_POST['popDebut'];
	$fin = $_POST['popFin'];

	page($debut, $fin, True);
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
	//mesCollesCetteSemaine();
	//toutesMesNotes();
	
	$action = ""; $quand="";
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
		$quand = $_GET['quand'];
	}
	
	switch ($action) {
		case "":
		case "lister":
		listerNotes($quand);
		break;
		
		case "filtrer":
		filtrerNotes($quand);
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