# Colles

Un petit site pour gérer les colles d'une classe de seconde année.

## Pour démarrer

### Prérequis (logiciels)

Par plateforme. Les solutions présentées sont des exemples.

* Mac : MAMP (www.mamp.info)
* Windows : WampServer (www.wampserver.com)
* Linux : ben, normalement c'est livré avec...

### Prérequis (connaissances)

* Savoir utiliser phpMyAdmin pour importer une base.
* Savoir mettre en place un site sous apache.

### installation

1. Démarrer votre serveur apache/mySQL
2. Télécharger le site depuis GitHub (normalement vous y êtes !) sous forme d'archive .zip. C'est le bouton vert (`clone or download`) option `Download ZIP`
3. Décompresser l'archive (a priori `colles-master.zip`) contenant le dossier complet dans le répertoire racine du serveur web (d'habitude `htdocs`)
4. Renommer le dossier de `colles-master` en `colles`
2. importer le fichier `sql/Colles-Structure+Donnees.sql` dans votre base de données


## Limitations

### La date du jour

Le site est en mode test et est figé au 29/09/2015. La date ne changera pas (pour changer ça voir `lib/Constantes.php`). Ceci est du au fait que les fichiers contenant les colloscopes de démonstration sont ceux de l'année 2015-16. On les trouve dans le dossier `uploads`.

### Version de développement

C'est sûrement plein de bugs...

### Documentation

Les fichiers de `lib/` sont généralement documentés. Pour les fichiers principaux, la documentation est plus spartiate quand il y en a...

### Encodage

UTF-8 sans BOM.