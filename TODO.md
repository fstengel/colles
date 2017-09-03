# À Faire

* [ ] Explorer un changement de la gestion des semaines : passser de `id_semaine IN (...)` à `Debut>=... AND Fin<=...` Pb potentiel : s'assurer que les semaines soient ordonnées par dates de début/fin
* [x] ~~Achever `gererEleve` (`cette` et `toute`)~~
* [ ] Nettoyer code
* [ ] Paginer les formulaires de `admin/gererPersonne` etc.
* [p] Dans les template/css modifier le` <div style="padding...` pour un `<div class="...` et un classe afférente dans le css.
* [p] Systématiser le `<div...` précédent
* [ ] Proprement gérer le debug (fonction+flags...)
* [ ] Gérer la langue collée. Pour cela il faut changer la table `Groupement` en ajoutant un champ `Langue` et reprendre l'importation des groupes dans `admin/lireCSVGroupes` 

# Nettoyage

* [ ] Chercher/indiquer le code mort
* [ ] Commenter le code mort
* [ ] Couper le code mort

# Documentation

Documenter

* `gererEleve`
* `gererProf`
* `gererResponsable`
* les fichiers `admin/`
