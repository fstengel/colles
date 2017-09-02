<form method="post" action="Profil.php?action=modifier" name="profil">
	<fieldset>
		<legend>Vous</legend>
		<table style="text-align: left;" border="1" cellpadding="2" cellspacing="2">
			<tbody>
				<tr class='odd'>
					<td style="width:8em;" >Nom</td>
					<td style="width:50em;">{nom}</td>
				</tr>
				<tr class='even'>
					<td>Pr&eacute;nom</td>
					<td>{prenom}</td>
				</tr>
				<tr class='odd'>
					<td>Type</td>
					<td>{type}.&nbsp;{lm}</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<fieldset>
		<legend>Mail</legend><br>
		{msgMail}
		<table frame="void" rules="none">
			<tbody>
				<tr style="vertical-align:bottom;">
					<td>
						<table style="text-align: left;" border="1" cellpadding="2" cellspacing="2">
							<tbody>
								<tr class='odd'>
									<td  style="width:15em;">Votre mail</td>
									<td><input value="{mail}" maxlength="50" size="50" name="mail"></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td><input name="chMail" value="Changer le Mail" type="submit"></td>
				</tr>
			</tbody>
		</table>

	</fieldset>
	<fieldset>
		<legend>Mot de passe</legend><br>
		{msgPass}
		<table frame="void" rules="none">
			<tr style="vertical-align:bottom;">
				<td>
					<table style="text-align: left;" border="1" cellpadding="2" cellspacing="2">
						<tbody>
							<tr class='odd'>
								<td style="width:15em;">Mot de passe Actuel</td>
								<td><input maxlength="50" size="50" name="pass0" type="password"></td>
							</tr>
							<tr class='even'>
								<td style="width:15em;">Nouveau mot de passe</td>
								<td><input maxlength="50" size="50" name="pass1" type="password"></td>
							</tr>
							<tr class='odd'>
								<td>Confirmez le mot de passe</td>
								<td><input maxlength="50" size="50" name="pass2" type="password"></td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<input name="chPass" value="Changer le Mot de Passe" type="submit">
				</td>
			</tr>
		</table>
	</fieldset>
</form>

