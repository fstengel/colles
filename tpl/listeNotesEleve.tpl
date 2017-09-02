<table style="text-align: left; max-width: 200%;" border="1" cellpadding="2" cellspacing="2">
	<tbody>
		<tr class='header' style="text-align:center; vertical-align:top;">
	        <th>Mati&egrave;re</th>
			<!-- BEGIN semaine -->
			<th>{semaine.Nom}</th>
			<!-- END semaine -->
		</tr>
		<!-- BEGIN ligne -->
		<tr class='{ligne.eo}'>
			<td>{ligne.Nom}</td>
			<!-- BEGIN note -->
			<td style="width : 3em;">{ligne.note.Valeur}</td>
			<!-- END note -->
		</tr>
		<!-- END ligne -->
	</tbody>
</table>