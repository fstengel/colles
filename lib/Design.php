<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

require("Chemins.php");

require_once("Template.php");
require_once("Util.php");
require_once("LibSemaines.php");
	
function Entete($titre, $texte="", $style="", $script="")
{
	$t = new Template(tplPath());
	$t->set_filenames(array('entete'=>'entete.tpl'));
	$css = cssPath();
	$js = jsPath();
	// Bricolage ! Si finit par .js c'est un nom de fichier sinon c'est un script complet...
	if (strlen($script)!=0) {
		if (endsWith($script,".js")) {
			$scr = "<script src=$js$script></script>";
		} else {
			$scr = "<script>$script</script>";
		}
	} else {
		$scr = "";
	}
	$t->assign_vars(array('titre'=>$titre,'texte'=>$texte,'css'=>$css, 'style'=>$style, 'script'=>$scr));
	$t->pparse('entete');
}

function menu($session, $accesDB) {
	$etat = $session->etatSession();
	switch ($etat) {
		case INVALIDE:
			//echo "menu dans le cas (non) connect&eacute;<BR>\n";
			switch ($session->lErreurSession()) {
				case FALSE:
					$msg = "<B>Vous n'&ecirc;tes pas connect&eacute;</B>\n";
					break;
				case MotDePasseIncorrect:
					$msg = "<B>Votre mot de passe est incorrect</B>\n";
					break;
				case LoginIncorrect:
					$msg = "<B>Nom d'utilisateur inconnu</B>\n";
					break;
			}
			echo "<HR>\n";
			if (isset($login))
				echo $session->formulaireIdentification(Debut,$login,$msg);
			else
				echo $session->formulaireIdentification(Debut,"",$msg);
			break;
		case EXPIREE:
		case VALIDE:
			$util = $session->lUtilisateur();
			//print_r($util); echo "<br>";
			// Il n'y a plus de colonne Type...
			//$type = ucfirst($util->Type);
			$tNat = Session::tableauNaturesUtilisateur($util->Nature);
			//print_r($tNat); echo "<BR>";
			//echo "menu pour un $type dans l'&eacute;tat $etat<BR>\n";
			$t = new Template(tplPath());
			$t->set_filenames(array('menu'=>"menu.tpl"));
			$qui = "$util->Prenom $util->Nom";
			$quoi = Session::natureUtilisateur($util->Nature);
			//echo "etat : $etat";
			if ($etat==VALIDE) {
				//echo " valide<BR>";
				$conn = $session->formulaireDeconnexion(Debut);
			} else {
				// Cas EXPIREE
				//echo " expire<BR>";
				$msg =  "Votre session a expir&eacute;. Il faut vous identifier...";
				$session->detruireSession();
				$conn = $session->formulaireIdentification(Debut,$util->NomDUtilisateur,$msg);			
			}
			// Bof. Très codage en dur
			$extra = "";
			$page = "index.php";
			// FAIT Pour une future évolution de menu.tpl : replacer {racineRoot}{admin}/{page} par {racineRoot}{newPage} ou équivalent...
			$newPage = "/index.php";
			if ($util->Nature==Eleve) {
				//$page = "gererEleve.php?action=lister&quand=cette";
				$newPage = defautEleve;
			}
			if ($util->Nature==Responsable) {
				$newPage = defautResponsable;
			}
			if ($util->Nature & Prof) {
				//$page = "gererProf.php?action=lister&quand=cette";
				$newPage = defautProf;
			}
			if ($util->Nature & Admin) {
				//$extra="/admin";
				$newPage = defautAdmin;
			}
			$newPage = pageParDefaut($util->Nature);
			$sem = cetteSemaine();
			$debut = dateSQLVersFrancais($sem->Debut);
			$fin = dateSQLVersFrancais($sem->Fin);
			$duree = diffrenceEnJours($sem->Debut, $sem->Fin);
			$date = aujourdhui(True);
			if ($sem) {
				$message = "Nous sommes le $date. La semaine courante est la semaine $sem->Nom (du $debut au $fin).";
				if ($duree>7) {
					$message .= "<BR>Attention la semaine est longue (2 semaines calendaires ou plus). Faites attention aux dates...";
				}
			} else {
				$message = "Nous sommes le $date et il n'y a pas de semaine courante.";
			}
			$t->assign_vars(array('qui'=>$qui, 'quoi'=> $quoi, 'connection'=>$conn, 'racineRoot'=>racineRoot, 'admin'=>$extra, 'page'=>$page,'newPage'=>$newPage, 'message'=>$message));
			foreach ($tNat as $nat) {
				$tT = new Template(tplPath());
				$tT->set_filenames(array('menu'=>"menu$nat.tpl"));
				//$tM['menu'] = "Menu $nat";
				$tT->assign_vars(array('racineRoot'=>racineRoot));
				$tM['menu'] = $tT->HTML_for_handle('menu');
				$t->assign_block_vars('menus', $tM);
			}
			$t->pparse('menu');
			break;
	}
}

function piedDePage()
{
	$t = new Template(tplPath());
	$t->set_filenames(array('pied'=>'pied.tpl'));
	$t->pparse('pied');
}

restoreRoot();
?>