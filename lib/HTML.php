<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 * @deprecated ?
 *
 */

// Fonctions produisant des conteneurs HTML
function Ancre ($url, $libelle, $classe=-1)
{
  $optionClasse = "";
  if ($classe != -1) $optionClasse = " CLASS='$classe'";
  return "<A HREF='$url'$optionClasse>$libelle</A>";   
}

function Image ($url, $largeur=-1, $hauteur=-1, $bordure=0)
{
  $attrLargeur = $attrHauteur = "";
  if ($largeur != -1) $attrLargeur = " WIDTH  = '$largeur' ";
  if ($hauteur != -1) $attrHauteur = " HEIGHT = '$hauteur' ";

  return "<IMG SRC='$url' $attrLargeur  $attrHauteur BORDER='$bordure'>\n";   
}
?>