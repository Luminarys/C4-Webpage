<?php
	//Uses the MapReference table to generate options within a select block. Include this within <select> </select> to have it generate all the available species

	//Set debugging on
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	
	//Load the settings
	$settings = parse_ini_file("settings.ini");	
	
	//Initialize Server, using settings
	$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["orthodb"] .';charset=utf8';
	$db = new PDO($dbInit, $settings["user"], $settings["password"]);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	//Build valid species for the gene
	$query = $db->prepare("SELECT DISTINCT ortho_prefix, display_name FROM OrthoMapOut as omo LEFT JOIN OrthoMapReference as omr ON omo.ortho_prefix = omr.prefix;");
	if($query->execute()){
		$result = $query->fetchAll();
		foreach ($result as $spec){
			echo "<option value=" . $spec["ortho_prefix"] . ">" . $spec["display_name"] . "</option>";
		}
	}else{
		echo "Warning, no OrthoMapReference or OrthoMapOut table defined, exiting";
		exit();
	}
?>
