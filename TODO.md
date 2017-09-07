# À Faire

* [x] ~~Gérer proprement le choix de la division/matière pour un responsable~~.
* [x] ~~Achever `gererEleve` (`cette` et `toute`)~~
* [ ] Nettoyer code
* [x] ~~Voir la disposition de `verifierQualiteMotDePasse` : ne serait-il pas mieux de l'intégrer à menu ?~~
* [x] ~~Changer les noms d des intervenants~~.
* [x] ~~Mettre en ligne la cersion castrée du site~~
* [ ] Paginer les formulaires de `admin/gererPersonne` etc.
* [x] ~~Ajouter un fichier `LICENCE.md`. Vraisemblablement GPL 3 ou Apache 2.~~ C'est Apache 2.
* [ ] Dans les template/css modifier le` <div style="padding...` pour un `<div class="...` et une classe afférente dans le css. (Partiel)
* [ ] Systématiser le `<div...` précédent (Partiel)
* [ ] Proprement gérer le debug (fonction+flags...)
* [ ] Explorer un changement de la gestion des semaines : passser de `id_semaine IN (...)` à `Debut>=... AND Fin<=...` Pb potentiel : s'assurer que les semaines soient ordonnées par dates de début/fin
* [ ] Gérer la langue collée. Pour cela il faut changer la table `Groupement` en ajoutant un champ `Langue` et reprendre l'importation des groupes dans `admin/lireCSVGroupes` ou alors utiliser un système de matières fille/mêre : les langues seraient filles de LV...

# Nettoyage

* [x] Chercher/indiquer le code mort
* [x] Commenter le code mort (sauf debug)
* [x] Couper le code mort (sauf debug)

# Documentation

Documenter

* [x] `gererEleve`
* [x] `gererProf`
* [x] `gererResponsable`
* [ ] les fichiers `admin/`
