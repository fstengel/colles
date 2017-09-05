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

$ok = $session->controleAccesCode(racineSite."gererResponsable.php?action=lister&quand=cette",array(Responsable),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

require_once(libPath()."LibSemaines.php");
require_once(libPath()."LibNotes.php");

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

function afficherNotes($semaines, $idColl, $matiere) {
	echo HTMLPourAfficherNotes($semaines, $idColl, $matiere);
}

function HTMLPourAfficherNotes($semaines, $idColl, $matiere) {
	global $accesDB;
	
	if (count($semaines)==0) {
		return;
	}
	$idSemaines = listeIDSemaines($semaines);
	
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
	$t->assign_vars(array('classe'=>$nomClasse));
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
 * @deprecated
 */
function listerMesNotesDesSemaines($semaines) {
	global $session;
	global $accesDB;
	
	if (count($semaines)==0) {
		return;
	}
	$moi = $session->utilisateur;
	$monId = $moi->id_personne;
	$annee = cetteAnnee()."-01-01";
	$req = "SELECT * FROM ".PrefixeDB."Responsable AS R JOIN ".PrefixeDB."IdColloscope AS IC ON R.Division=IC.division WHERE Personne=$monId AND IC.annee='$annee'";
	//echo "$req<BR>";
	$res = $accesDB->ExecRequete($req);
	$lesResponsables = $accesDB->ToutesLesLignes($res);
	// Passer ça en Template ?
	$t = new Template(tplPath());
	$t->set_filenames(array('liste'=>'formSemainesResponsable.tpl'));
	$t->assign_vars(array('classe'=>'', 'debut'=>'', 'fin'=>'') );
	//echo '<div class="blockTable">';
	foreach ($lesResponsables as $i=>$responsable) {
		//print_r($responsable); echo "<BR>";
		$matiere = $responsable['Matiere'];
		$idColl = $responsable['id'];
		//echo '<div class="innerTable">';
		// Récupérer le HTML plutot...
		$code = HTMLPourAfficherNotes($semaines, $idColl, $matiere);
		$t->assign_block_vars('notes', array('classe'=>$code) );
		//echo $code;
		//echo "</div>";
		//echo " ";
	}
	//echo "</div>";
	$t->pparse('liste');
}

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

function filtrerNotes($quand) {
	$debut = $_POST['popDebut'];
	$fin = $_POST['popFin'];
	$classe = $_POST['popClasse'];
	
	//Debug
	//echo "Filtrer  :"; print_r($_POST); echo "<BR>";
	listerMesNotesChoixDesSemaines($debut, $fin, $classe, True, True);
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