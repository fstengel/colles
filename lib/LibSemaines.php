<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

require("Chemins.php");

require_once("Constantes.php");
require_once(libPath()."Design.php");

/**
 * Suppose que $accesDB existe et a été défini par un appel à new AccesDB sinon...
 */

/**
 * Retourne l'objet contenant la ligne pour la semaine courante (s'il y en a une)
 *
 * @return object|null la semaine courante
 */
function cetteSemaine() {
	global $accesDB;
	
	$auj = aujourdhui();
	$req = "SELECT * FROM ".PrefixeDB."Semaine WHERE Debut<='$auj' and Fin >='$auj'";
	$res = $accesDB->ExecRequete($req);
	$sem = $accesDB->ObjetSuivant($res);
	return $sem;
}

/**
 * Retourne l'objet contenant la ligne pour la semaine suivante (s'il y en a une)
 *
 * @return object|null la semaine suivante
 */
function semaineProchaine() {
	global $accesDB;
	
	$auj = aujourdhui();
	$fin = finAnnee();
	$req = "SELECT * FROM ".PrefixeDB."Semaine WHERE Debut>'$auj' and Fin<='$fin' ORDER BY Debut ASC LIMIT 1";
	$res = $accesDB->ExecRequete($req);
	$sem = $accesDB->ObjetSuivant($res);
	return $sem;
	
}

/**
 * Retourne l'objet contenant la ligne pour la semaine derniere (s'il y en a une)
 *
 * @return object|null la semaine derniere
 */
function semaineDerniere() {
	global $accesDB;
	
	$auj = aujourdhui();
	$debut = debutAnnee();
	$req = "SELECT * FROM ".PrefixeDB."Semaine WHERE Fin <'$auj' AND Debut>='$debut' ORDER BY Fin DESC LIMIT 1";
	$res = $accesDB->ExecRequete($req);
	$sem = $accesDB->ObjetSuivant($res);
	return $sem;
	
}

/**
 * Retourne le tableau contenant les semaines passées (celles précédant la courante) de l'année courante
 *
 * @return array(array) les semaiens passées
 */
function semainesPassees() {
	global $accesDB;
	
	$auj = aujourdhui();
	$debut = debutAnnee();
	$req = "SELECT * FROM ".PrefixeDB."Semaine WHERE Debut>='$debut' AND Fin <'$auj' ORDER BY Debut ";
	$res = $accesDB->ExecRequete($req);
	$sem = $accesDB->ToutesLesLignes($res);
	return $sem;
	
}

/**
 * Retourne le tableau contenant les semaines passées en incluant la semaine courante de l'année courante
 *
 * @return array(array) les semaiens passées
 */
function semainesJusquAAujourdhui() {
	global $accesDB;
	
	$auj = aujourdhui();
	$debut = debutAnnee();
	$req = "SELECT * FROM ".PrefixeDB."Semaine WHERE Debut <='$auj' AND Debut>='$debut' ORDER BY Debut ";
	$res = $accesDB->ExecRequete($req);
	$sem = $accesDB->ToutesLesLignes($res);
	return $sem;
	
}

/**
 * Retourne la liste SQL des id de semaine.
 * Par exemple pour un tableau de semaines d'id 219, 220, 221 il retournera "( 219, 220, 221 )"
 *
 * @param array(array) $semaines les semaines
 *
 * @return string la liste SQL des id des semaines
 */
function listeIDSemaines($semaines) {
	$liste = "( ";
	$nSem = count($semaines);
	for ($i=0; $i<$nSem; $i++) {
		$sem = $semaines[$i];
		$liste .= $sem['id_semaine'];
		if ($i<$nSem-1) $liste .= ", ";
	}
	$liste .= ' )';
	return $liste;
}

/**
 * Retourne les items pour un popup de semaines
 *
 * @param array(array) $semaines la liste des semaines
 * @param int $i=-1 l'indice selected. Si <0 on compte du dernier (-1=dernier)
 * @param string $nom="popSemaines" le nom du popup
 * @param string $nom="formSemaines" le nom du formulaire
 *
 * @return string le code HTML du popup
 */
function popupSemaines($semaines, $i=-1, $nom="popSemaines", $form="formSemaines") {
	$n = count($semaines);
	if ($i<0) $i = $n+$i;
	if ($i<0) $i = 0;
	if ($i>=$n) $i = $n-1;
	// Debug
	//echo "pS : $i, $n<BR>";
	$pop = "<select name=\"$nom\" onchange=\"document.$form.filtrer.click()\" >";
	foreach ($semaines as $k=>$sem) {
		$id = $sem['id_semaine'];
		$nom = $sem['Nom'];
		if ($k==$i) {
			$ligne = "<option value='$id' selected>$nom</option>";
		} else {
			$ligne = "<option value='$id'>$nom</option>";
		}
		$pop .= $ligne;
	}
	$pop .= "</select>";
	return $pop;
}
?>