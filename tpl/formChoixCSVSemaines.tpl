<form  method='POST' action='lireCSVSemaines.php?action=verifier' name='Form'>
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
		<legend>Semaines</legend>
		Num&eacute;ro : <input type='text' name='numero' value='1'>
		D&eacute;but : <input type='text' name='debut' value='2'>
		Fin : <input type='text' name='fin' value='3'>
		Premi&egrave;re colonne  : <input type='text' name='colonne1' value='6'>
	</fieldset>
	{message}
	<input type='submit' name="valid" value="Continuer" size='0' maxlength='0' >
	<input type='submit' name='cancel' value='Abandon'>
	<button type="button" onclick="javascript:history.back()">Retour</button>
</form>
