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

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."gererProf.php?action=lister&quand=cette",array(Prof),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

require_once(libPath()."LibSemaines.php");
require_once(libPath()."LibNotes.php");

function tabDivisionParIdColloscope() {
	global $session;
	global $accesDB;

	$req = "SELECT * FROM ".PrefixeDB."IdColloscope JOIN ".PrefixeDB."Division ON Division=id_division";
	$res = $accesDB->ExecRequete($req);
	$tab = $accesDB->ToutesLesLignes($res);
	$div = array();
	foreach ($tab as $ligne) {
		$id = $ligne['id'];
		$div[$id] = $ligne;
	}
	return $div;
}


function mesCrenaux() {
	global $session;
	global $accesDB;
	
	$idMoi = $session->utilisateur->id_personne;
	$req = "SELECT id_crenau, idColloscope, Debut, Fin, Lieu FROM ".PrefixeDB."Crenau JOIN ".PrefixeDB."Intervenant ON Intervenant=id_intervenant WHERE Personne=$idMoi";
	$res = $accesDB->ExecRequete($req);
	$cren = $accesDB->ToutesLesLignes($res);
	return $cren;
}

/**
 * @deprecated ?
 */
function listeSQLMesCrenaux() {
	$cren = mesCrenaux();
	$nbCren = count($cren);
	$liste = "( ";
	for ($i=0; $i<$nbCren; $i++) {
		$crenau = $cren[$i];
		$id = $crenau['id_crenau'];
		$liste .= $id;
		if ($i<$nbCren-1) {
			$liste .= ', ';
		}
	}
	$liste .= " )";
	return $liste;
}

function mesCollesUneSemaine($idSem) {
	global $session;
	global$accesDB;
	
	$idMoi = $session->utilisateur->id_personne;
	$req = "SELECT $idSem as id_semaine, id_crenau, Cren.idColloscope, Colle.id_colle as id_colle, Jour, Debut, Fin, Lieu, Groupe 
		FROM ".PrefixeDB."Crenau AS Cren JOIN ".PrefixeDB."Intervenant ON Intervenant=id_intervenant JOIN ".PrefixeDB."Colle as Colle ON id_crenau=Crenau WHERE Personne=$idMoi AND Semaine=$idSem 
		ORDER BY Jour ASC, Debut ASC";
	// Debug
	//echo $req;
	$res = $accesDB->ExecRequete($req);
	$colles = $accesDB->ToutesLesLignes($res);
	return $colles;
}

function mesCollesDesSemainesSeparees($tabSemaines) {
	$tabColles = array();
	foreach ($tabSemaines as $sem) {
		$tabColles[] = mesCollesUneSemaine($sem);
	}
	return $tabColles;
}

function mesCollesDesSemaines($tabSemaines) {
	global $session;
	global$accesDB;
	
	$idMoi = $session->utilisateur->id_personne;
	$listeSem = listeIDSemaines($tabSemaines);
	$req = "SELECT Semaine as id_semaine, id_crenau, Cren.idColloscope, Colle.id_colle, Jour, Debut, Fin, Lieu, Groupe 
		FROM ".PrefixeDB."Crenau AS Cren JOIN ".PrefixeDB."Intervenant ON Intervenant=id_intervenant JOIN ".PrefixeDB."Colle as Colle ON id_crenau=Crenau WHERE Personne=$idMoi AND Semaine IN $listeSem 
		ORDER BY Semaine ASC, Jour ASC, Debut ASC";
	// Debug
	//echo $req;
	$res = $accesDB->ExecRequete($req);
	$colles = $accesDB->ToutesLesLignes($res);
	return $colles;
}

/**
 * @deprecated
 * Code Mort
 */
function mesCollesDesSemainesOLD($tabSemaines) {
	$tabColles = array();
	foreach ($tabSemaines as $sem) {
		$colles = mesCollesUneSemaine($sem);
		$tabColles = array_merge($tabColles, $colles);
	}
	return $tabColles;
}

function elevesDuGroupe($groupe, $idCol) {
	global $session, $accesDB;

	$req = "SELECT Eleve, Nom, Prenom, id_groupement FROM ".PrefixeDB."Groupement JOIN ".PrefixeDB."Personne ON Eleve=id_personne WHERE Groupe='$groupe' AND idColloscope='$idCol'";
	// Debug
	//echo "$req<BR>";
	$res = $accesDB->ExecRequete($req);
	$tab = $accesDB->ToutesLesLignes($res);
	// Debug
	//print_r($tab); echo "<BR>";
	return $tab;
}

