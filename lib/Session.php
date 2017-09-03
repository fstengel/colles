<?php
/**
 * Gestion d'une session
 *
 * @author Frank STENGEL
 * @version 1.1 2017-09-03
 *
 * @var object session l'objet contenant la session. Une ligne de la table Session
 * @var string idSession l'id de la session
 * @var object accesDB l'objet de liaison avec la base de données
 * @var object utilisateur l'objet contenant les données de l'utilisateur. Une ligne de la table Personne
 * @var bool identifie si l'utilisateur est identifié
 * @var bool motDePasseAChanger si le mot de passe doit être changé.
 * @var int typeMotDePasse voir les constantes afférentes.
 * @var string mdpReel le mot de passe débarrassé de son préfixe éventuel.
 * @var string|False erreurSession il y a eu une erreur lors de l'ouverture
 * @var object tpl Le template des formulaires d'identification et de déconnexion.
 */
require("Chemins.php");

require_once(libPath()."AccesDB.php");
//require_once("lib/Formulaire.php");
require_once(libPath()."Template.php");

require_once(libPath()."Constantes.php");

define("INVALIDE",0);
define("EXPIREE",1);
define("VALIDE",2);

define("MotDePasseIncorrect",1);
define("LoginIncorrect",2);

define("AccesInterdit",1);

class Session {
	var $session, $idSession;
	//var $connexion;
	var $accesDB;
	var $utilisateur, $identifie;
	var $motDePasseAChanger;
	var $erreurSession;
	var $tpl;
	
	/**
	 * Le constructeur, crée l'objet et le lie avec la connexion à la base de données
	 * @param object $accesDB L'accès à la base de données
	 *
	 * @return void
	 */
	function __construct($accesDB)
 	{
		session_start();
		$this->idSession = session_id();
		$this->accesDB = $accesDB;
		$this->session = $this->chercheSession();
		$this->identifie = FALSE;
		$this->motDePasseAChanger = TRUE;
		$this->erreurSession = FALSE;
		$this->typeMotDePasse = mdpInconnu;
		if (is_object($this->session)) {
			$reqSQL = "SELECT * FROM ".PrefixeDB."Personne WHERE id_personne='".$this->session->Personne."'";
			$res = $this->accesDB->ExecRequete($reqSQL);
			$this->utilisateur = $this->accesDB->ObjetSuivant($res);
			if (is_object($this->utilisateur))
				$this->identifie = TRUE;
		}
		$this->examenMotDePasseUtilisateur();
		$this->tpl = new Template(tplPath());
		$this->tpl->set_filenames(array('login'=>'formIdentification.tpl','logout'=>'formDecconect.tpl'));
	}
	
	/**
	 * Encode le mot de passe
	 *
	 * @param string $mdp le mot de passe en clair
	 *
	 * @return string le mot de passe crypte ou vide si le $mdp==""
	 */
	function codage($mdp)
	{
		if ($mdp=="") {
			return "";
		}
		$hash = password_hash($mdp, PASSWORD_DEFAULT);
		return $hash;
	}
	
	/**
	 * Examine la nature du mot de passe
	 * 4 Cas :
	 * -- vide,
	 * -- préfixé par prefixeClair (en clair)
	 * -- préfixé par prefixeCripte (crypté)
	 * -- sans préfixe (crypté)
	 * Utilise la valeur du mot de passe de $this->utilisateur.
	 * Va mettre à jour
	 * $this->typeMotDePasse
	 * $this->mdpReel
	 * $this->motDePasseAChanger
	 *
	 * @return void
	 */
	
	function examenMotDePasseUtilisateur() {
		if (!$this->identifie) {
			$this->typeMotDePasse = mdpInconnu;
			return;
		}
		$util = $this->utilisateur;
		$crypte = $util->MotDePasse;
		// Debug
		//error_log("ex MDP Util");
		$this->examenMotDePasse($crypte);
	}
	
