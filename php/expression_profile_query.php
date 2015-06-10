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

//Process user input by looping through the GET fields and appending the values onto the pre-query strings
$speciesPrefix = array("Zm","","");
$pre_query = "SELECT * FROM ";
$pre_query_a = " INNER JOIN ";
$pre_query_b = " ON ";
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
}else{
	$value = $species;
	$pre_query.=($value . "_Genes");
	$pre_query_a.=($value . "_Expression");
	$pre_query_b.=($value . "_Expression.id = " . $value . "_Genes.id");
}
//Prepare and execute query, concatenating the pre-query strings
//The substr is used to remove the final ','
//echo $pre_query . $pre_query_a . $pre_query_b . $pre_query_c . substr($pre_query_d,0,-1) . ")";
$query = $db->prepare($pre_query . $pre_query_a . $pre_query_b . $pre_query_c . substr($pre_query_d,0,-1) . ")");
$sample_query = $db->prepare("Select * FROM " . $species . "_Expression_Map ORDER BY biosample_id");
if($query->execute() && $sample_query->execute()){
	//Get the results
	$layout = $sample_query->fetchAll();
	$results = $query->fetchAll();
	$rows = $query->rowCount();
	$data = array();
	$bio_id = $layout[0]['biosample_id'];
	$res = array();
	$carray = array();
	foreach ($results as $row) {
		//Dynamically construct the JSON return by looping through the $layout table.
		//Because we've sorted in the query(ORDER BY), we know that rows are grouped by bioID
		//We can exploit this and simply build a sample array until there is a new bioID, in which case
		//we append the resulting array to the final result and start building a new array for the next sample.
		//This intermediate array is $carray
		$res[$row["name"]] = array();
		$name = $layout[0]['display_name'];
		foreach ($layout as $sample) {
			//If there's a different bioID then prev, set the array within the JSON for the sample equal to cArray
			//then clear $carray and update bio_id and name(the prev. sample name)
			if ($sample['biosample_id'] != $bio_id){
				$res[$row["name"]][$name] = $carray; 
				$bio_id = $sample['biosample_id'];
				$name = $sample['display_name'];
				$carray = array();
			}
			array_push($carray, $row[$sample["field_name"]]);
		}
		$res[$row["name"]][$name] = $carray; 
	}
	//echo json_encode($data);
	echo json_encode($res);
}


//Close connection
$db=null;
?>