function elevesDuGroupeAvecNote($idColle) {
	global $session, $accesDB;
	
	// Debug
	//echo " id Colle : $idColle<BR>";
	$req = "SELECT Eleve, Nom, Prenom, id_groupement, Valeur, id_note 
		FROM ".PrefixeDB."Colle AS C JOIN ".PrefixeDB."Groupement AS G ON C.Groupe=G.Groupe AND C.idColloscope=G.idColloscope
			JOIN ".PrefixeDB."Personne AS P ON G.Eleve=P.id_personne
			LEFT JOIN ".PrefixeDB."Note AS N ON N.Groupement=G.id_groupement and N.Colle=C.id_colle WHERE C.id_colle=$idColle
			ORDER BY Nom, Prenom";
	// Debug
	//echo "$req<BR>";
	$res = $accesDB->ExecRequete($req);
	$tab = $accesDB->ToutesLesLignes($res);
	// Debug
	//print_r($tab); echo "<BR>";
	return $tab;
}

function listeDesElevesDuGroupe($groupe, $idCol) {
	global $session, $accesDB;

	$tab = elevesDuGroupe($groupe, $idCol);
	$n = count($tab);
	$liste = "";
	for ($i=0; $i<$n; $i++) {
		$nom = ($tab[$i])['Nom'];
		$prenom = ($tab[$i])['Prenom'];
		$liste .= $nom." ".$prenom;
		if ($i<$n-1) $liste .= ", ";
	}
	return $liste;
}

function HTMLCrenauxDesSemaines($lesSemaines) {
	global $session;
	global $accesDB;
	
	$tab = array();
	$semainesParId = array();
	foreach($lesSemaines as $sem) {
		$tab[] = $sem['id_semaine'];
		$semainesParId[$sem['id_semaine']] = $sem;
	}
	//$mesColles = mesCollesUneSemaine($sem->id_semaine);
	$mesColles = mesCollesDesSemaines($lesSemaines);
	// Debug
	//print_r($mesColles); echo "<BR>";

	$t = new Template(tplPath());
	$t-> set_filenames(array('liste'=>'formCollesProf.tpl'));
	$nbColles = count($mesColles);
	$no = 1;
	$idSem0 = ($mesColles[0])['id_semaine'];
	$div = tabDivisionParIdColloscope();
	for ($i=0; $i<$nbColles; $i++) {
		$colle = $mesColles[$i];
		if ($i%2) $colle['eo']='odd'; else $colle['eo']='even';
		$colle['nSemaine'] = ($semainesParId[$colle['id_semaine']])['Nom'];
		if ($colle['id_semaine']!=$idSem0) {
			$idSem0 = $colle['id_semaine'];
			$colle['styleTr'] = "border-top: solid 1px;";
		}
		$colle['nJour'] = numVersJour($colle['Jour']);
		$leGroupe = $colle['Groupe'];
		$lIdColl = $colle['idColloscope'];
		// À revoir : il faut la classe, pas l'idColloscope...
		$colle['Classe'] = ($div[$lIdColl])['nom'];
		$tab = elevesDuGroupeAvecNote($colle['id_colle']);
		//$tab = elevesDuGroupe($leGroupe, $lIdColl);
		$colle['Debut'] = substr($colle['Debut'], 0, -3);
		$colle['Fin'] = substr($colle['Fin'], 0, -3);
		// Debug
		//print_r($colle); echo "<BR>";
		//$colle['Groupe'] .= " : ".$liste;
		$t->assign_block_vars('ligne',$colle);
		// deux lignes. Attention : dans l'ordre inverse ? ....
		foreach ($tab as $eleve) {
			$t->assign_block_vars('ligne.eleve', array('nom'=>$eleve['Nom']." ".$eleve['Prenom']));
		}
		// À changer pour prendre les notes : il faudra une fonction spécifique ?
		foreach ($tab as $eleve) {
			$idNote = $eleve['id_note'];
			$valeur = $eleve['Valeur'];
			$note = parseValeur($valeur);
			$t->assign_block_vars('ligne.note', array('groupement'=>$eleve['id_groupement'], 'idColle'=>$colle['id_colle'], 'idNote'=>$idNote, 'idColloscope'=>$lIdColl, 'numero'=>$no, 'origValeur'=>$note, 'valeur'=>$note));
			$no++;
		}
	}
	return $t->HTML_for_handle('liste');
	
}