	/**
	 * Examine la nature du mot de passe
	 * 4 Cas :
	 * -- vide,
	 * -- préfixé par prefixeClair (en clair)
	 * -- préfixé par prefixeCripte (crypté)
	 * -- sans préfixe (crypté)
	 * Utilise la valeur du mot de passe de $this->utilisateur.
	 * Va mettre à jour
	 * $this->typeMotDePasse
	 * $this->mdpReel
	 * $this->motDePasseAChanger
	 *
	 * @param string $crypte la mot de passe crypte.
	 *
	 * @return void
	 */
	
	function examenMotDePasse($crypte) {
		// Debug
		//error_log("Examen MDP : $crypte");
		if ($crypte=="") {
			$this->typeMotDePasse = mdpVide;
			$this->mdpReel = "";
			$this->motDePasseAChanger = True;
			return;
		}
		$len = strlen(prefixeClair);
		if (substr($crypte, 0, $len)===prefixeClair) {
			$this->typeMotDePasse = mdpClair;
			$this->mdpReel = substr($crypte,$len);
			$this->motDePasseAChanger = True;
			return;
		}
		$len = strlen(prefixeCrypte);
		if (substr($crypte, 0, $len)===prefixeCrypte) {
			$this->typeMotDePasse = mdpCrypte;
			$this->mdpReel=substr($crypte,$len);
			$this->motDePasseAChanger = False;
			return;
		}
		$this->typeMotDePasse = mdpCrypte;
		$this->mdpReel = $crypte;
		$this->motDePasseAChanger = False;
	}
	
	/**
	 * Vérifie le mot de passe.
	 * 4 Cas :
	 * -- vide,
	 * -- préfixé par prefixeClair (en clair)
	 * -- préfixé par prefixeCripte (crypté)
	 * -- sans préfixe (crypté)
	 *
	 * @param string $crypte le mot de passe tiré de la base
	 * @param string $mdp le mot de passe en clair donné
	 *
	 * @return boolean le mot de passe est vérifié 
	 */
	function verifierPasse($crypte, $mdp) {
		// Debug
		//error_log("verifier passe");
		$this->examenMotDePasse($crypte);
		// Debug
		//error_log("Verif Passe : $this->typeMotDePasse");
		switch ($this->typeMotDePasse) {
			case mdpInconnu:
			return False;
			break;
			case mdpVide:
			return $mdp=="";
			case mdpClair:
			return $mdp===$this->mdpReel;
			break;
			case mdpCrypte:
			return password_verify($mdp, $this->mdpReel);
			break;
		}
	}
	
	/**
	 * @deprecated
	 * @see verifierPasse
	 */
	function verifierPasseOLD($crypte, $mdp) {
		//echo "mdp : $mdp<BR>";
		//echo "crypte : $crypte<BR>";
		$this->examenMotDePasse($crypte);
		if ($crypte=="") {
			$this->typeMotDePasse = mdpVide;
			$this->mdpReel = "";
			return $mdp=="";
		}
		$len = strlen(prefixeClair);
		if (substr($crypte, 0, $len)===prefixeClair) {
			$this->typeMotDePasse = mdpClair;
			$this->mdpReel = substr($crypte,$len);
			return $mdp===$this->mdpReel;
		}
		$len = strlen(prefixeCrypte);
		if (substr($crypte, 0, $len)===prefixeCrypte) {
			$this->typeMotDePasse = mdpCrypte;
			$crypte=substr($crypte,$len);
			$this->mdpReel = $crypte;
		}
		//echo "crypte (final) : $crypte<BR>";
		return password_verify($mdp, $crypte);
	}
	
	/**
	 * Nettoye le login
	 * Enlève les caractères non alphanumériques minuscules
	 * @param string $login le login
	 * @return string le login nettoyé
	 */
	function nettoyerLogin($login) {
		$n = preg_match_all("/([a-z][a-z0-9]*)/",$login,$result);
		if ($n==0) return "";
		return $result[1][0];
	}
	
