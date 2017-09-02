<form  method='POST' action='lireCSVGroupes.php?action=verifier' name='Form'>
	<fieldset>
		<legend>Informations fichier</legend>
		Classe : <input type='text' name='classe' id='classe' value='{classe}' readonly>
		Ann&eacute;e : <input type='text' name='annee' id='annee' value='{annee}' readonly><BR>
		Nom du Fichier : <input type='text' name='nomFichier' id='nomFichier' value='{nomFichier}' readonly>
	</fieldset>
	<fieldset>
		<legend>Apercu du fichier {nomFichier}</legend>
		{csv}
	</fieldset>
	<fieldset>
		<legend>Groupes</legend>
		Nom : <input type='text' name='nom' value='1'>
		Pr&eacute;nom : <input type='text' name='prenom' value='2'>
		Groupe : <input type='text' name='groupe' value='3'>
		Premi&egrave;re ligne  : <input type='text' name='ligne1' value='2'>
	</fieldset>
	<input type='submit' name="valid" value="Continuer" size='0' maxlength='0' >
	<input type='submit' name='cancel' value='Abandon'>
	<button type="button" onclick="javascript:history.back()">Retour</button>
</form>
