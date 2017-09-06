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

entete("Indentification...");

if (isset($_POST['cancel'])) {
	echo '<meta http-equiv="Refresh" content="0; url='.PageDeBase.'">';
}

if (isset($_GET['page']))
	$page = $_GET['page'];
else
	$page = PageDeBase;
if (isset($_GET['message']))
	$message = $_GET['message'];
else
	$message = 0;

if ($message==AccesInterdit) {
	$page = PageDeBase;
}

echo "<HR>";

$etat = $session->etatSession();

if (($etat==INVALIDE) && isset($_POST['login'])) {
	$login = $_POST['login'];
	$password = $_POST['password'];
	//echo "On repassee par ici avec les infos : $login et $password<BR>";
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

if ($etat==INVALIDE) {
	// Debug
	//echo "<B>Invalide</B> tentative de connexion...<BR>";
	switch ($session->lErreurSession()) {
		case FALSE:
			$msg = "Vous n'&ecirc;tes pas connect&eacute;";
			break;
		case MotDePasseIncorrect:
			$msg = "Votre mot de passe est incorrect";
			break;
		case LoginIncorrect:
			$msg = "Nom d'utilisateur inconnu";
			break;
	}
	if (isset($login))
		echo $session->formulaireIdentification("Login.php?page=$page",$login,$msg);
	else
		echo $session->formulaireIdentification("Login.php?page=$page","",$msg);
} else {
	$sess = $session->laSession();
	$util = $session->lUtilisateur();
	echo "Bonjour $util->Prenom $util->Nom :<BR>";
	if ($etat==EXPIREE) {
		$msg= "Votre session a expir&eacute;. Il faut &agrave; vous identifier...";
		$session->detruireSession();
		echo $session->formulaireIdentification("Login.php?page=$page",$util->NomDUtilisateur,$msg);
	} else {
		echo "Vous &ecirc;tes connect&eacute;";
		echo '<meta http-equiv="Refresh" content="0; url='.$page.'">';
	}
}


piedDePage();

restoreRoot();
?>