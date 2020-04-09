<?php

function connect($config){
	try{
		$db = new PDO("mysql:host=".$config["server"].";dbname=".$config["db"], $config["login"], $config["password"]);
	}
	catch(Exception $e){
		$db = null;
	}

	return $db;
}