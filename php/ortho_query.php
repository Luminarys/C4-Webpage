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

$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["orthodb"] .';charset=utf8';
$db = new PDO($dbInit, $settings["user"], $settings["password"]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$gene = $_GET['orig'];
$ortho = $_GET['ortho'];

//Check if we're returning results for an annotation query
$annotation = false;
if(array_key_exists("annot", $_GET)){
	$annotation = true;	
}

if(in_array($gene,$validSpecies) && in_array($ortho,$validSpecies)){

	$inp = array($gene."_Genes.name",$ortho."_Genes.name",$gene."_Genes",$gene."_Ortho",$gene."_Ortho.id",$gene."_Genes.id",$ortho."_Ortho",$ortho."_Ortho.orth_id",$gene."_Ortho.orth_id",$ortho."_Genes",$ortho."_Genes.id",$ortho."_Ortho.id",$gene."_Genes.name");

	$pre_query = "SELECT " . $inp[0] . " AS gene, " . $inp[1] . " AS ortho FROM " . $inp[2] . " INNER JOIN " . $inp[3] . " ON " . $inp[4] . " = " . $inp[5] . " INNER JOIN " . $inp[6] . " ON " . $inp[7] . " = " . $inp[8] . " INNER JOIN " . $inp[9] . " ON " . $inp[10] . " = " . $inp[11] . " WHERE " . $inp[12] . " IN (";

}else{
	echo "Invalid species used, please try again";	
	exit();
}

$validGenes = "((GRMZM\dG\d{6})|(AC\d{6}.\d_FG\d{3})|(Sobic\.\d{3}(G|K)\d{6})|(Si[0-9]{6}m))";
foreach ($_GET as $key => $value) {

	if($key[0] == "g"){
		if(!preg_match($validGenes,$value,$match)){
			echo $value + "\n";
			echo "Invalid gene used, please try again";	
			exit();
		}
		$pre_query.=("'" . $match[0] . "',");
	}
}
//Prepare and execute query, concatenating the pre-query strings
//The substr is used to remove the final ','
$c_pq = substr($pre_query,0,-1) . ")";
$query = $db->prepare($c_pq);
if($query->execute()){
	if(!$annotation){
		//Initialize table
		echo "<form id='geneSelections'>";
		echo "<table id='orthoQueryTable' style='width:100%'>";
	    		echo "<thead>";
	    		echo "<th>Gene</th>";
	    		echo "<th>Ortholog</th>";
	    		echo "<th>Network Query</th>";
	    		echo "<th>Multigene Query Selection</th>";
	    		echo "</tr>";
	    		echo "</thead>";
			echo "<tfoot></tfoot>";
	    		echo "<tbody>";
		//Get the results
		$results = $query->fetchAll();
		$rows = $query->rowCount();
	
		//Prepare the second query for extracting data from Zmays_Metrics
		foreach ($results as $row) {
			//In the case of an intersection, just skip the row in the table	
			//Echo the table 
	    		echo "<tr>";
	    		echo "<td>" . $row['gene'] . "</td>";
	    		echo "<td>" . $row['ortho'] . "</td>";
			echo "<td><a href='gene_set_query.php?netlink=true&spec=". $ortho . "&g0=" . $row['ortho'] . "'>Query</a></td>";
			echo "<td><input type='checkbox' value=" . $row['ortho'] . "></td>";
	    		echo "</tr>";
			}
	    	echo "</tbody>";
		echo "</table>";
		echo "</form>";
	}else{
		$results = $query->fetchAll();
		echo "<p> Ortholog for species " . $ortho . ": " . $results[0]['ortho'] . "</p>";
	}
}


//Close connection
$db=null;
?>
