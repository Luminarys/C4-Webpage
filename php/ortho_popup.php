<?php 

//Set debugging on
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Load the settings
$settings = parse_ini_file("../settings.ini");	

$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["orthodb"] .';charset=utf8';
$db = new PDO($dbInit, $settings["user"], $settings["password"]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$query = $db->prepare("SELECT * FROM OrthoMapReference");
$validSpecies = array();
if($query->execute()){
	$result = $query->fetchAll();
	foreach ($result as $spec){
		array_push($validSpecies, $spec["prefix"]);
	}
}else{
	echo "Warning, no MapReference table defined, exiting";
	exit();
}
//SELECT ortho_prefix FROM OrthoMapOut WHERE network_prefix = ;
$query = $db->prepare("SELECT ortho_prefix FROM OrthoMapOut WHERE network_prefix = ?");
$species = $_GET["spec"];
if($query->execute(array($species))){
	$result = $query->fetchAll();
	$dataSet = $result[0][0];
}else{
	echo "Warning, no MapReference table defined, exiting";
	exit();
}


//Initialize Server, using settings
$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["maindb"] .';charset=utf8';
$db = new PDO($dbInit, $settings["user"], $settings["password"]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


//Build valid species and genes from MapReference table
$query = $db->prepare("SELECT * FROM MapReference");
$validGenes = "";
if($query->execute()){
	$result = $query->fetchAll();
	foreach ($result as $spec){
		array_push($validSpecies, $spec["prefix"]);
		$validGenes .= $spec["regex"] . "|";
	}
}else{
	echo "Warning, no MapReference table defined, exiting";
	exit();
}
$validGenes = "/" . substr($validGenes, 0, -1) . "/";

//Build and execute query
if(!in_array($species, $validSpecies)){
	echo "Invalid SQL query, please try again";	
	exit();
}
//Check if we're returning results for an annotation query
$annotation = false;
if(array_key_exists("annot", $_GET)){
	$annotation = true;	
}
$alt_specs = array();
$query = $db->prepare("SELECT * FROM MapReference");
$prefix_name = array();
if($query->execute()){
	$result = $query->fetchAll();
	foreach ($result as $spec){
		if($spec['prefix'] != $species){
			array_push($alt_specs, $spec["prefix"]);
			$prefix_name[$spec["prefix"]] = $spec["display_name"];
		}
	}
}else{
	echo "Warning, no MapReference table defined, exiting";
	exit();
}


$query = $db->prepare("SELECT * FROM " . $species . "_Annotation WHERE locus = ?");
if($query->execute(array($_GET["gene"]))){
	$results = $query->fetchAll();
	$rows = $query->rowCount();
	if(array_key_exists(0, $results)){
		echo "<p id='qtp'>ID: " . $results[0]['locus'] . ", ";
		if(array_key_exists('name', $results[0])){
			echo "Name: " . $results[0]['name'] . "</p>";
		}else{
			echo "<p id='qtp'>Name: None</p>"; 
		}
		if(array_key_exists('description', $results[0])){
			if($results[0]['description'] == ""){
				echo "<p id='qtp'>Description: None</p>"; 
			}else{
				echo "<p id='qtp'>Description: " . $results[0]['description'] . "</p>"; 
			}
		}else{
			echo "<p id='qtp'>Description: None</p>"; 
		}
	}else{
		echo "<p id='qtp'>ID: " . $_GET['gene'] . "</p>";
		echo "<p id='qtp'>Name: None</p>"; 
		echo "<p id='qtp'>Description: None</p>"; 
	}
}
//SELECT * FROM (SELECT * FROM Zmays_Genes WHERE Zmays_Genes.name='GRMZM2G001272') res LEFT JOIN Zmays_Metrics ON Zmays_Metrics.id = res.id;
$query = $db->prepare("SELECT * FROM (SELECT * FROM " . $species . "_Genes WHERE " . $species . "_Genes.name  = ? ) res LEFT JOIN " . $species . "_Metrics ON " . $species . "_Metrics.id = res.id");
if($query->execute(array($_GET["gene"]))){
	if(!$annotation){
		$results = $query->fetchAll();
		foreach ($results as $row) {
	    		echo "<p id='qtp'>Mean Exp: " . $row['mean_exp'] . "</p>";
	    		echo "<p id='qtp'>Mean Exp Rank:" . $row['mean_exp_rank'] . "</p>";
	    		echo "<p id='qtp'>K: " . $row['k'] . "</p>";
	    		echo "<p id='qtp'>K Rank:" . $row['k_rank'] . "</p>";
	    		echo "<p id='qtp'><p id='qtp'>Module: " . $row['module'] . "</p>";
	    		echo "<p id='qtp'>Modular K:" . $row['modular_k'] . "</p>";
	    		echo "<p id='qtp'>Modular K Rank:" . $row['modular_k_rank'] . "</p>";
	    		echo "<p id='qtp'>Modular Mean Exp rank:" . $row['modular_mean_exp_rank'] . "</p> ";
		}
	}else{

	}
}
//Close connection
$db=null;
?>
