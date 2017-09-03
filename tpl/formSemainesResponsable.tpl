<h2>Notes</h2>
<div class="indente">
	<form method="post" action="gererResponsable.php?action={action}&quand={quand}" name="formSemaines">
		<div>
			<table style="text-align: left; max-width: 200%;" border="1" cellpadding="2" cellspacing="2">
				<thead>
					<tr class="header">
						<th colspan="3">Filtrer <input name="filtrer" value="Filtrer" type="submit" style="visibility:hidden;"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="odd">
						<td>Les classes</td>
						<td colspan=2>{classe}</td>
					</tr>
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
	<div class="blockTable">
		<!-- BEGIN notes -->
		<div class="innerTable">
			{notes.classe}	
		</div>
		<!-- END notes -->
	</div>	
</div>