function listerCrenauxDesSemaines($lesSemaines) {
	$code = HTMLCrenauxDesSemaines($lesSemaines);
	echo $code;
}

function listerCrenauxChoixSemaines($debut=0, $fin=-1, $afficher=True, $idPop=False) {
	global $session;
	global $accesDB;
	
	$lesSemaines = semainesJusquAAujourdhui();
	if (count($lesSemaines)==0) {
		echo "Il n'y a pas de semaines de colles. Probl&egrave;me ?<BR>";
		return;
	}

	$code = "";
	if ($afficher) {
		// Debug
		//echo "D, F : $debut, $fin<BR>";
		//print_r($lesSemaines); echo "<BR>";
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
		// Debug
		//print_r($semainesAffichees); echo "<BR>";
		$code = HTMLCrenauxDesSemaines($semainesAffichees);
	}
	$popDebut = popupSemaines($lesSemaines, $debut, "popDebut");
	$popFin = popupSemaines($lesSemaines, $fin, "popFin");

	$t = new Template(tplPath());
	$t->set_filenames(array('liste'=>'formSemainesProf.tpl'));
	$t->assign_vars( array('action'=>'filtrer', 'quand'=>'?', 'debut'=>$popDebut, 'fin'=>$popFin, 'listeColles'=>$code) );
	$t->pparse('liste');
}

function listerCrenaux($quand) {
	switch ($quand) {
		case "":
		case"cette":
		$sem = cetteSemaine();
		if ($sem) {
			//listerCrenauxDesSemaines( array( (array) $sem) );
			listerCrenauxChoixSemaines(-1, -1);
		} else {
			echo "il n'y a pas de semaine courante !<BR>";
		}
		break;
		case "toutes":
		listerCrenauxChoixSemaines();
		break;
		default:
		echo "Rien à faire pour '$quand'(pour l'instant)<BR>";
	}
}

function filtrerCrenaux($quand) {
	// Debug
	//print_r($_POST); echo "<BR>";
	$debut = $_POST['popDebut'];
	$fin = $_POST['popFin'];
	listerCrenauxChoixSemaines($debut, $fin, True, True);
}

function modifierNotes() {
	global $session;
	global $accesDB;
	
	// Debug
	//print_r($_POST); echo "<BR>";
	$fait = False;
	if (array_key_exists('valid', $_POST)) {
		$groupement = $_POST['groupement'];
		$idColloscope = $_POST['idColloscope'];
		$idColle = $_POST['idColle'];
		$idNote = $_POST['idNote'];
		$champNote = $_POST['champNote'];
		$champOrig = $_POST['origNote'];
		foreach ($groupement as $n=>$groupe) {
			if ($idNote[$n]) {
				if ($champOrig[$n]!=$champNote[$n]) {
					$note  = parseNote($champNote[$n]);
					$req = "UPDATE ".PrefixeDB."Note SET Valeur='$note' WHERE id_note=$idNote[$n]";
					// Debug
					echo "$req<BR>";
					$res = $accesDB->ExecRequete($req);
					$Fait = True;
				}
			} else {
				if ($champNote[$n]) {
					$note  = parseNote($champNote[$n]);
					$req = "INSERT INTO ".PrefixeDB."Note (idColloscope, Valeur, Colle, Groupement) VALUES ($idColloscope[$n], '$note', $idColle[$n], $groupe)";
					// Debug
					echo "$req<BR>";
					$res = $accesDB->ExecRequete($req);
					$fait = True;
				}			
			}
		}
	}
	if (!$fait) {
		// Debug
		echo "Rien &agrave; faire<BR>";
	}
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
	
	$cren = mesCrenaux();
	$liste = listeSQLMesCrenaux();
	echo "Mes crenaux : ";  print_r($liste); echo "<BR>";
	
	$lesSemaines = semainesJusquAAujourdhui();
	$liste = listeIDSemaines($lesSemaines);
	echo "les id des semaines : $liste<BR>";
	
}

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
	
	switch ($action) {
		case "":
		case "lister":
		listerCrenaux($quand);
		break;
		
		case "modifier":
		// Debug
		//echo "On modifie <BR>";
		modifierNotes();
		break;
		
		case "filtrer":
		filtrerCrenaux($quand);
		break;
			
		default:
		echo "Action : $action($quand)<BR>";
		echo "rien à dire <BR>";
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