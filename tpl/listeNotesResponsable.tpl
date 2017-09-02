<table style="text-align: left; max-width: 200%;" border="1" cellpadding="2" cellspacing="2">
	<tbody>
		<tr class='header'>
			<td colspan="3">Classe : {classe}</td>
		</tr>
		<tr class='header' style="text-align:center; vertical-align:top;">
	        <th>Nom</th>
	        <th>Pr&eacute;nom</th>
			<th>Groupe</th>
			<!-- BEGIN semaine -->
			<th>{semaine.Nom}</th>
			<!-- END semaine -->
		</tr>
		<!-- BEGIN ligne -->
		<tr class='{ligne.eo}'>
			<td>{ligne.Nom}</td>
			<td>{ligne.Prenom}</td>
			<td>{ligne.Groupe}</td>
			<!-- BEGIN note -->
			<td style="width : 3em;">{ligne.note.Valeur}</td>
			<!-- END note -->
		</tr>
		<!-- END ligne -->
	</tbody>
</table>