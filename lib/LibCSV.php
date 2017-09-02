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


// À supprimer ?
function gererCSV() {
}

/**
 * Lit le fichier donné en argument. Si $tout est false ne lit que les MaxLignes premières lignes.
 *
 * @param string $cheminCSV Le chemin d'accès au fichier
 * @param boolean $tout Si vrai tout le fichier les lu.
 *
 * @return void
 */

function chargerCSV($cheminCSV, $tout=False) {
	$fichier = fopen($cheminCSV, "r");
	$lignes = array();
	$nLignes = 0;
	if (!$fichier) {
		echo "Fichier $cheminCSV ($fichier) invalide ?<BR>";
		return;
	}
	
	while (!feof($fichier)) {
		$ligne = fgetcsv($fichier);
		if ($ligne) {
			$lignes[]=$ligne;
			$nLignes++;
		}
		if ((!$tout)&&($nLignes>MaxLignes)) break;
	}
	fclose($fichier);
	return $lignes;
}

/**
 * Affiche le fichier donné en argument sous forme de table. Si $tout est false ne lit que les MaxLignes premières lignes.
 *
 * @param string $fichier Le chemin d'accès au fichier
 * @param boolean $tout Si vrai tout le fichier les lu.
 *
 * @return void
 */

function afficherCSV($fichier, $tout=False) {
	$code = codeAffichageCSV($fichier, $tout);
	echo $code;
}

/**
 * @deprecated Codage en dur. Conservé pour historique...
 * @see codeAffichageCSV pour la bonne fonction.
 *
 * Construit le code HTML pour afficher le fichier donné en argument sous forme de table. Si $tout est false ne lit que les MaxLignes premières lignes.
 *
 * @param string $fichier Le chemin d'accès au fichier
 * @param boolean $tout Si vrai tout le fichier les lu.
 *
 * @return void
 */

function codeAffichageCSVOLD($fichier, $tout=False) {
	$lignes = chargerCSV($fichier, $tout);
	$premiere = TRUE;
	$code = "";
	$code .= "<TABLE border='1'>";
	$code .= "<TBODY>";
	$nligne=1;
	foreach ($lignes as $ligne) {
		if ($ligne) {
			$n = count($ligne);
			if ($premiere) {
				$premiere = FALSE;
				$nCol = $n;
				if ($tout) $nColVis = $n; else  $nColVis = min($n, MaxColonnes);
				$code .= "<TR><TD></TD>";
				for ($col=1; $col<=$nColVis; $col++) {
					$code .= "<TD>$col</TD>";
				}
				$code .= "</TR>";
			} else {
				if ($n!=$nCol) {
					$code .= "?? : ";
					//var_dump($ligne);
					$code .= "<BR>";
				}
			}
			$code .= "<TR><TD>$nligne</TD>";
			for ($col = 0; $col <$nColVis; $col++) {
				$code .= "<TD>$ligne[$col]</TD>";
			}
			$code .= "</TR>\n";
			$nligne++;
			if ((! $tout)&&($nligne>MaxLignes)) break;
		}
	}
	$code .= "</TBODY>";
	$code .= "</TABLE>";
	return $code;
}

/**
 * Construit le code HTML pur afficher le fichier donné en argument sous forme de table. Si $tout est false ne lit que les MaxLignes premières lignes. Utilise un template.
 *
 * @param string $fichier Le chemin d'accès au fichier
 * @param boolean $tout Si vrai tout le fichier les lu.
 *
 * @return void
 */

function codeAffichageCSV($fichier, $tout=False) {
	$t = new Template(tplPath());
	$t-> set_filenames(array('liste'=>'tableauCSV.tpl'));
	$lignes = chargerCSV($fichier, $tout);
	$premiere = TRUE;
	$nligne=1;
	foreach ($lignes as $ligne) {
		if ($ligne) {
			$n = count($ligne);
			if ($premiere) {
				$premiere = FALSE;
				$nCol = $n;
				if ($tout) $nColVis = $n; else  $nColVis = min($n, MaxColonnes);
				
				for ($col=1; $col<=$nColVis; $col++) {
					$t->assign_block_vars('ligne1', array('texte'=>$col));
				}
				
			} else {
				if ($n!=$nCol) {
					$code .= "?? : ";
					//var_dump($ligne);
					$code .= "<BR>";
				}
			}
			if ($nligne%2) $eo='even'; else $eo='odd';
			$t->assign_block_vars('ligne', array('nom'=>$nligne, 'eo'=>$eo));
			for ($col = 0; $col <$nColVis; $col++) {
				$t->assign_block_vars('ligne.cell', array('groupe'=>$ligne[$col]));
			}
			$nligne++;
			if ((! $tout)&&($nligne>MaxLignes)) break;
		}
	}
	$code = $t->HTML_for_handle('liste');
	
	return $code;
	
}

?>