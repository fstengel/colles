# À Faire

* [x] ~~Gérer proprement le choix de la division/matière pour un responsable~~.
* [x] ~~Achever `gererEleve` (`cette` et `toute`)~~
* [ ] Nettoyer code
* [x] ~~Voir la disposition de `verifierQualiteMotDePasse` : ne serait-il pas mieux de l'intégrer à menu ?~~
* [ ] Paginer les formulaires de `admin/gererPersonne` etc.
* [ ] Dans les template/css modifier le` <div style="padding...` pour un `<div class="...` et une classe afférente dans le css. (Partiel)
* [ ] Systématiser le `<div...` précédent (Partiel)
* [ ] Proprement gérer le debug (fonction+flags...)
* [ ] Explorer un changement de la gestion des semaines : passser de `id_semaine IN (...)` à `Debut>=... AND Fin<=...` Pb potentiel : s'assurer que les semaines soient ordonnées par dates de début/fin
* [ ] Gérer la langue collée. Pour cela il faut changer la table `Groupement` en ajoutant un champ `Langue` et reprendre l'importation des groupes dans `admin/lireCSVGroupes` 

# Nettoyage

* [x] Chercher/indiquer le code mort
* [ ] Commenter le code mort
* [ ] Couper le code mort

# Documentation

Documenter

* [x] `gererEleve`
* [x] `gererProf`
* [x] `gererResponsable`
* [ ] les fichiers `admin/`
