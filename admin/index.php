<?php

require("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
require(libPath()."Session.php");

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."admin/index.php",array(Admin),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}


function listerConnectes() {
	global $accesDB;
	global $session;
	
	$now=time();
	$reqSQL = "SELECT DISTINCT s.Personne, p.Nom, p.Prenom, p.Nature FROM ".PrefixeDB."Session as s, "
		.PrefixeDB."Personne as p WHERE s.Fin>='$now' and s.Personne=p.id_personne";
	$res = $accesDB->ExecRequete($reqSQL);
	//$conn = array();
	$t = new Template(tplPath());
	$t->set_filenames(array('liste'=>"listeConnectes.tpl"));
	$nlig=0;
	while ($ligne=$accesDB->LigneSuivante($res)) {
		$nlig++;
		if ($nlig%2) $ligne['eo']="odd"; else $ligne['eo']="even";
		$ligne['Info'] = $ligne['Nom']." ".$ligne['Prenom']." (".Session::natureUtilisateur($ligne['Nature']).")";
		//$conn[] = $ligne;
		$t->assign_block_vars('ligne',$ligne);
	}
	//print_r($conn); echo "<BR>";
	$t->pparse('liste');
}


function main() {
	listerConnectes();
}

entete("Gestion des Oraux &mdash; PC Fabert 2009 &mdash; Accueil admin");



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