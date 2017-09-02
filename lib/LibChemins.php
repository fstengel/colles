<?php
/**
 * 
 * @author Frank STENGEL
 * @version 1.0 2017-09-02
 *
 */

global $pathStack;

if (!isset($pathStack)) $pathStack=array();
function pushPath($p) { global $pathStack; array_push($pathStack, $p);}
function popPath() { global $pathStack; $p=array_pop($pathStack); return $p;}

function saveRoot() { global $rootPath; pushPath($rootPath);}
function restoreRoot() {global $rootPath; $rootPath = popPath();}

function libPath() { global $rootPath; return $rootPath."lib/";}
function cssPath() { global $rootPath; return $rootPath."css/";}
function jsPath() { global $rootPath; return $rootPath."js/";}
function tplPath() { global $rootPath; return $rootPath."tpl/";}
function uploadPath() { global $rootPath; return $rootPath."uploads/";}



?>