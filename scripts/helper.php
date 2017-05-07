<?hh
header("Access-Control-Allow-Origin: *");
$url = $_SERVER['REQUEST_URI'];
$urlParts = explode("/", $url);
$getUrlPath = $urlParts[2];

if($getUrlPath == "getData") {
	require_once('backend.php');
	$pokemonName = $urlParts[3];
	$pokemonData = retrivePokeData($pokemonName);
	echo $pokemonData;
}
else{
	echo "{error: 404 *poof*}";
}