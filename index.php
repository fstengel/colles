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

function redirection() {
	global $session;
	
	$util = $session->lUtilisateur();
	// Pour pouvoir lire le message de redirection passer cette durée à >0...
	$duree = 0;

	if ($util->Nature & Admin) {
		// Modifier pour redirection immédiate !
		echo "<p>Admin : redirection dans $duree(s) vers admin/index</p>";
		//echo '<meta http-equiv="Refresh" content="0; url='.racineSite.'admin/index.php">';
		$url = racineSite.defautAdmin;
		echo "<meta http-equiv=\"Refresh\" content=\"$duree; url=$url\">";
		return;
	}
	if ($util->Nature & Prof) {
		echo "<p>Prof : redirection dans $duree(s) vers gererProf</p>";
		//echo '<meta http-equiv="Refresh" content="0; url='.racineSite.'gererProf.php?action=lister&quand=cette">';
		$url = racineSite.defautProf;
		echo "<meta http-equiv=\"Refresh\" content=\"$duree; url=$url\">";
		return;
	}
	if ($util->Nature == Eleve) {
		echo "<p>Vous êtes un eleve... On va rediriger vers la page idoine dans $duree(s)...</p>";
		//echo '<meta http-equiv="Refresh" content="0; url='.racineSite.'gererEleve.php?action=lister&quand=cette">';
		$url = racineSite.defautEleve;
		echo "<meta http-equiv=\"Refresh\" content=\"$duree; url=$url\">";
		return;
	}
	if ($util->Nature == Responsable) {
		echo "<p>Prof : redirection dans dans $duree(s) vers gererResponsable</p>";
		//echo '<meta http-equiv="Refresh" content="0; url='.racineSite.'gererEleve.php?action=lister&quand=cette">';
		$url = racineSite.defautResponsable;
		echo "<meta http-equiv=\"Refresh\" content=\"$duree; url=$url\">";
		return;
	}	
}

entete("Gestion des Colles &mdash; PC Fabert ".Annee);

$etat =  $session->etatSession();

if (($etat==INVALIDE) && isset($_POST['login']) && isset($_POST['ident'])) {
	$login = $_POST['login'];
	$password = $_POST['password'];
	//echo "On repassee par ici avec les infos : '$login' et '$password'<BR>";
	switch ($session->creerSession($login,$password)) {
		case TRUE:
			//echo "<B>Session cr&eacute;ee...</B><BR>";
			$etat = VALIDE;
			$sess = $session->laSession();
			$util = $session->lUtilisateur();
			break;
		default:
			break;
	}
}

if (($etat==VALIDE)&&(isset($_POST['logout']))) {
	//echo "l'utilisateur veut se déconnecter !<BR>\n";
	$session->Deconnexion();
	$etat = INVALIDE;
}

menu($session,$accesDB);

/** 
* @todo  Améliorer le template
*/
if (($etat==VALIDE) && ($session->identifie) && ($session->motDePasseAChanger)) {
	//print_r($session);
	$t = new Template(tplPath());
	$t->set_filenames(array('mess'=>"message.tpl"));
	$mess = "Il FAUT changer le mot de passe !";
	$t->assign_vars(array('message'=>$mess));
	$t->pparse('mess');
}

//print_r($session); echo "<BR>";
// Attention fait (partiellement) double emploi avec Design.menu()
// À changer pour ne prendre qu'un seul : une fonction avec un return ?
if ($etat==VALIDE) {
	redirection();
}

piedDePage();

restoreRoot();
?>