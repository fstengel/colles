<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

require("Chemins.php");

require_once("Constantes.php");
require_once(libPath()."Design.php");

/**
 * Transforme une note/texte en décimal. Les notes ont un séparateur décimal qui peut être indifféremment . ou ,
 * Le texte est Abs, A (absent), N, NN (Non Noté). Utilise le tableau constant TexteVersNote
 *
 * @param string $valeur la valeur à transformer
 * @param string $contraint=True  pour contraindre la note à l'intervalle [0,20].
 *
 * @return float la note encodée 
 */
function parseNote($valeur, $contraint=True) {
	$texte = strtoupper($valeur);
	if (array_key_exists($texte, TexteVersNote)) {
		return TexteVersNote[$texte];
	}
	$note = floatval(str_replace(',', '.', $valeur));
	if ($contraint && $note<0) $note=0;
	if ($contraint && $note>20) $note=20;
	return $note;
}

/**
 * Transforme une note encodée en texte. Si la note est un nombre, le séparateur est la virgule. Utilise le tableau constant NoteVersTexte.
 *
 * @param string $valeur la valeur à transformer
 *
 * @return string la note décodée 
 */
function parseValeur($note) {
	if (!$note) return;
	if (array_key_exists((int) $note, NoteVersTexte)) {
		return NoteVersTexte[(int) $note];
	}
	$valeur = number_format($note, 1, ",", "");
	return $valeur;
}

?>