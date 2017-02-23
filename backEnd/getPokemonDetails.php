<?php

function extractData($value){
	$finalValue=[];
	$finalValue["name"]=$value["name"];
	$finalValue["weight"]=$value["weight"];
	$finalValue["height"]=$value["height"];
	$finalValue["sprites"]["front_female"]=$value["sprites"]["front_female"];
	$finalValue["sprites"]["front_default"]=$value["sprites"]["front_default"];
	$finalValue["stats"]=$value["stats"];
	return $finalValue;
}

function getPokeDataFromServer($name){
	echo $name;
	$cl=curl_init('http://pokeapi.co/api/v2/pokemon/' . $name .'/');
	curl_setopt($cl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($cl,CURLOPT_PROXY,'10.3.100.207:8080');
	$res=curl_exec($cl);
	curl_close($cl);
	$res=json_decode($res,TRUE);
	return $res;
}

function getData($name){
	$redis=new Redis();
	$redis->connect('127.0.0.1',6379);
	if($redis->exists($name)){
		$value=$redis->get($name);
		$value=json_decode($value);
		echo json_encode($value);
	}
	else{
		$value=getPokeDataFromServer($name);
		$jsonData=extractData($value);
		$finalString=json_encode($jsonData);
		$redis->set($name,$finalString);
		echo json_encode($jsonData);
	}
}