	/**
	 * Accesseur : retourne la session.
	 * Actualise $this->session si nécessaire
	 * @return object la session courante
	 */
	function laSession()
	{
		if (!is_object($this->session))
			$this->session = $this->chercheSession();
		return $this->session;
	}
	
	/**
	 * Accesseur : retourne l'utilisateur en l'actualisant si nécessaire
	 * @return object l'utilisateur
	 */
	function lUtilisateur($actualiser=false)
	{
		if ($actualiser) {
			if (is_object($this->session)) {
				$reqSQL = "SELECT * FROM ".PrefixeDB."Personne WHERE id_personne='".$this->session->Personne."'";
				$res = $this->accesDB->ExecRequete($reqSQL);
				$this->utilisateur = $this->accesDB->ObjetSuivant($res);
				if (is_object($this->utilisateur))
					$this->identifie = TRUE;
			}		
		}
		return $this->utilisateur;
	}
	
	/**
	 * Accesseur : retourne l'erreur de session
	 * @return string l'erreur
	 */
	function lErreurSession() {
		return $this->erreurSession;
	}

	/**
	 * Crée la session.
	 *
	 * Logge la tentative de création
	 * @param string $login le nom d'utilisateur
	 * @param string $motDePasse le mot de passe
	 *
	 * @return boolean la session s'est bien créee.
	 */
	function creerSession($login,$motDePasse)
	{
		if ($login=="") {
			$this->ErreurSession = FALSE;
			return FALSE;
		}
		$utilisateur = Session::chercheUtilisateur($login);

		if (is_object($utilisateur))
		{
			//echo Session::verifierPasse($utilisateur->MotDePasse, $motDePasse)+"<BR>";
			//if ($utilisateur->MotDePasse != "" || password_verify($utilisateur->MotDePasse, $hash))
			//if ($utilisateur->MotDePasse == Session::codage($motDePasse))
			if ($this->verifierPasse($utilisateur->MotDePasse, $motDePasse))
			{
				/*
				$this->motDePasseAChanger = FALSE;
				if ($utilisateur->MotDePasse == "") {
					$this->motDePasseAChanger  = TRUE;
				}
				*/
				// TODO attaquer la BDD pour changer le MDP crypté...
				if ($this->typeMotDePasse==mdpCrypte) {
					$this->motDePasseAChanger = FALSE;
					$crypte = $this->mdpReel;
					if (password_needs_rehash($crypte, PASSWORD_DEFAULT)) {
						$newCrypte = password_hash($motDePasse, PASSWORD_DEFAULT);
						// Debug
						//error_log("changer de mdp : $crypte->$newCrypte");
					}
				} else {
					$this->motDePasseAChanger  = TRUE;
				}
				$maintenant = date("U");
				$fin = $maintenant+3600;

				$reqSQL = "INSERT INTO ".PrefixeDB."Session (id_session,Personne,Fin) VALUES ('$this->idSession',"
							. "'$utilisateur->id_personne','$fin')";
				$resultat = $this->accesDB->ExecRequete($reqSQL);
				$this->utilisateur = $utilisateur;
				$this->log('login');
				$this->identifie = TRUE;
				$this->erreurSession = FALSE;
				return TRUE;
			}
			//echo "<B>Mot de passe incorrect</B><P>\n";
			$this->erreurSession = MotDePasseIncorrect;
			$this->utilisateur = $utilisateur;
			$this->erreurSession = TRUE;
			$this->log('wrongPass');
			return FALSE;
		} else {
			//echo "<B>L'utilisateur $login est inconnu !</B><P>\n";
			$this->erreurSession = LoginIncorrect;
			$this->erreurSession = TRUE;
			$this->log('unknownUser');
			return FALSE;
		}
	}

	/**
	 * Détruit la session en le loggant si nécessaire
	 * @param boolean $destroy Vrai sil a destruction es loggée (True par défaut)
	 */
	function detruireSession($destroy=true)
	{
		session_destroy();

		$session = $this->session;
		$reqSQL = "DELETE FROM ".PrefixeDB."Session WHERE id_session='$session->id_session'";
		$resultat = $this->accesDB->ExecRequete($reqSQL);
		if ($destroy) {
			$this->log('destroy');
		}
	}

