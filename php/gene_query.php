<html>

<head>


<!-- jQuery UI CSS -->
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
  
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>

<!-- jQuery UI -->  
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>

</head>
<body>
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
$validSpecies = array("Zmays","Sbicolor", "Sitalica");
$pre_query = "SELECT * FROM ";
$pre_query_a = "WHERE gene_id_A IN (";
$pre_query_b = "OR gene_id_B IN (";
$species;
$AND = False;
$validGenes = "((GRMZM\dG\d{6})|(AC\d{6}.\d_FG\d{3})|(Sobic\.\d{3}(G|K)\d{6})|(Si[0-9]{6}m))";

$species = $_GET['spec'];

if(in_array($species,$validSpecies)){
	//This is pretty complex - Basically the query utilizes a UNION and a lot of LEFT JOINs to jam a bunch of tables together and select proper value.
	//Most important is the () res section which essentially finds the gene id and adjacency value for all edges attached to the input gene
	//The UNION is used because there are cases in which node1 = input gene and others where node2 = input gene. We need those instances to be filtered
	//so a UNION of situations where Zmays_Edges.node1 != Zmays_Adjacency.id and likewise with node2 is necessary.
	//Res is then used to join the metrics and gene name tables
	$inp = array($species."_Genes",$species."_Adjacency",$species."_Adjacency.id",$species."_Genes.id",$species."_Edges",$species."_Edges.edgeId",$species."_Adjacency.edgeId",$species."_Edges.node1",$species."_Adjacency.id",$species."_Genes.name",$species."_Genes",$species."_Adjacency",$species."_Adjacency.id",$species."_Genes.id",$species."_Edges",$species."_Edges.edgeId",$species."_Adjacency.edgeId",$species."_Edges.node2",$species."_Adjacency.id",$species."_Genes.name", $species."_Genes", $species."_Genes.id", $species."_Metrics", $species."_Metrics.id");

	$pre_query_a = "SELECT * FROM (SELECT node1 as gene, adjacency FROM " . $inp[0] . " LEFT JOIN " . $inp[1] . " ON " . $inp[2] . " = " . $inp[3] . " LEFT JOIN " . $inp[4] . " ON " . $inp[5] . " = " . $inp[6] . " WHERE " . $inp[7] . " != " . $inp[8] . " AND " . $inp[9] . " IN (";

	$pre_query_b = ") UNION ALL SELECT node2 as gene, adjacency FROM " . $inp[10] . " LEFT JOIN " . $inp[11] . " ON " . $inp[12] . " = " . $inp[13] . " LEFT JOIN " . $inp[14] . " ON " . $inp[15] . " = " . $inp[16] . " WHERE " . $inp[17] . " != " . $inp[18] . " AND " . $inp[19] . " IN (";

	$pre_query_c = ")) res LEFT JOIN " . $inp[20] . " ON " . $inp[21] . " = res.gene LEFT JOIN " . $inp[22] . " ON " . $inp[23] . " = res.gene";


}else{
	echo "Invalid species used, please try again";	
	exit();
}

foreach ($_GET as $key => $value) {

	if($key[0] == "g"){
		if(!preg_match($validGenes,$value,$match)){
			echo "Invalid gene used, please try again";	
			exit();
		}
		$pre_query_a.=("'" . $match[0] . "',");
		$pre_query_b.=("'" . $match[0] . "',");
	}else if($key == "type"){
		if($value == "AND"){
			$AND = True;
		}
	}
}
//Prepare and execute query, concatenating the pre-query strings
//The substr is used to remove the final ','
$query = $db->prepare(substr($pre_query_a,0,-1) . substr($pre_query_b,0,-1) . $pre_query_c);
if($query->execute()){

	//Get the results
	$results = $query->fetchAll();
	$rows = $query->rowCount();

	//Use these to store values we've seen - they will be used to find
	//all genes which are connected to at least two of the queried ones
	$seen = array();
	$seenTwice = array();
	$indeces = array();
	$pos = 0;
	//Generate an index list of valid rows which will be displayed in the table
	if($AND){
		foreach ($results as $row){
			//If the gene has appeared at least once, but
			//no more than twice then we will add it to the indeces table and to the seenTwice table so it isn't readded
			if(in_array($row['id'],$seen) && !in_array($row['id'],$seenTwice)){
				array_push($indeces,$pos);
				array_push($seenTwice,$row['id']);
			}else if (!in_array($row['id'],$seenTwice)){
				array_push($seen,$row['id']);
			}
			$pos++;
		}
	}
	if(count($indeces) < 1 && $AND){
		echo "No genes which were adjacent to at least two of the queried genes are present";
		exit();
	}
	//Pre table search forms
	echo '<table border="0" cellspacing="5" cellpadding="5">';
        echo '<tbody><tr>';
        echo '    <td><b>Filtering: </b></td>';
        echo '    <td>Column: </td>';
        echo '    <td><select id="filterChoice">';
        echo '    <option value="1">Adjacency Value</option>';
        echo '    <option value="2">Mean Exp</option>';
        echo '    <option value="3">Mean Exp Rank</option>';
        echo '    <option value="4">K</option>';
        echo '    <option value="5">K Rank</option>';
        echo '    <option value="6">Module</option>';
        echo '    <option value="7">Modular K</option>';
        echo '    <option value="8">Modular K Rank</option>';
        echo '    <option value="9">Modular Mean Exp Rank</option>';
        echo '    </select></td>';
        echo '    <td>Minimum: </td>';
        echo '    <td><input type="text" id="min" name="min"></td>';
        echo '    <td>Maximum: </td>';
        echo '    <td><input type="text" id="max" name="max"></td>';
        echo '</tr>';
    	echo ' </tbody></table>	';
	//Initialize table
	echo "<table id='basicQueryTable' style='width:100%'>";
    		echo "<thead>";
    		echo "<th>Gene</th>";
    		echo "<th>Adjacency Value</th>";
    		echo "<th>Mean Exp</th>";
    		echo "<th>Mean Exp Rank</th>";
    		echo "<th>K</th>";
    		echo "<th>K Rank</th>";
    		echo "<th>Module</th>";
    		echo "<th>Modular K</th>";
    		echo "<th>Modular K Rank</th>";
    		echo "<th>Modular Mean Exp Rank</th>";
    		echo "</tr>";
    		echo "</thead>";
		echo "<tfoot></tfoot>";
    		echo "<tbody>";

	//Loop through initial results, perform subquery for Metrics and create the table
	$pos = 0;
	foreach ($results as $row) {
		if($AND){
			//Skip anything not in the index
			if (!in_array($pos,$indeces)){
				$pos++;
				continue;
			}
			$pos++;
		}
		//Echo the table 
    		echo "<tr>";
    		echo "<td>" . $row['name'] . "</td>";
    		echo "<td>" . $row['adjacency'] . "</td>";
    		echo "<td>" . $row['mean_exp'] . "</td>";
    		echo "<td>" . $row['mean_exp_rank'] . "</td>";
    		echo "<td>" . $row['k'] . "</td>";
    		echo "<td>" . $row['k_rank'] . "</td>";
    		echo "<td>" . $row['module'] . "</td>";
    		echo "<td>" . $row['modular_k'] . "</td>";
    		echo "<td>" . $row['modular_k_rank'] . "</td>";
    		echo "<td>" . $row['modular_mean_exp_rank'] . "</td>";
    		echo "</tr>";
		}
    	echo "</tbody>";
	echo "</table>";
}


//Close connection
$db=null;
?>

</body>
</html>

