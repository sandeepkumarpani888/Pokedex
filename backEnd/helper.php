<?php

$url=$_SERVER['REQUEST_URI'];
$urlParts=explode("/",$url);
$getUrlPath=$urlParts[2];

if($getUrlPath=="getData"){
	require_once('getPokemonDetails.php');
	$pokemonName=$urlParts[3];
	$finalVal=getData($pokemonName);
	echo $finalVal;
}
else{
	echo "{error: 404 *poof*}";
}