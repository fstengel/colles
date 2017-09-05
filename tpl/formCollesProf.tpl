<form method="post" action="gererProf.php?action=modifier&quand={quand}" name="formColles">
	A, Abs, ABS : élève absent ; N, Nn, NN : élève non noté ; case vide : pas de note attribuée.<BR>
	<table style="text-align: left; max-width: 200%;" border="1" cellpadding="2" cellspacing="2">
		<tbody>
			<tr class='header' style="text-align:center; vertical-align:top;">
		        <th style="width: 5em;">Semaine</th>
		        <th style="width: 5em;">Jour</th>
				<th style="width: 4em;">D&eacute;but</th>
				<th style="width: 4em;">Fin</th>
				<th style="width: 4em;">Lieu</th>
				<th style="width: 4em;">Classe</th>
				<th style="min-width:4em;">Groupe</th>
			</tr>
			<!-- BEGIN ligne -->	
			<tr class='{ligne.eo}' style="vertical-align:middle;{ligne.styleTr}">
		        <td >{ligne.nSemaine}</td>
		        <td>{ligne.nJour}</td>
				<td>{ligne.Debut}</td>
				<td>{ligne.Fin}</td>
				<td>{ligne.Lieu}</td>
				<td>{ligne.Classe}</td>
				<td>
					<table border="1" frame="void" cellpadding="0" cellspacing="0">
						<tr>
							
							<td rowspan="2">{ligne.Groupe}</td>
							<!-- BEGIN eleve -->
							<td><label>{ligne.eleve.nom}</label></td>
							<!-- END eleve -->
						</tr>
						<tr>
							<!-- BEGIN note -->
							<td>
								<input type="hidden" name="groupement[{ligne.note.numero}]" value="{ligne.note.groupement}">
								<input type="hidden" name="idNote[{ligne.note.numero}]" value="{ligne.note.idNote}">
								<input type="hidden" name="idColloscope[{ligne.note.numero}]" value="{ligne.note.idColloscope}">
								<input type="hidden" name="idColle[{ligne.note.numero}]" value="{ligne.note.idColle}">
								<input type="hidden" name="origNote[{ligne.note.numero}]" value="{ligne.note.origValeur}">
								<input type="text" name="champNote[{ligne.note.numero}]" value="{ligne.note.valeur}">
							</td>
							<!-- END note -->
						</tr>
					</table>
				</td>
			</tr>
			<!-- END ligne -->
		</tbody>
	</table>
	<input type='submit' name="valid" value="Continuer" size='0' maxlength='0' >
	<input type='submit' name='cancel' value='Abandon'>
</form>
