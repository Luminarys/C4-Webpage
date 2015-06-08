<?php 

//Set debugging on
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Load the authentication into an array
$auth = file("DB.auth");

//Initialize Server, loading the file auth
$dbInit = 'mysql:host=' . substr($auth[0],0,-1) . ';dbname=' . substr($auth[3],0,-1) .';charset=utf8';
$db = new PDO($dbInit, substr($auth[1],0,-1), substr($auth[2],0,-1));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$species = $_GET["spec"];

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
	echo $results[0]['name'];
}
//Close connection
$db=null;
?>
