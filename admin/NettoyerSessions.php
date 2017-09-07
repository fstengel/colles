<?php

require_once("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
//Changé de require vers require_once
require_once(libPath()."Session.php");

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."admin/NettoyerSessions.php",array(Admin),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

// Si pas d'erreur (redirection et autres), on peut continuer...

entete("Netoyage des sessions...");

menu($session,$accesDB);

//echo "<HR>\n";


if (isset($_GET['action']))
	$action = $_GET['action'];
else
	$action="";

//echo "Nettoyage<BR>";

$s = $session -> laSession();
$id_s = $s->id_session;
$t = date("U");

if (isset($_POST['valid'])) {
	echo "On nettoye...\n";
	$resultat = $accesDB->ExecRequete("DELETE From ".PrefixeDB."Session WHERE FIN<$t AND id_session<>'$id_s'");
	$session->log('clean');
	echo " c'est fait.<BR>";
	echo '<meta http-equiv="Refresh" content="1; url=../index.php">';
} else {
	$res = $accesDB->ExecRequete("SELECT * FROM ".PrefixeDB."Session WHERE FIN<$t AND id_session<>'$id_s'");
	$nb = 0;
	while ($ligne=$accesDB->LigneSuivante($res)) $nb++;
	if ($nb>0) {
		$t = new Template(tplPath());
		$t->set_filenames(array('nettoyer'=>"formNettoyage.tpl"));
		if (($nb>1)||($nb==0)) $plr="s"; else $plr="";
		$t->assign_vars(array('nb'=>$nb, 'plr'=>$plr));
		$t->pparse('nettoyer');
	} else {
		echo "Il n'y a rien &agrave; faire !";
	}
}


piedDePage();

restoreRoot();
?>