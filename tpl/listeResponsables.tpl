<form method="post" action="GererResponsables.php?action=modifier" name="FormListe">
	<table style="text-align: left; min-width: 500px;" border="1" cellpadding="2" cellspacing="2">
		<tbody>
			<tr class='header' style="text-align:center; vertical-align:top;">
				<th style="width: 5em; vertical-align:middle; visibility:hidden;"><input name="filtrer" value="Filtrer" type="submit" style="visibility:hidden;"><input name="status" value="listePersonnes" type="hidden"></th>
				<th style="width: 5em;">Division</th>
				<th style="width: 5em;">Mati&egrave;re</th>
				<th style="width: 10em;">Nom</th>
			</tr>
			<!-- BEGIN ligne -->	
			<tr  class='{ligne.eo}'>
				<td style="text-align:center;"><input name="modifier[{ligne.nlig}]" value="{ligne.id_matiere}" type="checkbox"></td>
				<td> 
					{ligne.NomDivision}<input type="hidden" name="Division[{ligne.nlig}]" value="{ligne.id_division}">
				</td>
				<td>
					{ligne.Matiere}<input type="hidden" name="Responsable[{ligne.nlig}]" value="{ligne.id_responsable}">
				</td>
				<td>
					<select name="Personne[{ligne.nlig}]">{ligne.popupPersonne}}</select>
					</td>
			</tr>
			<!-- END ligne -->
		</tbody>
	</table>
	<table style="width:100%;">
		<tbody>
			<tr class='empty' style="text-align:center; vertical-align:middle;">
				<th style="width: 4em; vertical-align:middle; height:2em;"><input name="valid" value="Modifier" type="submit"></th>
				<th style="font-weight:normal; text-align:left;"> les responsables s&eacute;lectionn&eacute;es</th>
			</tr>
		</tbody>
	</table>
</form>
<hr>
