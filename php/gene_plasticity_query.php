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
		if($spec["regex"][0] == "(" && $spec["regex"][strlen($spec["regex"]) - 1] != ")"){
			$validGenes .= $spec["regex"] . ")|";
		}else{
			$validGenes .= $spec["regex"] . "|";
		}
	}
}else{
	echo "Warning, no MapReference table defined, exiting";
	exit();
}
$validGenes = "/" . substr($validGenes, 0, -1) . "/";

$query = $db->prepare("SELECT * FROM MapCompare");
if($query->execute()){
	$result = $query->fetchAll();
	foreach ($result as $spec){
		array_push($validSpecies, $spec["prefix"]);
	}
}else{
	echo "Warning, no MapCompare table defined, exiting";
	exit();
}
$orig = $_GET['orig'];
$target = $_GET['target'];
$comp = $_GET['comp'];
$expressionOption = false;
if(array_key_exists("noex", $_GET)){
	$expressionOption = false;
}
$csv;
if(in_array($orig,$validSpecies) && in_array($target,$validSpecies) && in_array($comp, $validSpecies)){
	//This is pretty complex - Basically the query utilizes a UNION and a lot of LEFT JOINs to jam a bunch of tables together and select proper value.
	//Most important is the () res section which essentially finds the gene id and adjacency value for all edges attached to the input gene
	//The UNION is used because there are cases in which node1 = input gene and others where node2 = input gene. We need those instances to be filtered
	//so a UNION of situations where Zmays_Edges.node1 != Zmays_Adjacency.id and likewise with node2 is necessary.
	//Res is then used to join the metrics and gene name tables

	$pre_query = "SELECT 
	G.name AS `source`,
	GN.id,
	GN.name AS `name`, 
	G2.name AS `alt_source_name`,
	G3.name AS `alt_name`,
	AN.description as desc1, 
	AN.name as aname1, 
	AN2.description as desc2, 
	AN2.name as aname2, 
	AN3.description as desc3, 
	AN3.name as aname3, 
	E.adjacency,
	M.k,
	M.k_rank,
	M.neg_modular_k,
	M.neg_modular_k_rank,
	M.pos_modular_k,
	M.pos_modular_k_rank,
	M.neg_modular_mean_exp_rank_1,
	M.neg_modular_mean_exp_rank_2,
	M.pos_modular_mean_exp_rank_1,
	M.pos_modular_mean_exp_rank_2,
	M.neg_module,
	M.pos_module,
	M.exp_mean_1,
	M.exp_mean_2,
	M.exp_rank_1,
	M.exp_rank_2 
	FROM " . $orig . "_Genes AS G 
	LEFT JOIN " . $comp . "_Adjacency AS A 
	ON A.id = G.id 
	LEFT JOIN " . $comp . "_Edges AS E 
	ON E.edgeId = A.edgeId 
	LEFT JOIN " . $comp . "_Metrics AS M 
	ON M.id = 
	CASE WHEN E.node1 = G.id THEN E.node2 
	ELSE E.node1 END 
	LEFT JOIN " . $orig . "_Genes AS GN 
	ON GN.id = 
	CASE WHEN E.node1 = G.id THEN E.node2 
	ELSE E.node1 END 
	LEFT JOIN " . $orig . "_Annotation AS AN 
	ON AN.id = 
	CASE WHEN E.node1 = G.id THEN E.node2 
	ELSE E.node1 END 
	LEFT JOIN " . $target . "_Genes AS G2
	ON G2.id = E.alt_node1
	LEFT JOIN ". $target . "_Genes AS G3
	ON G3.id = E.alt_node2
	LEFT JOIN " . $target . "_Annotation AS AN2
	ON AN2.id = E.alt_node1
	LEFT JOIN " . $target . "_Annotation AS AN3
	ON AN3.id = E.alt_node2
	WHERE G.name IN (";

//	$pre_query = "SELECT G.name AS `source`,GN.id,GN.name AS `name`,E.adjacency,M.modular_k_rank,M.modular_k, M.modular_mean_exp_rank,M.module,M.k,M.k_rank,M.mean_exp,M.mean_exp_rank FROM " . $genes . " AS G LEFT JOIN ".$species."_Adjacency AS A ON A.id = G.id LEFT JOIN ".$species."_Edges AS E ON E.edgeId = A.edgeId LEFT JOIN ".$metrics." AS M ON M.id = CASE WHEN E.node1 = G.id THEN E.node2 ELSE E.node1 END LEFT JOIN ".$genes." AS GN ON GN.id = CASE WHEN E.node1 = G.id THEN E.node2 ELSE E.node1 END WHERE G.name IN (";


}else{
	echo "Invalid species used, please try again";	
	exit();
}
//Should this be loaded as a CSV or not?
$csv = false;
$gn = 0;
$graph = false;
$sources = array();
foreach ($_GET as $key => $value) {
	if($key[0] == "g"){
		if(!preg_match($validGenes,$value,$match)){
			echo "Invalid gene used, please try again";	
			exit();
		}
		$pre_query.=("'" . $match[0] . "',");
		$gn++;		
		array_push($sources, $match[0]);
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
	if(!$csv && !$graph){
		//Pre table search forms
		echo '<table border="0" cellspacing="5" cellpadding="5">';
		echo '<tbody><tr>';
		echo '    <td><b>Filtering: </b></td>';
		echo '    <td>Column: </td>';
		echo '    <td><select id="filterChoice">';
		echo '    <option value="4">Adjacency Value</option>';
		echo '    <option value="5">K</option>';
		echo '    <option value="6">K Rank</option>';
		echo '    </select></td>';
		echo '    <td>Minimum: </td>';
		echo '    <td><input type="text" id="min" name="min"></td>';
		echo '    <td>Maximum: </td>';
		echo '    <td><input type="text" id="max" name="max"></td>';
		echo ' 	  <td><select id="invertChoice"><option value="false">Within Range</option><option value="true">Outside of Range</option></select></td>';
		echo '</tr>';
		echo ' </tbody></table>	';
		//Initialize table
		echo "<form id='geneSelections'>";
		echo "<table id='basicQueryTable' style='width:100%'>";
	    		echo "<thead>";
	    		echo "<th class='minfo' value='A node that was found in a resulting edge from the query.'>Source</th>";
	    		echo "<th class='minfo' value='A node that was found in a resulting edge from the query.'>Alt Source</th>";
	    		echo "<th class='minfo' value='The other node in the edge query(will always be in the queried genes list).'>Gene</th>";
	    		echo "<th class='minfo' value='The other node in the edge query(will always be in the queried genes list).'>Alt Gene</th>";
	    		echo "<th class='minfo' value='The Pearson correlation value between the two nodes of the edge.'>Adjacency Value</th>";
	    		echo "<th class='minfo' value='The connectivity of the Target Node in the entire GCN of the given data background. This is the sum of the edge strengths in the network which involve the Target Node.'>K</th>";
	    		echo "<th class='minfo' value='The rank (highest first) of the connectivity (K) of the Target Node, among all nodes in the given data background.'>K Rank</th>";
			echo "<th>neg_modular_k</th>";
			echo "<th>neg_modular_k_rank</th>";
			echo "<th>pos_modular_k</th>";
			echo "<th>pos_modular_k_rank</th>";
			echo "<th>neg_modular_mean_exp_rank_1</th>";
			echo "<th>neg_modular_mean_exp_rank_2</th>";
			echo "<th>pos_modular_mean_exp_rank_1</th>";
			echo "<th>pos_modular_mean_exp_rank_2</th>";
			echo "<th>neg_module</th>";
			echo "<th>pos_module</th>";
			echo "<th>neg_exp_mean_1</th>";
			echo "<th>pos_exp_mean_2</th>";
			echo "<th>neg_exp_rank_1</th>";
			echo "<th>pos_exp_rank_2</th>";
			if($expressionOption){
	    			echo "<th class='minfo' value='Gene'>Expression Selection</th>";
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
				if (!in_array($row['id'], $sources)){
					continue;
				}
			}
			if($row['name'] == ""){
				continue;
			}
			//Echo the table 
	    		echo "<tr>";
	    		echo "<td class=popup value=?link=true&spec=". $orig ."&gene=". $row['source'] . ">" . $row['source'] . "</td>";
	    		echo "<td class=popup value=?link=true&spec=". $orig ."&gene=". $row['alt_source_name'] . ">" . $row['alt_source_name'] . "</td>";
	    		echo "<td class=popup value=?link=true&spec=". $orig ."&gene=". $row['name'] . ">" . $row['name'] . "</td>";
	    		echo "<td class=popup value=?link=true&spec=". $orig ."&gene=". $row['alt_name'] . ">" . $row['alt_name'] . "</td>";
	    		echo "<td>" . $row['adjacency'] . "</td>";
	    		echo "<td>" . $row['k'] . "</td>";
	    		echo "<td>" . $row['k_rank'] . "</td>";
	    		echo "<td>" . $row['neg_modular_k'] . "</td>";
	    		echo "<td>" . $row['neg_modular_k_rank'] . "</td>";
	    		echo "<td>" . $row['pos_modular_k'] . "</td>";
	    		echo "<td>" . $row['pos_modular_k_rank'] . "</td>";
	    		echo "<td>" . $row['neg_modular_mean_exp_rank_1'] . "</td>";
	    		echo "<td>" . $row['neg_modular_mean_exp_rank_2'] . "</td>";
	    		echo "<td>" . $row['pos_modular_mean_exp_rank_1'] . "</td>";
	    		echo "<td>" . $row['pos_modular_mean_exp_rank_2'] . "</td>";
	    		echo "<td>" . $row['neg_module'] . "</td>";
	    		echo "<td>" . $row['pos_module'] . "</td>";
	    		echo "<td>" . $row['exp_mean_1'] . "</td>";
	    		echo "<td>" . $row['exp_mean_2'] . "</td>";
	    		echo "<td>" . $row['exp_rank_1'] . "</td>";
	    		echo "<td>" . $row['exp_rank_2'] . "</td>";
			if($expressionOption){
				echo "<td><input type='checkbox' value=" . $row['name'] . "></td>";
			}
	    		echo "</tr>";
		}
	    	echo "</tbody>";
		echo "</table>";
		echo "</form>";
	}else if($csv){
		//Generate a CSV file
		header( 'Content-Type: text/csv' );
           	header( 'Content-Disposition: attachment;filename=result.csv');
            	$fp = fopen('php://output', 'w');
		//Write the header column
		fputcsv($fp, array("target","source","adjacency","mean_exp","mean_exp_rank","k","k_rank","module","modular_k","modular_k_rank","modular_mean_exp_rank","connections","name","description"));
		foreach ($results as $row) {
			if($AND){
				//Skip anything with less than two genes for the AND query
				if (!in_array($row['id'], $sources)){
					continue;
				}
			}
			//Write the actual info for each line
			//Ensure that only genes which have data are returned.
			if(!$row['name'] == ""){
				fputcsv($fp, array($row['name'],$row['source'],$row['adjacency'],$row['mean_exp'],$row['mean_exp_rank'],$row['k'],$row['k_rank'],$row['module'],$row['modular_k'],$row['modular_k_rank'],$row['modular_mean_exp_rank'],$seen[$row['id']],$row["name"],$row["description"]));
			}
		
		}
		fclose($fp);
	}
}


//Close connection
$db=null;
?>
