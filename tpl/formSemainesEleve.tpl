<div >
	<form method="post" action="gererEleve.php?action={action}&quand={quand}" name="formSemaines">
		<div>
			<table style="text-align: left; max-width: 200%;" border="1" cellpadding="2" cellspacing="2">
				<thead>
					<tr class="header">
						<th colspan="3">Filtrer <input name="filtrer" value="Filtrer" type="submit" style="visibility:hidden;"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="even">
						<td>Les Semaines</td>
						<td>Debut</td>
						<td>{debut}</td>
					</tr>
					<tr class ="even">
						<td></td>
						<td>Fin</td>
						<td>{fin}</td>
					</tr>
				</tbody>
			</table>
			
		</div>
	</form>
	<BR>
	<div>
		{listeColles}
	</div>
</div>
