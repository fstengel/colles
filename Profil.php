<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

require_once("Chemins.php");

require_once(libPath()."Design.php");
require_once(libPath()."Util.php");

// Ce qui est nécessaire au début d'une page...
require_once(libPath()."AccesDB.php");
require_once(libPath()."Session.php");;

$accesDB = new AccesDB;
$session = new Session($accesDB);

//$ok = $session->controleAcces("Profil.php",array("","admin","prof","eleve"),PageDeBase);
$ok = $session->controleAccesCode("Profil.php",array(Admin, Prof, Responsable, Eleve),PageDeBase);
if ($ok!=VALIDE) {
	// Normalement on ne passe pas par là !
	piedDePage();
	return;
}

define("pasDePOST",1);
define("POSTValide",2);
define("POSTMailInvalide",3);
define("POSTPassInvalide",4);


function validerMail($mail) {
	$valid = preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#",$mail);
	return $valid;
}

function listeMatieres($util) {
	global $accesDB;
	
	if ($util->Nature&(Prof|Responsable)) {
		$id = $util->id_personne;
		$reqSQL = "SELECT m.Nom FROM ".PrefixeDB."Matiere as m, ".PrefixeDB."Intervenant as i "
			."WHERE i.Personne=$id AND m.id_matiere=i.Matiere";
		$res = $accesDB->ExecRequete($reqSQL);
		$lm = array();
		while($ligne=$accesDB->LigneSuivante($res)) {
			$lm[]=$ligne['Nom'];
		}
		$nm = count($lm);
		switch ($nm) {
			case 0:
				return "<B>Probl&egrave;me ! Pas de mati&egrave;res !!!</B>";
				break;
			case 1:
				return "Mati&egrave;re : ".$lm[0].".";
				break;
			default:
				return "Mati&egrave;res : ".implode(", ",$lm).".";
		}
	} else {
		return "";
	}
}

function decortiquerPOST() {
	global $session;
	global $accesDB;
	global $msgMail, $msgPass;
	
	
	if (isset($_POST['chMail'])) {
		//echo "Changement de mail<BR>\n";
		$mail = $_POST['mail'];
		if (validerMail($mail)) {
			//echo "Mail valide : $mail<BR>";
			//echo "On le change<BR>";
			$util = $session->lUtilisateur();
			$id_util = $util->id_personne;
			$reqSQL = "UPDATE ".PrefixeDB."Personne SET Mail='$mail' WHERE id_personne='$id_util'";
			//print_r($util); echo "<BR>\n";
			//echo "$reqSQL<BR>\n";
			$accesDB->ExecRequete($reqSQL);
			$util= $session->lUtilisateur(true);
			$msgMail = '<div>Mail modifi&eacute; avec succ&egrave;s<BR></div>';
			return POSTValide;
		} else {
			$msgMail =  '<div style="color:red;">Mail invalide !</div>';
			return POSTMailInvalide;
		}
	} else if (isset($_POST['chPass'])) {
		//echo "Changement de mot de passe<BR>\n";
		$pass0 = $_POST['pass0'];
		$util = $session->lUtilisateur();
		//if ($util->MotDePasse == Session::codage($pass0))
		if ($session->verifierPasse($util->MotDePasse, $pass0))
		{
			//echo "MDP OK<BR>\n";
			$pass1 = $_POST['pass1'];
			$pass2 = $_POST['pass2'];
			if ($pass1==$pass2) {
				if ($pass1==="") {
					$passe = "";
					$extra = "(attention, il est vide !)";
				} else {
					$mdp = Session::codage($pass1);
					$passe = prefixeCrypte.$mdp;
					$extra = "";
				}
				$id_util = $util->id_personne;
				$reqSQL = "UPDATE ".PrefixeDB."Personne SET MotDePasse='$passe' WHERE id_personne='$id_util'";
				$accesDB->ExecRequete($reqSQL);
				$util=$session->lUtilisateur(true);
				$msgPass = "<div>Mot de Passe modifi&eacute; avec succ&egrave;s $extra<BR></div>";
				return POSTValide;
			} else {
				$msgPass = '<div style="color:red;">Nouveaux Mots de Passe diff&eacute;rents !</div>'	;
				return POSTPassInvalide;		
			}
		} else {
			$msgPass =  '<div style="color:red;">Mot de Passe incorrect !</div>';
			return POSTPassInvalide;		
		}
	} else return pasDePOST;
}

function afficherFormulaire() {
	global $session;
	global $accesDB;
	global $msgMail, $msgPass;
	
			$t = new Template(tplPath());
			$t->set_filenames(array('profil'=>'profil.tpl'));
			$util = $session->lUtilisateur();
			$nom = $util->Nom;
			$prenom = $util->Prenom;
			//$type = ucfirst($util->Type);
			$nature = $util->Nature;
			$texteNature = Session::natureUtilisateur($nature);
			$lm = listeMatieres($util);
			$mail = $util->Mail;
			$t->assign_vars(array('nom'=>$nom, 'prenom'=>$prenom, 'type'=>$texteNature, 'mail'=>$mail, 'msgMail'=>$msgMail, 'msgPass'=>$msgPass, 'lm'=>$lm));
			$t->pparse('profil');
}

function main() {
	global $session;
	global $accesDB;
	global $msgMail, $msgPass;
	
	$msgMail = "";
	$msgPass = "";
	
	$dp = decortiquerPOST();
	switch ($dp) {
		case pasDePOST:
		case POSTPassInvalide:
		case POSTMailInvalide:
		case POSTValide:
			afficherFormulaire();
			break;
		default:
			echo "Tout est OK<BR>\n";
			echo '<meta http-equiv="Refresh" content="0; url='."Profil.php".'">';
			break;
	}
}

entete("Votre profil");

menu($session,$accesDB);

main();


piedDePage();

restoreRoot();
?>