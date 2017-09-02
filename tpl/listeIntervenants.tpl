<form method="post" action="GererIntervenants.php?action=modifier" name="FormListe">
	<table style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
		<tbody>
			<tr class='header' style="text-align:center; vertical-align:top;">
				<th style="width: 5em; vertical-align:middle; visibility:hidden;"><input name="filtrer" value="Filtrer" type="submit" style="visibility:hidden;"><input name="status" value="listePersonnes" type="hidden"></th>
				<th style="width: 10em;">Nom</th>
				<th style="width: 5em;">Matiere</th>
				<th style="width: 5em;  vertical-align:middle; visibility:hidden;"></th>		
			</tr>
			<!-- BEGIN ligne -->	
			<tr  class='{ligne.eo}'>
				<td style="text-align:center;"><input name="modifier[{ligne.nlig}]" value="{ligne.id_intervenant}" type="checkbox"></td>
				<td>
					<select name="Personne[{ligne.nlig}]">{ligne.popupPersonne}</select>
				</td>
				<td>
					<select name="Matiere[{ligne.nlig}]">{ligne.popupMatiere}</select>
					</td>
				<td style="text-align:center;"><input name="supprimer[{ligne.nlig}]" value="{ligne.id_crenau}" type="checkbox"></td>
			</tr>
			<!-- END ligne -->
		</tbody>
	</table>
	<table style="width:100%;">
		<tbody>
			<tr class='empty' style="text-align:center; vertical-align:middle;">
				<th style="width: 4em; vertical-align:middle; height:2em;"><input name="valid" value="Modifier" type="submit"></th>
				<th style="font-weight:normal; text-align:left;"> les intervenants s&eacute;lectionn&eacute;es</th>
				<th style="font-weight:normal; text-align:right;">Pour supprimer : cocher des deux c&ocirc;t&eacute;s</th>
				<th style="width: 5em; vertical-align:middle; height:2em;"><input name="valid" value="Supprimer" type="submit"></th>
			</tr>
		</tbody>
	</table>
</form>
<hr>
<form method="post" action="GererIntervenants.php?action=ajout" name="FormAjout">
	<table style="text-align: left; width: 100%;" border="1" cellpadding="2" cellspacing="2">
		<tbody>
			<tr class='header' style="text-align:center; vertical-align:top;">
				<th style="width: 5em; vertical-align:middle; visibility:hidden;"><input name="filtrer" value="Filtrer" type="submit" style="visibility:hidden;"><input name="status" value="listePersonnes" type="hidden"></th>
				<th style="width: 10em;">Nom</th>
				<th style="width: 5em;">Matiere</th>
				<th style="width:5em;  vertical-align:middle; visibility:hidden;"></th>		
			</tr>
			<!-- BEGIN ajout -->	
			<tr  class='{ajout.eo}'>
				<td style="text-align:center;"><input name="ajouter[{ajout.nlig}]" type="checkbox"></td>
				<td>
					<select name="Personne[{ajout.nlig}]">{ajout.popupPersonne}</select>
				</td>
				<td>
					<select name="Matiere[{ajout.nlig}]">{ajout.popupMatiere}</select>
					</td>
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
