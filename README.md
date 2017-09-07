# Colles

Un petit site pour gérer les colles d'une classe de seconde année.

## Pour démarrer

### Pour les impatients

Et ceux qui n'ont pas forcément envie de tout installer.

* Télécharger le source : voir le point 2. de la section [installation](#installation).
* Jouer : une version un peu moins à jour que le code de ce dépôt est disponible à l'adresse [colles.stengel.fr](http://colles.stengel.fr). Vous disposez de tous les utilisateurs sauf `fstengel` qui est inaccessible. Voir [ce paragraphe](#pour-jouer-avec) pour quelques noms d'utilisateurs.

### Prérequis (logiciels)

Il est nécessaire de disposer d'une solution donnant

1. Un serveur web
2. Un interpréteur PHP
3. Un moteur de bases de données MySQL

Généralement un paquet complet combine ces trois groupes de logiciels. Par plateforme et par exemple nous avons : 

* Mac : MAMP ([www.mamp.info](http://www.mamp.info))
* Windows : WampServer ([www.wampserver.com](http://www.wampserver.com))
* Linux : ben, normalement c'est livré avec et si travaillez avec un pingouin vous devriez savoir comment faire :stuck_out_tongue_winking_eye:...

### Prérequis (connaissances)

* Savoir utiliser phpMyAdmin pour importer une base.
* Savoir mettre en place un site sous apache (ou autre serveur web).
* Savoir jouer avec GitHub.

### Installation

1. Démarrer votre serveur apache/mySQL
2. Télécharger le site depuis GitHub (normalement vous y êtes !) sous forme d'archive .zip. C'est le bouton vert (`clone or download`) en haut de cette page. Choisir l'option `Download ZIP`
3. Décompresser l'archive (a priori `colles-master.zip`) contenant le dossier complet dans le répertoire racine du serveur web (`htdocs` chez apache)
4. Renommer le dossier de `colles-master` en `colles`
2. importer le fichier `sql/Colles-Structure+Donnees.sql` dans votre base de données

### Pour jouer avec

La page d'acceuil sera à l'URL `votreServeur/colles/ `où *`votreServeur`* est bien sûr l'adresse de votre serveur web personnel. Chez moi c'est `http://localhost:8888`

Essayez avec 4 utilisateurs *sans mot de passe* (!) :

* `fstengel` qui est admin, responsable et prof (cet utilisateur n'est pas dispnible sur la version [colles.stengel.fr](http://colles.stengel.fr)) . Attention, importer des CSV risque de détruire les données déjà présentes. Ceci dit : on ré-importe la BDD et hop !
* `pgarnier` qui est prof
* `imoncerf` qui est responsable et prof
* `jelgato` qui est élève

Avec un peu de nez on peut trouver d'autres utilisateurs avec leur rôle en utilisant l'admin pour regarder les personnes.

## Limitations

### La date du jour

Le site est en mode test et est figé au 29/09/2015. La date ne changera pas (pour changer ça voir `lib/Constantes.php`). Ceci est du au fait que les fichiers contenant les colloscopes de démonstration sont ceux de l'année 2015-16. On les trouve dans le dossier `uploads`.

### Version de développement

C'est sûrement plein de bugs...

### Documentation

Les fichiers de `lib/` sont généralement documentés. Pour les fichiers principaux, la documentation est plus spartiate quand il y en a...

### Encodage

UTF-8 sans BOM.

### Licence

GPL 3 ou compatibles.
