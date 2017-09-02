<?php

require("Chemins.php");

require_once("Template.php");
require_once("Util.php");
	
function Entete($titre, $texte="", $style="")
{
	$t = new Template(tplPath());
	$t->set_filenames(array('entete'=>'entete.tpl'));
	$css = cssPath();
	$t->assign_vars(array('titre'=>$titre,'texte'=>$texte,'css'=>$css, 'style'=>$style));
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
			$type = ucfirst($util->Type);
			$tNat = Session::tableauNaturesUtilisateur($util->Nature);
			print_r($tNat); echo "<BR>";
			//echo "menu pour un $type dans l'&eacute;tat $etat<BR>\n";
			$t = new Template(tplPath());
			$t->set_filenames(array('menu'=>"menu$type.tpl"));
			$qui = "$util->Prenom $util->Nom";
			$quoi = $session->natureUtilisateur($util->Nature);
			//echo "etat : $etat";
			if ($etat==VALIDE) {
				//echo " valide<BR>";
				$conn = $session->formulaireDeconnexion(Debut);
			} else {
				//echo " expire<BR>";
				$msg =  "Votre session a expir&eacute;. Il faut vous identifier...";
				$session->detruireSession();
				$conn = $session->formulaireIdentification(Debut,$util->NomDUtilisateur,$msg);			
			}
			$t->assign_vars(array('qui'=>$qui, 'quoi'=> $quoi, 'connection'=>$conn, 'racineRoot'=>racineRoot));
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