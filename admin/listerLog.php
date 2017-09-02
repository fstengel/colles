<?php

require("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
require(libPath()."Session.php");

//require_once(libPath()."Constantes.php");


$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."admin/GererCrenaux.php",array(Admin),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

function actionListeLog($limit=30) {
	global $accesDB, $session;
		
	$reqSQL = "SELECT l.Instant, l.Action, p.Nom, p.Prenom, p.Nature FROM ".PrefixeDB."Log as l, "
		.PrefixeDB."Personne as p WHERE p.id_personne=l.Personne ORDER BY Instant DESC LIMIT 0,$limit";
	$res = $accesDB->ExecRequete($reqSQL);
	$t = new Template(tplPath());
	$t->set_filenames(array('liste'=>"listeLog.tpl"));
	$t->assign_vars(array('nb'=>$limit));
	$nlig=0;
	while ($ligne=$accesDB->LigneSuivante($res)) {
		$nlig++;
		if ($nlig%2) $ligne['eo']="odd"; else $ligne['eo']="even";
		$sNature = Session::natureUtilisateur($ligne['Nature']);
		$ligne['NP'] = $ligne['Nom']." ".$ligne['Prenom']." (".$ligne['Nature'].' : '.$sNature.")";
		//$conn[] = $ligne;
		$t->assign_block_vars('ligne',$ligne);
	}
	//print_r($conn); echo "<BR>";
	$t->pparse('liste');
	
}

function main() {
	echo "<HR>\n";
	actionListeLog();	
}

entete("Gestion des cr&eacute;neaux");

menu($session,$accesDB);

main();


piedDePage();

restoreRoot();

?>