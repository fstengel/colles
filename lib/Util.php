<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

require_once("Constantes.php");
//require_once("HTML.php");
require_once("NettoyerTexte.php");

/**
 * Teste si la chaîne $haystack a pour début $needle
 *
 * @param string $haystack chaîne dans laquelle on cherche
 * @param string $needle chaîne recherchée
 *
 * @return boolean vrai si trouvée
 */

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
	 if ($length==0) {
		 return true;
	 }
     return (substr($haystack, 0, $length) === $needle);
}

/**
 * Teste si la chaîne $haystack a pour fin $needle
 *
 * @param string $haystack chaîne dans laquelle on cherche
 * @param string $needle chaîne recherchée
 *
 * @return boolean vrai si trouvée
 */

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

$tabNumVersJour = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");

$tabJourVersNum = array();
for ($i=0; $i<7; $i++) {
	$j = $tabNumVersJour[$i];
	$tabJourVersNum[$j] = $i;
}

/**
 * Transforme un jour texte en un numéro dans la semaine (dimanche=0)
 *
 * @param string $jour le nom du jour
 *
 * @return int le numéro du jour
 */

function jourVersNum($jour) {
	global $tabJourVersNum;
	
	$j = strtolower($jour);
	return $tabJourVersNum[$j];
}

/**
 *	Transforme un numéro de jour (dim=0) en son nom en minuscules
 *
 * @param int $i le numéro
 *
 * @return string le nom du jour en minuscules
 */

function numVersJour($i) {
	global $tabNumVersJour;
	return $tabNumVersJour[$i];
}

/**
 * Renvoie l'année scolaire courante. L'année par défaut est retournée si celle-ci existe.
 * Pour 2016-09-15 retourne 2016 (année scolaire 2016-17)
 * Pour 2016-05-25 retourne 2015 (année scolaire 2015-16)
 *
 * @return string l'année courante
 */

function cetteAnnee() {
	if (defined("AnneeDefaut")) {
		return AnneeDefaut;
	} else {
		$dt = new DateTime();
		$a = $dt->format("Y");
		$m = $dt->format("m");
		if ($m<8) {
			$a--;
		}
		return $a;
	}
}

/**
 * Retourne le "premier" jour de l'année scolaire courante
 * Pour 2016-17 retourne 2016-08-01
 *
 * @return string le premier jour de l'année
 */
function debutAnnee() {
	$a = cetteAnnee();
	return $a."-08-01";
}

/**
 * Retourne le "dernier" jour de l'année scolaire courante
 * Pour 2016-17 retourne 2017-07-31
 *
 * @return string le premier jour de l'année
 */
function finAnnee() {
	$a = cetteAnnee()+1;
	return $a."-07-31";
}

/**
 * Renvoie aujourd'hui sous forme de date SQL (ou Français)
 *
 * Si AnneeDefaut est defini alors renvoie JourDefaut (à revoir ?)
 *
 * @param boolean $francais=False : si True la date est au format d/m/Y sinon Y-m-d
 *
 * @return string la date actuelle format SQL (ou Français)
 */

function aujourdhui($francais=False) {
	if (defined("AnneeDefaut")) {
		$auj =  JourDefaut;
		if ($francais) {
			$stamp = strtotime($auj);
			$auj = date("d/m/Y",$stamp);
		}
		return $auj;
	} else {
		if ($francais) $auj = date("d/m/Y"); else $auj = date("Y-m-d");
		return $auj;
	}
}

/**
 * Transforme une date SQL en une date en Français.
 *
 * @param string $date La date SQL (Y-m-d)
 *
 * @return string la date en Français (d/m/Y)
 */

function dateSQLVersFrancais($date) {
	$stamp = strtotime($date);
	return date("d/m/Y", $stamp);
}

/**
 * Calcule la différence en jours entre deux dates SQL
 *
 * @param string $debut début en sql
 * @param string $fin fin en SQL
 *
 * @return int le nombre de jours qui séparent ces deux dates.
 */

function diffrenceEnJours($debut, $fin) {
	$d = new DateTime($debut);
	$f = new DateTime($fin);
	$i = $f->diff($d);
	return $i->days;
}

/**
 * "Normalise" une date SQL (chaîne Y-m-d) vers un jour donné. C-à-d force le jour de la date à être le jour donné de la semaine correspondant à cette date.`
 *
 * Le 2015-09-29 est un mardi. L'appel normaliseDateVersJour('2015-09-29', 1) retourne '2015-09-28' : c'est le lundi de la semaine contenant le 2015-09-29.
 *
 * @param string $date la date à normaliser
 * @param int $jour le jour destination
 *
 * @return string la date normalisée
 */

function normaliseDateVersJour($date, $jour) {
	$dt = new DateTime($date);
	$sem = $dt->format('W');
	$an = $dt->format('Y');
	$dtn = (new DateTime())->setISODate($an, $sem, $jour);
	return $dtn->format('Y-m-d');
}

/**
 * "Normalise" une date (DateTime)vers un jour donné. C-à-d force le jour de la date à être le jour donné de la semaine correspondant à cette date
 *
 * @see normaliseDateVersJour pour les explications.
 *
 * @param DateTime $date la date à normaliser
 * @param int $jour le jour destination
 *
 * @return DateTime la date normalisée
 */

function normaliseDateTimeVersJour($date, $jour) {
	$dt = $date;
	$sem = $dt->format('W');
	$an = $dt->format('Y');
	$dtn = (new DateTime())->setISODate($an, $sem, $jour);
	return $dtn;
}

/**
 * Détermine la page par défaut. C'est simple si l'utilisateur a une nature unique Sinon l'ordre d'importance est Admin>>Prof>>Responsable
 *
 * @param int $nature=-1 est -1 on prend la nature de l'utilisateur, sinon c'est $nature qui sera utilisée.
 *
 * @return string la page par défaut.
 *
 * REMARQUE Cette question ainsi que la suivante : à déplacer vers Design.php ?
 */

function pageParDefaut($nature=-1) {
	global $session;
	
	$util = $session->lUtilisateur();
	if ($nature==-1) {
		$nature = $util->Nature;
	}
	
	if ($nature & Admin) {
		$url = defautAdmin;
		return $url;
	}
	if ($nature & Prof) {
		$url = defautProf;
		return $url;
	}
	if ($nature == Eleve) {
		$url = defautEleve;
		return $url;
	}
	if ($nature == Responsable) {
		$url = defautResponsable;
		return $url;
	}
	return racineSite;
}

/**
 * Procède à la redirection vers la page de départ d'un type d'utilisateur
 *
 * @param int $nature=-1 est -1 on prend la nature de l'utilisateur, sinon c'est $nature qui sera utilisée.
 *
 * @return void
 */
function redirection($nature=-1) {
	global $session;
	
	$util = $session->lUtilisateur();
	if ($nature==-1) {
		$nature = $util->Nature;
	}
	// Pour pouvoir lire le message de redirection passer cette durée à >0...
	$duree = 0;

	$page = pageParDefaut($nature);
	$url = racineSite.$page;
	echo "<p>Redirection dans $duree(s) vers $page</p>";
	echo "<meta http-equiv=\"Refresh\" content=\"$duree; url=$url\">";
	return;
}

?>