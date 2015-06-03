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
if($query->execute()){
	$result = $query->fetchAll();
	foreach ($result as $spec){
		if($spec['prefix'] != $species){
			array_push($alt_specs, $spec["prefix"]);
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
	echo "<p>ID: " . $results[0]['locus'] . ", ";
	echo "Name: " . $results[0]['name'] . "</p>";
	echo "<p>Description: " . $results[0]['description'] . "</p>"; 
}
//SELECT * FROM (SELECT * FROM Zmays_Genes WHERE Zmays_Genes.name='GRMZM2G001272') res LEFT JOIN Zmays_Metrics ON Zmays_Metrics.id = res.id;
$query = $db->prepare("SELECT * FROM (SELECT * FROM " . $species . "_Genes WHERE " . $species . "_Genes.name  = ? ) res LEFT JOIN " . $species . "_Metrics ON " . $species . "_Metrics.id = res.id");
if($query->execute(array($_GET["gene"]))){
	if(!$annotation){
		$results = $query->fetchAll();
		foreach ($results as $row) {
	    		echo "<p>Mean Exp: " . $row['mean_exp'] . ", ";
	    		echo "Mean Exp Rank:" . $row['mean_exp_rank'] . ", ";
	    		echo "K: " . $row['k'] . ", ";
	    		echo "K Rank:" . $row['k_rank'] . "</p>";
	    		echo "<p>Module: " . $row['module'] . ", ";
	    		echo "Modular K:" . $row['modular_k'] . ", ";
	    		echo "Modular K Rank:" . $row['modular_k_rank'] . ", ";
	    		echo "Modular Mean Exp rank:" . $row['modular_mean_exp_rank'] . "</p> ";
		}
	}else{

	}
}
//Do Ortho Queries
$dbInit = 'mysql:host=' . substr($auth[0],0,-1) . ';dbname=' . substr($auth[4],0,-1) .';charset=utf8';
$db = new PDO($dbInit, substr($auth[1],0,-1), substr($auth[2],0,-1));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

foreach ($alt_specs as $ortho){
	$gene = $species;
	$inp = array($gene."_Genes.name",$ortho."_Genes.name",$gene."_Genes",$gene."_Ortho",$gene."_Ortho.id",$gene."_Genes.id",$ortho."_Ortho",$ortho."_Ortho.orth_id",$gene."_Ortho.orth_id",$ortho."_Genes",$ortho."_Genes.id",$ortho."_Ortho.id",$gene."_Genes.name");
	$oquery = "SELECT " . $inp[0] . " AS gene, " . $inp[1] . " AS ortho FROM " . $inp[2] . " INNER JOIN " . $inp[3] . " ON " . $inp[4] . " = " . $inp[5] . " INNER JOIN " . $inp[6] . " ON " . $inp[7] . " = " . $inp[8] . " INNER JOIN " . $inp[9] . " ON " . $inp[10] . " = " . $inp[11] . " WHERE " . $inp[12] . " = ?";
	
	$query = $db->prepare($oquery);
	if($query->execute(array($_GET["gene"]))){
		$results = $query->fetchAll();
		echo "<p> Ortholog for species " . $ortho . ": " . $results[0]['ortho'] . "</p>";
	}
}



//Close connection
$db=null;
?>
