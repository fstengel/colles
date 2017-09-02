<script>
	function fabriquerNomFicher() {
		var cl = document.getElementById('classe').value;
		var an = document.getElementById('annee').value;
		var nf = ""+cl+"-"+an+"-semaines.csv";
		document.getElementById('nomFichier').value = nf;
	}
</script>
<form  method='POST' action='lireCSVSemaines.php?action=choix' name='Form'>
	<fieldset>
		<legend>Fichier CSV des Semaines</legend>
		Classe : <input type='text' name='classe' id='classe' value='PC' onKeyUp="fabriquerNomFicher()">
		Ann&eacute;e : <input type='text' name='annee' id='annee' value='2015' onKeyUp="fabriquerNomFicher()"><BR>
		Nom du Fichier : <input type='text' size="40" name='nomFichier' id='nomFichier' value='PC-2015-semaines.csv'>
	</fieldset>
	<input type='submit' name="valid" value="Continuer" size='0' maxlength='0' >
	<input type='submit' name='cancel' value='Abandon'>
	<button type="button" onclick="javascript:history.back()">Retour</button>
</form>