	/**
	 * Déconnecte l'utilisateur et détruit la session sans le logger
	 */
	function Deconnexion() {
		$this->log('logout');
		$this->detruireSession(false);
		$this->session = FALSE;
		$this->identifie = FALSE;
	}
	
	/**
	 * Logge dans la base de données. Très vestigial
	 * @param string $why raison de l'entrée log
	 */
	function log($why, $mess="") {
		$utilisateur = $this->utilisateur;
		if (!is_object($utilisateur)) $utilisateur->id_personne=0;
		$reqSQL = "INSERT INTO ".PrefixeDB."Log (Personne,Action) VALUES ('$utilisateur->id_personne','$why')";
		$resultat = $this->accesDB->ExecRequete($reqSQL);			
	}

	/**
	 * Recherche un utilisateur
	 * @param string $login le nom d'utilisateur
	 *
	 * @return void|object l'utilisateur trouvé (void si pas trouvé)
	 */
	function chercheUtilisateur($login)
	{
		$login = Session::nettoyerLogin($login);
		$reqSQL = "SELECT * FROM ".PrefixeDB."Personne WHERE NomDUtilisateur='$login'";
		$resultat = $this->accesDB->ExecRequete($reqSQL);
		$ligne = $this->accesDB->ObjetSuivant($resultat);
		return $ligne;
	}

	/**
	 * Fabrique le formulaire d'dentification
	 * @param string $script la page à appeler lors du POST
	 * @param string $loginDefaut le l@ogin par défaut
	 * @param string $msg le message à afficher
	 *
	 * @return le code HTML du formulaire d'identification.
	 */
	function formulaireIdentification($script, $loginDefaut="",$msg="")
	{
		if ($msg!="") {
			$this->tpl->assign_vars(array('msg'=>"<B>$msg</B>"));
		}
		$this->tpl->assign_vars(array('login'=>$loginDefaut,'script'=>$script,'racineRoot'=>racineRoot));
		return $this->tpl->HTML_for_handle('login');
	}
	
	/**
	 * Fabrique le formulaire de déconnexion
	 * @param string $script la page à appeler lors du POST
	 *
	 * @return le code HTML du formulaire de déconnexion.
	 */
	function formulaireDeconnexion($script) {
		$this->tpl->assign_vars(array('racineRoot'=>racineRoot));
		return $this->tpl->HTML_for_handle('logout');
	}

	/**
	 * Recherche la session dans la base de données à partir de l'id.
	 * Actualise la variable de session
	 * @return object la session
	 */
	function chercheSession()
	{
		$idSession = $this->idSession;
		$reqSQL = "SELECT * FROM ".PrefixeDB."Session WHERE id_session='$idSession'";
		$resultat = $this->accesDB->ExecRequete($reqSQL);
		$ligne = $this->accesDB->ObjetSuivant($resultat);
		$this->session = $ligne;
		return $ligne;
	}

	/**
	 * Détermine l'état de session
	 * @return int le code de type de session
	 */
	function etatSession()
	{
		if (is_object($this->session)) {
			$session = $this->session;
			$maintenant = date("U");
			if ($session->Fin>$maintenant) {
				return VALIDE;
			} else {
				return EXPIREE;
			}
		} else {
			return INVALIDE;
		}
	}

	/**
	 * @deprecated
	 * Utiliser controleAccesCode
	 */
	function controleAcces($script, $acces, $scriptRejet="")
	{
		if ($scriptRejet=="") $scriptRejet=$script;
		$etat = $this->etatSession();
		if ($etat==INVALIDE) {
			echo '<meta http-equiv="Refresh" content="0; url='.racineSite."Login.php?page=$script&alt=$scriptRejet".'">';
			return INVALIDE;
		} else {
			$sess = $this->laSession();
			$util = $this->lUtilisateur();
			if (in_array($util->Type,$acces)) {
				if ($etat==EXPIREE) {
					echo '<meta http-equiv="Refresh" content="0; url='.racineSite."Login.php?page=$script&alt=$scriptRejet".'">';
					return EXPIREE;
				} else {
					return VALIDE;
				}
			} else {
				echo '<meta http-equiv="Refresh" content="0; url='.$scriptRejet.'">';
				return INVALIDE;
			}
		}
		
	}
	
