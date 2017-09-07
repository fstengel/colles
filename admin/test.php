<?php

require("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
//Changé de require vers require_once
require_once(libPath()."Session.php");

$accesDB = new AccesDB;
$session = new Session($accesDB);

$ok = $session->controleAccesCode(racineSite."admin/test.php",array(Admin),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

function main() {
	echo "Tout OK<BR>";
	echo 'A<span id="demo"></span>B';
	$script = 'document.getElementById("demo").innerHTML = 5 + 6';
	echo "<button onclick='$script'>Try it</button><BR>";
	
	define("Absent", -1);
	define("NonNote", -2);
	define("test", array("A"=>Absent, "ABS"=>Absent, "N"=>NonNote, "NN"=>NonNote));
	define("truc", array(Absent=>"Abs", NonNote=>"NN") );
	print_r(truc); echo "<BR>";
	
	$a = 1.0;
	$x = $a==1;
	echo "$a, '$x'<BR>";
}


entete("Gestion des Oraux &mdash; PC Fabert 2009 &mdash; Test mail");

menu($session,$accesDB);

main();

piedDePage();

restoreRoot();
?>