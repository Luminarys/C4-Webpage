<?php 

//Set debugging on
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Load the authentication into an array
$auth = file("DB.auth");

//Initialize Server, loading the file auth
$dbInit = 'mysql:host=' . substr($auth[0],0,-1) . ';dbname=C4;charset=utf8';
$db = new PDO($dbInit, substr($auth[1],0,-1), substr($auth[2],0,-1));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Process user input by looping through the GET fields and appending the values onto the pre-query strings
$validSpecies = array("Zmays","Sbicolor");
$pre_query = "SELECT * FROM ";
$pre_query_a = "WHERE gene_id IN (";
$species;
$AND = False;
$validGenes = "((GRMZM\dG\d{6})|(AC\d{6}.\d_FG\d{3})|(Sobic\.\d{3}(G|K)\d{6}))";
foreach ($_GET as $key => $value) {

	if($key[0] == "g"){
		if(!preg_match($validGenes,$value,$match)){
			echo "Invalid gene used, please try again";	
			exit();
		}
		$pre_query_a.=("'" . $match[0] . "',");
	}else if($key == "spec"){
		$species = $value;
		if(!in_array($species, $validSpecies)){
			echo "Invalid SQL query, please try again";	
			exit();
		}
		$pre_query.=($value . "_Expression");
	}
}
//Prepare and execute query, concatenating the pre-query strings
//The substr is used to remove the final ','
$query = $db->prepare($pre_query . " " . substr($pre_query_a,0,-1) . ")");
if($query->execute()){
	//Get the results
	$results = $query->fetchAll();
	$rows = $query->rowCount();
	$data = array();
	foreach ($results as $row) {
		$data[$row["gene_id"]] = array(
			"MP-1" => array(
				$row["Zm.MP-1-2"],  $row["Zm.MP-1-3"], $row["Zm.MP-1-4"]
			),
			"MP-4" => array(
				$row["Zm.MP-4-2"],  $row["Zm.MP-4-3"], $row["Zm.MP-4-4"]
			),
			"MP-T" => array(
				$row["Zm.MP-T-2"],  $row["Zm.MP-T-3"], $row["Zm.MP-T-4"]
			),
			"BS-1" => array(
				$row["Zm.BS-1-1"],  $row["Zm.BS-1-2"], $row["Zm.BS-1-4"]
			),
			"BS-4" => array(
				$row["Zm.BS-4-1"],  $row["Zm.BS-4-2"], $row["Zm.BS-4-4"]
			),
			"BS-T" => array(
				$row["Zm.BS-T-1"],  $row["Zm.BS-T-2"], $row["Zm.BS-T-4"]
			),
			"LS-1" => array(
				$row["Zm.leaf.R1-1"], $row["Zm.leaf.R3-1"], $row["Zm.leaf.R4-1"], $row["Zm.leaf.R6-1"]
			),
			"LS-2" => array(
				$row["Zm.leaf.R1-2"], $row["Zm.leaf.R2-2"], $row["Zm.leaf.R3-2"], $row["Zm.leaf.R4-2"], $row["Zm.leaf.R6-2"]
			),
			"LS-3" => array(
				$row["Zm.leaf.R1-3"], $row["Zm.leaf.R2-3"], $row["Zm.leaf.R3-3"], $row["Zm.leaf.R4-3"]
			),
			"LS-4" => array(
				$row["Zm.leaf.R1-4"], $row["Zm.leaf.R2-4"], $row["Zm.leaf.R3-4"], $row["Zm.leaf.R4-4"], $row["Zm.leaf.R6-4"]
			),
			"LS-6" => array(
				$row["Zm.leaf.R1-6"], $row["Zm.leaf.R2-6"], $row["Zm.leaf.R3-6"], $row["Zm.leaf.R4-6"], $row["Zm.leaf.R6-6"]

			),
			"LS-8" => array(
				$row["Zm.leaf.R1-8"], $row["Zm.leaf.R2-8"], $row["Zm.leaf.R3-8"], $row["Zm.leaf.R4-8"]
			),
			"LS-9" => array(
				$row["Zm.leaf.R1-9"], $row["Zm.leaf.R2-9"], $row["Zm.leaf.R3-9"], $row["Zm.leaf.R4-9"], $row["Zm.leaf.R6-9"]
			),
			"LS-10" => array(
				$row["Zm.leaf.R1-10"], $row["Zm.leaf.R2-10"], $row["Zm.leaf.R3-10"], $row["Zm.leaf.R4-10"], $row["Zm.leaf.R6-10"]
			),
			"LS-11" => array(
				$row["Zm.leaf.R1-11"], $row["Zm.leaf.R2-11"], $row["Zm.leaf.R3-11"], $row["Zm.leaf.R4-11"], $row["Zm.leaf.R6-11"]
			),
			"LS-12" => array(
				$row["Zm.leaf.R1-12"], $row["Zm.leaf.R2-12"], $row["Zm.leaf.R3-12"], $row["Zm.leaf.R4-12"], $row["Zm.leaf.R6-12"]
			),
			"LS-13" => array(
				$row["Zm.leaf.R1-13"], $row["Zm.leaf.R2-13"], $row["Zm.leaf.R3-13"], $row["Zm.leaf.R4-13"], $row["Zm.leaf.R6-13"]
			),
			"LS-14" => array(
				$row["Zm.leaf.R1-14"], $row["Zm.leaf.R2-14"], $row["Zm.leaf.R3-14"], $row["Zm.leaf.R4-14"], $row["Zm.leaf.R6-14"]
			),
			"LS-15" => array(
				$row["Zm.leaf.R1-15"], $row["Zm.leaf.R2-15"], $row["Zm.leaf.R3-15"], $row["Zm.leaf.R4-15"], $row["Zm.leaf.R6-15"]
			),
			"LS-16" => array(
				$row["Zm.leaf.R1-16"], $row["Zm.leaf.R2-16"], $row["Zm.leaf.R3-16"], $row["Zm.leaf.R4-16"], $row["Zm.leaf.R6-16"]
			)
			);
	}
	echo json_encode($data);
}


//Close connection
$db=null;
?>
