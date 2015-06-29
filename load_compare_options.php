
<?php

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
	$query = $db->prepare("SELECT pfx_compare, MapReference.display_name FROM MapCompare LEFT JOIN MapReference ON MapCompare.pfx_compare = MapReference.prefix WHERE pfx_control = ?");
	if($query->execute(array($_GET["spec"]))){
		echo "<select id='compareSelect'>";
		$result = $query->fetchAll();
		foreach ($result as $spec){
			echo "<option value=" . $spec["pfx_compare"] . ">" . $spec["display_name"] . "</option>";
		}
		echo "</select>";
	}else{
		echo "Warning, no MapReference table defined, exiting";
		exit();
	}
?>
