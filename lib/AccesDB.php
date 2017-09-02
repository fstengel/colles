<?php
//require("Chemins.php");
require_once("Connect.php");

/**
 * Classe gérant l'accès à la base de donnée
 * 
 * @author Frank STENGEL
 * @version 1.0 2016-11-23
 * 
 * @todo Passer à mysqli. VITAL !
 * @todo Créer une méthode pour les requètes préparées
 *
 */
class AccesDB {
	/**
	 * @property object $connection La connexion ouverte
	 * @property string $erreurSQL L'erreur SQL si la requète précédente à échoué. "" si pas d'erreur.
	 */
	var $connection;
	var $erreurSQL;
	
	/**
	 * Initialise l'objet en tentant une connexion à la base.
	 *
	 * Initialise l'objet en remplissant la variable $connection. En cas d'erreur, meurt à l'aide d'un die.
	 *	 *
	 * @return void
	 */
	function __construct() {
		//$connection = mysql_connect (HoteDB, UtilisateurDB, MotDePasseDB);
		$connection = new mysqli(HoteDB, UtilisateurDB, MotDePasseDB, DB);
		
		if ($connection->connect_error) {
				die("D&eacute;sol&eacute;, connexion au serveur ".HoteDB." impossible [".$connection->connect_error."]\n");
		}
		
		$this->connection = $connection;
		
		//if (!mysql_select_db(DB,$connection)) {
		//	die("D&eacute;sol&eacute;, acc&egrave;s &agrave; la base ".DB." impossible\n".
		//	"<b>Message de MySQL :</b> " . mysql_error($connection));
		//}
	}
	
	/**
	 * Accesseur pour la propriété $connection.
	 *
	 * @return object la connexion
	 */
	function laConnection() {
		return $this->connection;
	}
	
	/**
	 * Exectution d'une requête.
	 *
	 * @param string $requete la chaîne de requête. Celle-ci doit être simple et ne doit pas se terminer par ;.
	 *
	 * @return void|ressource la ressource $resultat (ou true) si la requête a réussi sinon void. 
	 */
	function ExecRequete ($requete)
 	{
		$connection = $this->connection;
		//print_r($connection); echo "<BR>";
  		$resultat = $connection->query($requete);
		//print_r($resultat); echo "<BR>";


  		if ($resultat) {
			$erreurSQL="";
			return $resultat;
		}
  		else {
			if (debug) {
    			die ("<b>Erreur dans l'ex&eacute;cution de la requ&ecirc;te '$requete'.</b><br/>".
    				"<b>Message de MySQL :</b> " .  $connection->error);
			} else {
				$erreurSQL = $connection->error;
				return;
			}
			
  		}  
 	}

 	/**
 	 * Recherche de l'objet suivant. Utile ?
	 *
	 * @param ressource $resultat la ressource correspondant au résultat de la requête.
	 *
	 * @return void|object le prochain résultat. void si plus/pas de résultat.
 	 */
 	function ObjetSuivant ($resultat)
 	{
   		return  $resultat->fetch_object();
   		//return  mysql_fetch_object ($resultat);
 	} 

 	/**
 	 * Recherche de la ligne suivante (retourne un tableau)
	 *
	 * @param ressource $resultat la ressource correspondant au résultat de la requête.
	 *
	 * @return void|array le prochain résultat sous forme de tableau. void si plus/pas de résultat.
 	 */
 	function LigneSuivante ($resultat)
 	{
   		return  $resultat->fetch_assoc();
 	}
	
	/**
	 * Récupère tous les résultats en un tableau
	 *
	 * @param ressource $resultat la ressource correspondant au résultat de la requête.
	 *
	 * @return void|array les résultats sous forme de tableau de tableaux associatifs. void si pas de résultat.
 	 */
 	function ToutesLesLignes ($resultat)
 	{
   		return  $resultat->fetch_all(MYSQLI_ASSOC);
 	}
}
?>