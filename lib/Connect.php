<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

//defined in Constantes.php
//define("local",true);
//define("local",false);


if (local) {
	define("HoteDB","localhost:8889");
	define("UtilisateurDB","root");
	define("MotDePasseDB","root");
	define("DB","mydb");
	//define("DB","stengelmysql");
	define("PrefixeDB","Colles_");
} else {
	/*
		Serveur        : mysql5-11.start
		Utilisateur    : stengelmysql
		Nom de la base : stengelmysql
		Mot de passe   : LqImlCZs
	*/
	define("HoteDB","mysql5-11.start");
	define("UtilisateurDB","stengelmysql");
	define("MotDePasseDB","LqImlCZs");
	define("DB","stengelmysql");
	define("PrefixeDB","Colles_");	
}
?>