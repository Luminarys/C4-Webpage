<?php 

//Set debugging on
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Load the settings
$settings = parse_ini_file("../settings.ini");	

//Initialize Server, using settings
$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["maindb"] .';charset=utf8';
$db = new PDO($dbInit, $settings["user"], $settings["password"]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Process user input by looping through the GET fields and appending the values onto the pre-query strings
$speciesPrefix = array("Zm","","");
$pre_query = "SELECT * FROM ";
$pre_query_a = " INNER JOIN ";
$pre_query_b = " ON ";
$pre_query_c = " WHERE "; 
$pre_query_d = " IN (";
$species;
$AND = False;

//Build valid species and genes from MapReference table
$query = $db->prepare("SELECT * FROM MapReference");
$validSpecies = array();
$validGenes = "";
if($query->execute()){
	$result = $query->fetchAll();
	foreach ($result as $spec){
		array_push($validSpecies, $spec["prefix"]);
		$validGenes .= $spec["regex"] . "|";
	}
}else{
	echo "Warning, no MapReference table defined, exiting";
	e/xit();
}
$validGenes = "/" . substr($validGenes, 0, -1) . "/";

$species = $_GET['spec'];
if(!in_array($species,$validSpecies)){
	echo "Invalid SQL Query used, please try again";
	exit();
}
//Prepare and execute query, concatenating the pre-query strings
//The substr is used to remove the final ','
//echo $pre_query . $pre_query_a . $pre_query_b . $pre_query_c . substr($pre_query_d,0,-1) . ")";
$sample_query = $db->prepare("Select * FROM " . $species . "_Expression_Map ORDER BY biosample_id");
if($sample_query->execute()){
	//Get the results
	$layout = $sample_query->fetchAll();
	$data = array();
	$res = array();
	$carray = array();
	foreach ($layout as $sample) {
		if(!in_array($sample['display_name'], $res)){
			array_push($res,$sample['display_name']);
		}
	}
	//echo json_encode($data);
	echo json_encode($res);
}


//Close connection
$db=null;
?>
