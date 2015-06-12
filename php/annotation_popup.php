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
	echo "<p id='qtp'>ID: " . $results[0]['locus'] . ", ";
	echo "Name: " . $results[0]['name'] . "</p>";
	if($results[0]['description'] == ""){
	echo "<p id='qtp'>Description: None</p>"; 
	}else{
	echo "<p id='qtp'>Description: " . $results[0]['description'] . "</p>"; 
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
//Do Ortho Queries
$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["orthodb"] .';charset=utf8';
$db = new PDO($dbInit, $settings["user"], $settings["password"]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

foreach ($alt_specs as $ortho){
	$gene = $species;
	$inp = array($gene."_Genes.name",$ortho."_Genes.name",$gene."_Genes",$gene."_Ortho",$gene."_Ortho.id",$gene."_Genes.id",$ortho."_Ortho",$ortho."_Ortho.orth_id",$gene."_Ortho.orth_id",$ortho."_Genes",$ortho."_Genes.id",$ortho."_Ortho.id",$gene."_Genes.name");
	$oquery = "SELECT " . $inp[0] . " AS gene, " . $inp[1] . " AS ortho FROM " . $inp[2] . " INNER JOIN " . $inp[3] . " ON " . $inp[4] . " = " . $inp[5] . " INNER JOIN " . $inp[6] . " ON " . $inp[7] . " = " . $inp[8] . " INNER JOIN " . $inp[9] . " ON " . $inp[10] . " = " . $inp[11] . " WHERE " . $inp[12] . " = ?";
	
	$query = $db->prepare($oquery);
	if($query->execute(array($_GET["gene"]))){
		$results = $query->fetchAll();
		if(array_key_exists(0, $results)){
			echo "<p id='qtp'>" . $prefix_name[$ortho]  . " Ortholog: " . $results[0]['ortho'] . "</p>";
			echo "<p id='qtp'><a href='/annotation_query.php?anlink=True&spec=". $ortho . "&gene=" . $results[0]['ortho'] . "' target='_blank'>Annotation Query</a></p>";
			echo "<p id='qtp'><a href='/gene_set_query.php?netlink=True&spec=". $ortho . "&gene=" . $results[0]['ortho'] . "' target='_blank'>Network Query</a></p>";
			echo "<p id='qtp'><a href='/expression_query.php?exlink=True&spec=". $ortho . "&gene=" . $results[0]['ortho'] . "' target='_blank'>Expression Query</a></p>";
		}else{
			echo "<p id='qtp'> Ortholog for species " . $prefix_name[$ortho] . ": None</p>";
		}
	}
}



//Close connection
$db=null;
?>
