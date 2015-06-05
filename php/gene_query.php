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
$pre_query = "SELECT * FROM ";
$pre_query_a = "WHERE gene_id_A IN (";
$pre_query_b = "OR gene_id_B IN (";
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
	exit();
}
$validGenes = "/" . substr($validGenes, 0, -1) . "/";

$species = $_GET['spec'];
$csv;

if(in_array($species,$validSpecies)){
	//This is pretty complex - Basically the query utilizes a UNION and a lot of LEFT JOINs to jam a bunch of tables together and select proper value.
	//Most important is the () res section which essentially finds the gene id and adjacency value for all edges attached to the input gene
	//The UNION is used because there are cases in which node1 = input gene and others where node2 = input gene. We need those instances to be filtered
	//so a UNION of situations where Zmays_Edges.node1 != Zmays_Adjacency.id and likewise with node2 is necessary.
	//Res is then used to join the metrics and gene name tables
	$metrics = $species."_Metrics";
	$genes = $species."_Genes";
	
	$pre_query = "SELECT G.name AS `source`,GN.id,GN.name AS `name`, AN.description, AN.name as aname, E.adjacency,M.modular_k_rank,M.modular_k, M.modular_mean_exp_rank,M.module,M.k,M.k_rank,M.mean_exp,M.mean_exp_rank FROM ".$genes." AS G LEFT JOIN ".$species."_Adjacency AS A ON A.id = G.id LEFT JOIN ".$species."_Edges AS E ON E.edgeId = A.edgeId LEFT JOIN ".$species."_Metrics AS M ON M.id = CASE WHEN E.node1 = G.id THEN E.node2 ELSE E.node1 END LEFT JOIN ".$species."_Genes AS GN ON GN.id = CASE WHEN E.node1 = G.id THEN E.node2 ELSE E.node1 END LEFT JOIN ".$species."_Annotation AS AN ON AN.id = CASE WHEN E.node1 = G.id THEN E.node2 ELSE E.node1 END  WHERE G.name IN (";

//	$pre_query = "SELECT G.name AS `source`,GN.id,GN.name AS `name`,E.adjacency,M.modular_k_rank,M.modular_k, M.modular_mean_exp_rank,M.module,M.k,M.k_rank,M.mean_exp,M.mean_exp_rank FROM " . $genes . " AS G LEFT JOIN ".$species."_Adjacency AS A ON A.id = G.id LEFT JOIN ".$species."_Edges AS E ON E.edgeId = A.edgeId LEFT JOIN ".$metrics." AS M ON M.id = CASE WHEN E.node1 = G.id THEN E.node2 ELSE E.node1 END LEFT JOIN ".$genes." AS GN ON GN.id = CASE WHEN E.node1 = G.id THEN E.node2 ELSE E.node1 END WHERE G.name IN (";


}else{
	echo "Invalid species used, please try again";	
	exit();
}
//Should this be loaded as a CSV or not?
$csv = false;
$gn = 0;
foreach ($_GET as $key => $value) {
	if($key[0] == "g"){
		if(!preg_match($validGenes,$value,$match)){
			echo "Invalid gene used, please try again";	
			exit();
		}
		$pre_query.=("'" . $match[0] . "',");
		$gn++;		
	}else if($key == "type"){
		if($value == "AND"){
			$AND = True;
		}
	}else if($key == "csv"){
		$csv = true;
	}
}
//Prepare and execute query, concatenating the pre-query strings
//The substr is used to remove the final ','
$query = $db->prepare(substr($pre_query,0,-1) . ")");
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
		foreach ($results as $row){
			//If the gene has appeared at least once, but
			//no more than twice then we will add it to the indeces table and to the seenTwice table so it isn't readded
			if(!array_key_exists($row['id'],$seen)){
				$seen[$row['id']] = 1;
			}else{
				$seen[$row['id']] = $seen[$row['id']] + 1;
			}
		}
	if(!$csv){
		//Pre table search forms
		echo '<table border="0" cellspacing="5" cellpadding="5">';
	        echo '<tbody><tr>';
	        echo '    <td><b>Filtering: </b></td>';
	        echo '    <td>Column: </td>';
	        echo '    <td><select id="filterChoice">';
	        echo '    <option value="2">Adjacency Value</option>';
	        echo '    <option value="3">Mean Exp</option>';
	        echo '    <option value="4">Mean Exp Rank</option>';
	        echo '    <option value="5">K</option>';
	        echo '    <option value="6">K Rank</option>';
	        echo '    <option value="7">Module</option>';
	        echo '    <option value="8">Modular K</option>';
	        echo '    <option value="9">Modular K Rank</option>';
	        echo '    <option value="10">Modular Mean Exp Rank</option>';
	        echo '    <option value="11">Connections</option>';
	        echo '    </select></td>';
	        echo '    <td>Minimum: </td>';
	        echo '    <td><input type="text" id="min" name="min"></td>';
	        echo '    <td>Maximum: </td>';
	        echo '    <td><input type="text" id="max" name="max"></td>';
	        echo '</tr>';
	    	echo ' </tbody></table>	';
	
		echo "<a id='getCSV' href='" .$_SERVER["REQUEST_URI"] ."&csv=true' download='kek.csv'>Download table as CSV with annotations</a>";
		//Initialize table
		echo "<table id='basicQueryTable' style='width:100%'>";
	    		echo "<thead>";
	    		echo "<th>Gene</th>";
			if ($gn > 1){
	    		echo "<th>Source</th>";
			}
	    		echo "<th>Adjacency Value</th>";
	    		echo "<th>Mean Exp</th>";
	    		echo "<th>Mean Exp Rank</th>";
	    		echo "<th>K</th>";
	    		echo "<th>K Rank</th>";
	    		echo "<th>Module</th>";
	    		echo "<th>Modular K</th>";
	    		echo "<th>Modular K Rank</th>";
	    		echo "<th>Modular Mean Exp Rank</th>";
			if ($gn > 1){
	    		echo "<th>Connections</th>";
			}
	    		echo "</tr>";
	    		echo "</thead>";
			echo "<tfoot></tfoot>";
	    		echo "<tbody>";
	
		//Loop through initial results, perform subquery for Metrics and create the table
		$pos = 0;
		foreach ($results as $row) {
			if($AND){
				//Skip anything with less than two genes for the AND query
				if ($seen[$row['id']] < 2){
					continue;
				}
			}
			//Echo the table 
	    		echo "<tr>";
	    		echo "<td class=popup value=?link=true&spec=". $species ."&gene=". $row['name'] . ">" . $row['name'] . "</td>";
			if ($gn > 1){
	    		echo "<td class=popup value=?link=true&spec=". $species ."&gene=". $row['source'] . ">" . $row['source'] . "</td>";
			}
	    		echo "<td>" . $row['adjacency'] . "</td>";
	    		echo "<td>" . $row['mean_exp'] . "</td>";
	    		echo "<td>" . $row['mean_exp_rank'] . "</td>";
	    		echo "<td>" . $row['k'] . "</td>";
	    		echo "<td>" . $row['k_rank'] . "</td>";
	    		echo "<td>" . $row['module'] . "</td>";
	    		echo "<td>" . $row['modular_k'] . "</td>";
	    		echo "<td>" . $row['modular_k_rank'] . "</td>";
	    		echo "<td>" . $row['modular_mean_exp_rank'] . "</td>";
			if ($gn > 1){
	    		echo "<td>" . $seen[$row['id']] . "</td>";
			}
	    		echo "</tr>";
			}
	    	echo "</tbody>";
		echo "</table>";
	}else{
		//Generate a CSV file
		header( 'Content-Type: text/csv' );
           	header( 'Content-Disposition: attachment;filename=result.csv');
            	$fp = fopen('php://output', 'w');
		//Write the header column
		fputcsv($fp, array("target","source","adjacency","mean_exp","mean_exp_rank","k","k_rank","module","modular_k","modular_k_rank","modular_mean_exp_rank","connections","name","description"));
		foreach ($results as $row) {
			if($AND){
				//Skip anything with less than two genes for the AND query
				if ($seen[$row['id']] < 2){
					continue;
				}
			}
			//Write the actual info for each line
			fputcsv($fp, array($row['name'],$row['source'],$row['adjacency'],$row['mean_exp'],$row['mean_exp_rank'],$row['k'],$row['k_rank'],$row['module'],$row['modular_k'],$row['modular_k_rank'],$row['modular_mean_exp_rank'],$seen[$row['id']],$row["name"],$row["description"]));
		
		}
		fclose($fp);
	}
}


//Close connection
$db=null;
?>