	/**
	 * Vérifie si l'utilisateur peut accéder à la page
	 * @param string $script le script de la page courante dans le cas d'une session invalide/expirée (on repasse par Login.php)
	 * @param int[] $acces tableau d'entiers représentant les catégories qui ont accès )à la page
	 * @param string $scriptRejet le script à appeler au cas ou l'identification rate
	 */
	function controleAccesCode($script, $acces, $scriptRejet="")
	{
		if ($scriptRejet=="") $scriptRejet=$script;
		$etat = $this->etatSession();
		if ($etat==INVALIDE) {
			echo '<meta http-equiv="Refresh" content="0; url='.racineSite."Login.php?page=$script&alt=$scriptRejet".'">';
			return INVALIDE;
		} else {
			$sess = $this->laSession();
			$util = $this->lUtilisateur();
			$ok = FALSE;
			$nature = $util->Nature;
			foreach ($acces as $code) {
				if ($nature & $code) {
					$ok = TRUE;
					break;
				}
			}
			if ($ok) {
				if ($etat==EXPIREE) {
					echo '<meta http-equiv="Refresh" content="0; url='.racineSite."Login.php?page=$script&alt=$scriptRejet".'">';
					return EXPIREE;
				} else {
					return VALIDE;
				}
			} else {
				echo '<meta http-equiv="Refresh" content="0; url='.$scriptRejet.'">';
				return INVALIDE;
			}
		}
		
	}
	
	/**
	 * retourne le texte décrivant la nature de l'utilisateur à partir du code
	 * @param int $code le code décrivant la nature
	 * @return string la chaine décrivant la nature
	 * @todo la mettre statique et gérer le statique interne : FAIT
	 */
	static function natureUtilisateur($code) {
		$nat = "";
		$tNature = Session::tableauNaturesUtilisateur($code);
		$n = count($tNature);
		if ($n==0) {
			return "Néant";
		}
		for ($i=0; $i<$n; $i++) {
			$nat .= $tNature[$i];
			if ($i<$n-1) {
				$nat .= ', ';
			}
		}
		return $nat;
	}
	
	// Code à faire mourir
	function natureUtilisateurOLD($code) {
		$nat = "";
		if ($code==0) {
			return "Néant";
		}
		if ($code & Eleve) { $nat = "Eleve" ;}
		if ($code & Prof) {
			if (strlen($nat)>0) {
				$nat = $nat.", Prof";
			} else {
				$nat = "Prof";
			}
		}
		if ($code & Responsable) {
			if (strlen($nat)>0) {
				$nat = $nat.", Responsable";
			} else {
				$nat = "Responsable";
			}
		}
		if ($code & Admin) {
			if (strlen($nat)>0) {
				$nat = $nat.", Admin";
			} else {
				$nat = "Admin";
			}
		}
		return $nat;
	}
	
	/**
	 * retourne un tableau donnant les rôles l'utilisateur à partir du code
	 * @param int $code le code décrivant la nature
	 * @return string[] le tableau donnant les rôles
	 * @todo la mettre statique : FAIT
	 */
	static function tableauNaturesUtilisateur($code) {
		$nat = array();
		if ($code==0) return $nat;
		if ($code & Eleve) { $nat[]="Eleve";}
		if ($code & Prof) { $nat[]="Prof";}
		if ($code & Responsable) { $nat[]="Responsable";}
		if ($code & Admin) { $nat[]="Admin";}
		if (count($nat)==0) {$nat[]="Inconnu";}
		return $nat;
	}
	
}


restoreRoot();

?>