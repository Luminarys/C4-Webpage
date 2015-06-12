<?php
	//Uses the MapReference table to generate options within a select block. Include this within <select> </select> to have it generate all the available species

	//Set debugging on
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	
	//Load the settings
	$settings = parse_ini_file("settings.ini");	
	
	//Initialize Server, using settings
	$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["maindb"] .';charset=utf8';
	$db = new PDO($dbInit, $settings["user"], $settings["password"]);
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
