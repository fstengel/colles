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

//print_r($session); echo "<BR>";
// Attention fait (partiellement) double emploi avec Design.menu()
// À changer pour ne prendre qu'un seul : une fonction avec un return ?
if ($etat==VALIDE) {
	redirection();
}

piedDePage();

restoreRoot();
?>