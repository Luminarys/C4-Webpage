<?php
	//Set debugging on
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	
	//Load the authentication into an array
	$auth = file("php/DB.auth");
	
	//Initialize Server, loading the file auth
	$dbInit = 'mysql:host=' . substr($auth[0],0,-1) . ';dbname=' . substr($auth[3],0,-1) .';charset=utf8';
	$db = new PDO($dbInit, substr($auth[1],0,-1), substr($auth[2],0,-1));
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	//Build valid species and genes from MapReference table
	$query = $db->prepare("SELECT * FROM MapReference");
	if($query->execute()){
		$result = $query->fetchAll();
		foreach ($result as $spec){
			echo "<option value=" . $spec["prefix"] . ">" . $spec["display_name"] . "</option>";
		}
	}else{
		echo "Warning, no MapReference table defined, exiting";
		exit();
	}
?>
