<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

define("debug",true);
//define("debug",false);

//define("local",false);
define("local",true);

//define("Annee",2010);
define("Annee",date("Y"));
define("unJour",86400);
//define("futurProche",86400);
define("futurProche",86399);

define("Lundi",1);
define("Vendredi",5);

// Lecture de CSV
define("MaxLignes",10);
define("MaxColonnes",8);

// L'année/jours courants pour debug
// À commenter si on veut utiliser la date du jour...
//<DEBUG>
define("AnneeDefaut",2015);
define("JourDefaut", "2015-09-29");
//</DEBUG>




//define("afficherPasse",true);
define("afficherPasse",false);
define("prefixeClair","Clair:");
define("prefixeCrypte","*****:");

if (local) {
	define("racineRoot","/colles");
	define("racineSite","http://localhost:8888/colles/"); // Local
} else {
	define("racineRoot","");
	define("racineSite","http://colles.stengel.fr/"); // distant
}
define("Debut",racineSite."index.php");
define("PageDeBase",Debut);

// Les masques
define("Eleve", 0x1);
define("Prof", Eleve<<1);
define("Responsable", Eleve<<2);
define("Admin", Eleve<<8);
// Les pages d'entrée
define("defautEleve","/gererEleve.php?action=lister&quand=cette");
define("defautProf","/gererProf.php?action=lister&quand=cette");
define("defautResponsable", "/gererResponsable.php");
define("defautAdmin","/admin/index.php");

//Les notes magiques
define("Absent", -1);
define("NonNote", -2);
define("PasDeNote", -3); // Pour gérer le cas où un note a été enlevée
define("TexteVersNote", array("A"=>Absent, "ABS"=>Absent, "N"=>NonNote, "NN"=>NonNote, ""=>PasDeNote));
define("NoteVersTexte", array(Absent=>"Abs", NonNote=>"NN", PasDeNote=>"") );


?>