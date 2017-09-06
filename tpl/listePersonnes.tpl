<form method="post" action="GererPersonnes.php?action=modifier" name="FormListe">
	<table style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
		<tbody>
			<tr class='header' style="text-align:center; vertical-align:top;">
				<th style="width: 5em; vertical-align:middle; visibility:hidden;">
					<input name="filtrer" value="Filtrer" type="submit" style="visibility:hidden;">
					<input name="status" value="listePersonnes" type="hidden">
					<input name="page" value="{page}" type="hidden">
				</th>
				<th style="width: 5em;">Nom</th>
				<th style="width: 5em;">Pr&eacute;nom</th>
				<th style="width: 4em;">Mail</th>
				<th style="width: 4em;">Login</th>
				<th style="width: 4em;">MDP</th>
				<th style="width: 1em;">E</th>
				<th style="width: 1em;">P</th>
				<th style="width: 1em;">R</th>
				<th style="width: 1em;">A</th>
				<th style="width:5em;  vertical-align:middle; visibility:hidden;"></th>		
			</tr>
			<!-- BEGIN ligne -->	
			<tr  class='{ligne.eo}'>
				<td style="text-align:center;"><input name="modifier[{ligne.nlig}]" value="{ligne.id_personne}" type="checkbox"></td>
				<td><input maxlength="45" size="20" name="Nom[{ligne.nlig}]" value="{ligne.Nom}"></td>
				<td><input maxlength="45" size="20" name="Prenom[{ligne.nlig}]" value="{ligne.Prenom}"></td>
				<td><input maxlength="45" size="20" name="Mail[{ligne.nlig}]" value="{ligne.Mail}"></td>
				<td><input maxlength="45" size="20" name="NomDUtilisateur[{ligne.nlig}]" value="{ligne.NomDUtilisateur}"></td>
				<td><input maxlength="45" size="20" name="MotDePasse[{ligne.nlig}]" value="{ligne.MotDePasse}"></td>
				<td style="text-align:center;"><input name="eleve[{ligne.nlig}]" value="{ligne.eleve}" type="checkbox" {ligne.eleveCheck}></td>
				<td style="text-align:center;"><input name="prof[{ligne.nlig}]" value="{ligne.prof}" type="checkbox" {ligne.profCheck}></td>
				<td style="text-align:center;"><input name="responsable[{ligne.nlig}]" value="{ligne.responsable}" type="checkbox" {ligne.responsableCheck}></td>
				<td style="text-align:center;"><input name="admin[{ligne.nlig}]" value="{ligne.admin}" type="checkbox" {ligne.adminCheck}></td>
				<td style="text-align:center;"><input name="supprimer[{ligne.nlig}]" value="{ligne.id_crenau}" type="checkbox"></td>
			</tr>
			<!-- END ligne -->
		</tbody>
	</table>
	<table style="width:100%;">
		<tbody>
			<tr class='empty' style="text-align:center; vertical-align:middle;">
				<th style="width: 4em; vertical-align:middle; height:2em; text-align:left;"><input name="valid" value="Modifier" type="submit"></th>
				<th style="font-weight:normal; text-align:left;"> les personnes s&eacute;lectionn&eacute;es</th>
				<th style="font-weight:normal; text-align:right;">Pour supprimer : cocher des deux c&ocirc;t&eacute;s</th>
				<th style="width: 5em; vertical-align:middle; height:2em;  text-align:right;"><input name="valid" value="Supprimer" type="submit"></th>
			</tr>
			<tr class='empty' style="text-align:center; vertical-align:middle; visibility:hidden;">
				<th style="width: 4em; vertical-align:middle; height:2em; text-align:left;"><input name="precedent" value="Pr&eacute;c&eacute;dente" type="submit"></th>
				<th style="font-weight:normal; text-align:center;" colspan="2"> Personnes de {premiere} &agrave; {derniere} sur {nbPersonnes}</th>
				<th style="width: 5em; vertical-align:middle; height:2em;  text-align:right;"><input name="suivant" value="Suivante" type="submit"></th>
			</tr>
		</tbody>
	</table>
</form>
<hr>
<form method="post" action="GererPersonnes.php?action=ajout" name="FormAjout">
	<table style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
		<tbody>
			<tr class='header' style="text-align:center; vertical-align:top;">
				<th style="width: 5em; vertical-align:middle; visibility:hidden;"><input name="filtrer" value="Filtrer" type="submit" style="visibility:hidden;"><input name="status" value="listePersonnes" type="hidden"></th>
				<th style="width: 5em;">Nom</th>
				<th style="width: 5em;">Pr&eacute;nom</th>
				<th style="width: 4em;">Mail</th>
				<th style="width: 4em;">Login</th>
				<th style="width: 4em;">MDP</th>
				<th style="width: 1em;">E</th>
				<th style="width: 1em;">P</th>
				<th style="width: 1em;">R</th>
				<th style="width: 1em;">A</th>
				<th style="width:5em;  vertical-align:middle; visibility:hidden;"></th>		
			</tr>
			<!-- BEGIN ajout -->	
			<tr  class='{ajout.eo}'>
				<td style="text-align:center;"><input name="ajouter[{ajout.nlig}]" type="checkbox"></td>
				<td><input maxlength="45" size="20" name="Nom[{ajout.nlig}]"></td>
				<td><input maxlength="45" size="20" name="Prenom[{ajout.nlig}]"></td>
				<td><input maxlength="45" size="20" name="Mail[{ajout.nlig}]"></td>
				<td><input maxlength="45" size="20" name="NomDUtilisateur[{ajout.nlig}]"></td>
				<td><input maxlength="45" size="20" name="MotDePasse[{ajout.nlig}]"></td>
				<td style="text-align:center;"><input name="eleve[{ajout.nlig}]" type="checkbox"></td>
				<td style="text-align:center;"><input name="prof[{ajout.nlig}]" type="checkbox"></td>
				<td style="text-align:center;"><input name="responsable[{ajout.nlig}]" type="checkbox"></td>
				<td style="text-align:center;"><input name="admin[{ajout.nlig}]" type="checkbox"></td>
				<td style="text-align:center;"></td>
			</tr>
			<!-- END ajout -->
		</tbody>
	</table>
	<table style="width:100%;">
		<tbody>
			<tr class='empty' style="text-align:center; vertical-align:middle;">
				<th style="width: 4em; vertical-align:middle; height:2em;"><input name="valid" value="Ajouter" type="submit"></th>
				<th style="font-weight:normal; text-align:left;"> les lignes s&eacute;lectionn&eacute;es</th>
			</tr>
		</tbody>
	</table>
</form>
