-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:8889
-- Généré le :  Sam 02 Septembre 2017 à 15:12
-- Version du serveur :  5.6.35
-- Version de PHP :  7.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `mydb`
--
CREATE DATABASE IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `mydb`;


-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `colles_classe`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `colles_classe`;
CREATE TABLE IF NOT EXISTS `colles_classe` (
`Eleve` int(11) unsigned
,`Nom` varchar(45)
,`Prenom` varchar(45)
,`Groupe` varchar(2)
,`Groupement` int(11) unsigned
,`idColloscope` int(11) unsigned
);

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Colle`
--

DROP TABLE IF EXISTS `Colles_Colle`;
CREATE TABLE IF NOT EXISTS `Colles_Colle` (
  `id_colle` int(11) UNSIGNED NOT NULL,
  `idColloscope` int(11) UNSIGNED NOT NULL,
  `Semaine` int(11) UNSIGNED NOT NULL,
  `Crenau` int(11) UNSIGNED NOT NULL,
  `Groupe` varchar(2) COLLATE utf8_bin NOT NULL,
  `Ligne` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `Colonne` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_colle`),
  KEY `fk_Colle_Crenau_idx` (`Crenau`),
  KEY `fk_Colle_Semaine_idx` (`Semaine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Crenau`
--

DROP TABLE IF EXISTS `Colles_Crenau`;
CREATE TABLE IF NOT EXISTS `Colles_Crenau` (
  `id_crenau` int(11) UNSIGNED NOT NULL,
  `idColloscope` int(11) UNSIGNED NOT NULL,
  `Jour` smallint(5) NOT NULL,
  `Debut` time NOT NULL,
  `Fin` time NOT NULL,
  `Lieu` varchar(45) COLLATE utf8_bin NOT NULL,
  `Intervenant` int(11) UNSIGNED NOT NULL,
  `Ligne` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_crenau`),
  KEY `fk_Crenau_Intervenant_idx` (`Intervenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `colles_crenaucomplet`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `colles_crenaucomplet`;
CREATE TABLE IF NOT EXISTS `colles_crenaucomplet` (
`id_crenau` int(11) unsigned
,`id_intervenant` int(11) unsigned
,`Jour` smallint(5)
,`Debut` time
,`Fin` time
,`Lieu` varchar(45)
,`idColloscope` int(11) unsigned
,`Matiere` int(11) unsigned
);

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Division`
--

DROP TABLE IF EXISTS `Colles_Division`;
CREATE TABLE IF NOT EXISTS `Colles_Division` (
  `id_division` int(11) UNSIGNED NOT NULL,
  `nom` varchar(10) NOT NULL,
  `id_parent` int(11) UNSIGNED DEFAULT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_division`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Groupement`
--

DROP TABLE IF EXISTS `Colles_Groupement`;
CREATE TABLE IF NOT EXISTS `Colles_Groupement` (
  `id_groupement` int(11) UNSIGNED NOT NULL,
  `idColloscope` int(11) UNSIGNED NOT NULL,
  `Groupe` varchar(2) CHARACTER SET utf8 NOT NULL,
  `Eleve` int(11) UNSIGNED NOT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_groupement`),
  KEY `fk_Groupement_Eleve_idx` (`Eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_IdColloscope`
--

DROP TABLE IF EXISTS `Colles_IdColloscope`;
CREATE TABLE IF NOT EXISTS `Colles_IdColloscope` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `division` int(11) UNSIGNED NOT NULL,
  `annee` date NOT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_anneeDivision` (`annee`,`division`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Intervenant`
--

DROP TABLE IF EXISTS `Colles_Intervenant`;
CREATE TABLE IF NOT EXISTS `Colles_Intervenant` (
  `id_intervenant` int(11) UNSIGNED NOT NULL,
  `Personne` int(11) UNSIGNED NOT NULL,
  `Matiere` int(11) UNSIGNED NOT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_intervenant`),
  KEY `fk_Intervenant_Personne_idx` (`Personne`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Log`
--

DROP TABLE IF EXISTS `Colles_Log`;
CREATE TABLE IF NOT EXISTS `Colles_Log` (
  `id_log` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Personne` smallint(5) UNSIGNED NOT NULL,
  `Instant` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Action` enum('login','logout','destroy','clean','wrongPass','unknownUser','error_1') NOT NULL,
  PRIMARY KEY (`id_log`),
  KEY `Instant` (`Instant`)
) ENGINE=MyISAM AUTO_INCREMENT=13270 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Matiere`
--

DROP TABLE IF EXISTS `Colles_Matiere`;
CREATE TABLE IF NOT EXISTS `Colles_Matiere` (
  `id_matiere` int(11) UNSIGNED NOT NULL,
  `Nom` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_matiere`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Note`
--

DROP TABLE IF EXISTS `Colles_Note`;
CREATE TABLE IF NOT EXISTS `Colles_Note` (
  `id_note` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idColloscope` int(11) UNSIGNED NOT NULL,
  `Valeur` decimal(3,1) DEFAULT NULL,
  `Colle` int(11) UNSIGNED NOT NULL,
  `Groupement` int(11) UNSIGNED NOT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_note`),
  KEY `fk_Note_Colle_idx` (`Colle`),
  KEY `fk_Note_Groupement_idx` (`Groupement`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Personne`
--

DROP TABLE IF EXISTS `Colles_Personne`;
CREATE TABLE IF NOT EXISTS `Colles_Personne` (
  `id_personne` int(11) UNSIGNED NOT NULL,
  `Nom` varchar(45) NOT NULL,
  `Prenom` varchar(45) CHARACTER SET utf8 NOT NULL,
  `Civilite` enum('M.','Mme.','Mlle.') DEFAULT NULL,
  `Mail` varchar(45) NOT NULL,
  `NomDUtilisateur` varchar(45) NOT NULL,
  `MotDePasse` varchar(255) DEFAULT NULL,
  `Type-old` enum('eleve','prof','admin') NOT NULL,
  `Nature` bigint(20) NOT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_personne`,`NomDUtilisateur`),
  KEY `idx_personne_nom` (`Nom`),
  KEY `idx_personne_nomUtilsateur` (`NomDUtilisateur`),
  KEY `idx_nomPrenom` (`Nom`,`Prenom`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Responsable`
--

DROP TABLE IF EXISTS `Colles_Responsable`;
CREATE TABLE IF NOT EXISTS `Colles_Responsable` (
  `id_responsable` int(11) UNSIGNED NOT NULL,
  `Division` int(11) NOT NULL,
  `Personne` int(11) UNSIGNED NOT NULL,
  `Matiere` int(11) UNSIGNED NOT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_responsable`),
  KEY `fk_Responsable_Personne_idx` (`Personne`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Semaine`
--

DROP TABLE IF EXISTS `Colles_Semaine`;
CREATE TABLE IF NOT EXISTS `Colles_Semaine` (
  `id_semaine` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Annee` date NOT NULL,
  `Nom` varchar(10) CHARACTER SET utf8 NOT NULL,
  `Debut` date NOT NULL,
  `Fin` date NOT NULL,
  `Colonne` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `DerniereModification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_semaine`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `Colles_Session`
--

DROP TABLE IF EXISTS `Colles_Session`;
CREATE TABLE IF NOT EXISTS `Colles_Session` (
  `id_session` varchar(40) NOT NULL,
  `Personne` int(11) UNSIGNED NOT NULL,
  `Fin` decimal(10,0) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_session`,`Personne`),
  KEY `fk_Colles_Session_Personne_idx` (`Personne`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la vue `colles_classe`
--
DROP TABLE IF EXISTS `colles_classe`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `colles_classe`  AS  (select `G`.`Eleve` AS `Eleve`,`P`.`Nom` AS `Nom`,`P`.`Prenom` AS `Prenom`,`G`.`Groupe` AS `Groupe`,`G`.`id_groupement` AS `Groupement`,`G`.`idColloscope` AS `idColloscope` from (`colles_personne` `P` join `colles_groupement` `G` on((`P`.`id_personne` = `G`.`Eleve`)))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `colles_crenaucomplet`
--
DROP TABLE IF EXISTS `colles_crenaucomplet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `colles_crenaucomplet`  AS  select `C`.`id_crenau` AS `id_crenau`,`I`.`id_intervenant` AS `id_intervenant`,`C`.`Jour` AS `Jour`,`C`.`Debut` AS `Debut`,`C`.`Fin` AS `Fin`,`C`.`Lieu` AS `Lieu`,`C`.`idColloscope` AS `idColloscope`,`I`.`Matiere` AS `Matiere` from (`colles_crenau` `C` join `colles_intervenant` `I` on((`C`.`Intervenant` = `I`.`id_intervenant`))) ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `Colles_Colle`
--
ALTER TABLE `Colles_Colle`
  ADD CONSTRAINT `fk_Colle_Crenau` FOREIGN KEY (`Crenau`) REFERENCES `Colles_Crenau` (`id_crenau`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Colle_Semaine` FOREIGN KEY (`Semaine`) REFERENCES `Colles_Semaine` (`id_semaine`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `Colles_Crenau`
--
ALTER TABLE `Colles_Crenau`
  ADD CONSTRAINT `fk_Colles_Crenau_Intervenant` FOREIGN KEY (`Intervenant`) REFERENCES `Colles_Intervenant` (`id_intervenant`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `Colles_Groupement`
--
ALTER TABLE `Colles_Groupement`
  ADD CONSTRAINT `fk_Groupement_Eleve` FOREIGN KEY (`Eleve`) REFERENCES `Colles_Personne` (`id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `Colles_Intervenant`
--
ALTER TABLE `Colles_Intervenant`
  ADD CONSTRAINT `fk_Colles_Intervenant_Personne` FOREIGN KEY (`Personne`) REFERENCES `Colles_Personne` (`id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `Colles_Note`
--
ALTER TABLE `Colles_Note`
  ADD CONSTRAINT `fk_Note_Colle` FOREIGN KEY (`Colle`) REFERENCES `Colles_Colle` (`id_colle`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Note_Groupement` FOREIGN KEY (`Groupement`) REFERENCES `Colles_Groupement` (`id_groupement`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `Colles_Responsable`
--
ALTER TABLE `Colles_Responsable`
  ADD CONSTRAINT `fk_Responsable_Personne` FOREIGN KEY (`Personne`) REFERENCES `Colles_Personne` (`id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `Colles_Session`
--
ALTER TABLE `Colles_Session`
  ADD CONSTRAINT `fk_Colles_Session_Personne` FOREIGN KEY (`Personne`) REFERENCES `Colles_Personne` (`id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;
