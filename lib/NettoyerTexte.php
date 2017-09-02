<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */


// Nettoie le texte ou le tableau....

function _nettoyerTexte_($t) { return htmlspecialchars(strip_tags($t)); }

function nettoyerTexte($t) {
	if (is_array($t)) {
		$t = array_map('nettoyerTexte',$t);
	} else {
		$t = _nettoyerTexte_($t);
	}
	return $t;
}
?>
