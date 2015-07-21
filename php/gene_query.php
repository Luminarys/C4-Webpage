<?php 

//Set debugging on
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

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
		$validGenes .= $spec["regex"] . "|";
	}
}else{
	echo "Warning, no MapReference table defined, exiting";
	exit();
}
$validGenes = "/" . substr($validGenes, 0, -1) . "/";

$species = $_GET['spec'];
$expressionOption = true;
if(array_key_exists("noex", $_GET)){
	$expressionOption = false;
}
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
	}else if($key == "type"){
		if($value == "AND"){
			$AND = True;
		}
	}else if($key == "csv"){
		$csv = true;
	}else if($key == "network"){
		$graph = true;
	}
}

function outputFilter($num) {
	echo '<tr>';
	echo '    <td>Column: </td>';
	echo '    <td><select id="filterChoice' . $num . '">';
	if ($gn > 1){
		echo '    <option field=' .'"adjacency"' . 'value="2">Adjacency Value</option>';
	 	echo '    <option field=' . '"mean_exp"' . 'value="3">Mean Exp</option>';
	 	echo '    <option field=' . '"mean_exp_rank"' . 'value="4">Mean Exp Rank</option>';
	 	echo '    <option field=' . '"k"' . 'value="5">K</option>';
	 	echo '    <option field=' . '"k_ranl' . 'value="6">K Rank</option>';
	 	echo '    <option field=' . '"module"' . 'value="7">Module</option>';
	 	echo '    <option field=' . '"modular_k"' . 'value="8">Modular K</option>';
	 	echo '    <option field=' . '"modular_k_rank"' . 'value="9">Modular K Rank</option>';
	 	echo '    <option field=' . '"modular_mean_exp_rank"' . 'value="10">Modular Mean Exp Rank</option>';
	 }else{
	 	echo '    <option field=' . '"adjacency"' . 'value="1">Adjacency Value</option>';
	 	echo '    <option field=' . '"mean_exp"' . 'value="2">Mean Exp</option>';
	 	echo '    <option field=' . '"mean_exp_rank"' . 'value="3">Mean Exp Rank</option>';
	 	echo '    <option field=' . '"k"' . 'value="4">K</option>';
	 	echo '    <option field=' . '"k_rank"' . 'value="5">K Rank</option>';
	 	echo '    <option field=' . '"module"' . 'value="6">Module</option>';
	 	echo '    <option field=' . '"modular_k"' . 'value="7">Modular K</option>';
	 	echo '    <option field=' . '"modular_k_rank"' . 'value="8">Modular K Rank</option>';
	 	echo '    <option field=' . '"modular_mean_exp_rank"' . 'value="9">Modular Mean Exp Rank</option>';
	}
	echo '    </select></td>';
	echo '    <td>Minimum: </td>';
	echo '    <td><input type="text" id="min' . $num . '" name="min"></td>';
	echo '    <td>Maximum: </td>';
	echo '    <td><input type="text" id="max' . $num . '" name="max"></td>';
	//echo ' 	  <td><select id="invertChoice"><option value="false">Within Range</option><option value="true">Outside of Range</option></select></td>';
	echo '</tr>';
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
		if($expressionOption){
		        echo ' <br>   <b>Filtering: </b><br>';
		        echo '<button id="networkGraph" onclick="">Create network graph based on filtering settings</button>';
			echo '<table border="0" cellspacing="5" cellpadding="5" align="center">';
		        echo '<tbody>';
			outputFilter("");
			outputFilter("2");
			outputFilter("3");
		    	echo ' </tbody></table>	';
			echo "<button id='getCSV' url='" .$_SERVER["REQUEST_URI"] ."&csv=true'>Download table as CSV with annotations</button>";
		}
		//Initialize table
		echo "<form id='geneSelections'>";
		echo "<table id='basicQueryTable' style='width:100%'>";
	    		echo "<thead>";
			if ($gn > 1){
	    			echo "<th class='minfo' value='A node that was found in a resulting edge from the query.'>Source</th>";
			}
	    		echo "<th class='minfo' value='The other node in the edge query(will always be in the queried genes list).'>Gene</th>";
	    		echo "<th class='minfo' value='The Pearson correlation value between the two nodes of the edge.'>Adjacency Value</th>";
	    		echo "<th class='minfo' value='The average within-dataset expression for the Target Node. This value is computed directly from the input expression values.'>Mean Exp</th>";
	    		echo "<th class='minfo' value='The rank (highest first) of the Target Node's mean  expression, in a list of all genes in the data background.'>Mean Exp Rank</th>";
	    		echo "<th class='minfo' value='The connectivity of the Target Node in the entire GCN of the given data background. This is the sum of the edge strengths in the network which involve the Target Node.'>K</th>";
	    		echo "<th class='minfo' value='The rank (highest first) of the connectivity (K) of the Target Node, among all nodes in the given data background.'>K Rank</th>";
	    		echo "<th class='minfo' value='The Module of which the Target Node is a member. For each GCN and data background, all nodes ar members of zero or one Modules.'>Module</th>";
	    		echo "<th class='minfo' value='The connectivity of the Target Node when only edges in which the non-Target Node is a member of the same module as the Target Node are included in the edge strength summation.'>Modular K</th>";
	    		echo "<th class='minfo' value='The rank (highest first) of a Target Node's Modular K, among all of the genes of the given Module.  It has been shown that Nodes which are high ranking in Modular Connectivity often play critical roles in the function assigned to that module via functional term enrichment analysis.'>Modular K Rank</th>";
	    		echo "<th class='minfo' value='The  rank (highest first) of the mean expression of the Target Node, among nodes which are members of the same module as the Target Node. Utilization of this value,in conjunction with functional term enrichment analysis, has been shown to increase the hit rate of a targeted gene candidate screen by as much as 48-fold'>Modular Mean Exp Rank</th>";
			if ($gn > 1){
	    			echo "<th class='minfo' value='Gene'>Connections</th>";
			}
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
			if ($gn > 1){
	    			echo "<td class=popup value=?link=true&spec=". $species ."&gene=". $row['source'] . ">" . $row['source'] . "</td>";
			}
	    		echo "<td class=popup value=?link=true&spec=". $species ."&gene=". $row['name'] . ">" . $row['name'] . "</td>";
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
		if(array_key_exists("max",$_GET)){
			$filterMax = floatval($_GET["max"]);
			$filterMin = floatval($_GET["min"]);
			$filterTarget = $_GET["field"];
		}
		if(array_key_exists("max2",$_GET)){ $filterMax2 = floatval($_GET["max2"]);
			$filterMin2 = floatval($_GET["min2"]);
			$filterTarget2 = $_GET["field2"];
		}
		if(array_key_exists("max3",$_GET)){
			$filterMax3 = floatval($_GET["max3"]);
			$filterMin3 = floatval($_GET["min3"]);
			$filterTarget3 = $_GET["field3"];
		}
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
				if(array_key_exists("max",$_GET)){
					if($row[$filterTarget] > $filterMax || $row[$filterTarget] < $filterMin){
						continue;
					}
				}
				if(array_key_exists("max2",$_GET)){
					if($row[$filterTarget2] > $filterMax2 || $row[$filterTarget2] < $filterMin2){
						continue;
					}
				}
				if(array_key_exists("max3",$_GET)){
					if($row[$filterTarget3] > $filterMax3 || $row[$filterTarget3] < $filterMin3){
						continue;
					}
				}
				fputcsv($fp, array($row['name'],$row['source'],$row['adjacency'],$row['mean_exp'],$row['mean_exp_rank'],$row['k'],$row['k_rank'],$row['module'],$row['modular_k'],$row['modular_k_rank'],$row['modular_mean_exp_rank'],$seen[$row['id']],$row["aname"],$row["description"]));
			}
		}
		fclose($fp);
	}else if($graph){
		//Track the maximum number of edges any one node has
		//This will be used for generating the legend
		$max = 1;
		$ret = array();
		//Nodes will have info about the gene
		$ret["nodes"] = array();
		//Edges will list all edges
		$ret["edges"] = array(); 
		
		//Links a gene name with an position
		$indeces = array();
		$index = 0;
		//Insert in the sources
		foreach ($sources as $source){
			$indeces[$source] = $index;
			array_push($ret["nodes"],array("name"=>$source, "group"=>0));
			$index++;
		}
		$eindex = 0;
		//Filtering parameters, derived from the Network Query Table
		$filterMax = $_GET["max"];
		$filterMin = $_GET["min"];
		$filterTarget = $_GET["field"];
		if(array_key_exists("max2",$_GET)){ $filterMax2 = floatval($_GET["max2"]);
			$filterMin2 = floatval($_GET["min2"]);
			$filterTarget2 = $_GET["field2"];
		}
		if(array_key_exists("max3",$_GET)){
			$filterMax3 = floatval($_GET["max3"]);
			$filterMin3 = floatval($_GET["min3"]);
			$filterTarget3 = $_GET["field3"];
		}
		//Generate a JSON which has 3 dicts - nodes, edges, and max
		//Nodes will consist of a list of nodes with fields [Name(the gene ID), and Group(number of edges)
		//Edges will consist of a list with the Source ID, target ID, and adjacency value
		//Max will be the node with the maximum number of edges, used to generate the legend in the Network Graph
		foreach ($results as $row){
			if($row[$filterTarget] > $filterMax || $row[$filterTarget] < $filterMin){
				continue;
			}
			if(array_key_exists("max2",$_GET)){
				if($row[$filterTarget2] > $filterMax2 || $row[$filterTarget2] < $filterMin2){
					continue;
				}
			}
			if(array_key_exists("max3",$_GET)){
				if($row[$filterTarget3] > $filterMax3 || $row[$filterTarget3] < $filterMin3){
					continue;
				}
			}
			$increment = false;
			if(!array_key_exists($row["name"], $indeces)){
				$indeces[$row["name"]] = $index;
				array_push($ret["nodes"],array("name"=>$row["name"], "group"=>1));
				$increment = true;
			}else{
				//Keep the Source nodes the same color, everything else should be incremented to indicate how many connections a node has
				if(!in_array($row["name"], $sources)){
					$ret["nodes"][$indeces[$row["name"]]]["group"]++;
					//update maximum
					if($ret["nodes"][$indeces[$row["name"]]]["group"] > $max){
						$max = $ret["nodes"][$indeces[$row["name"]]]["group"];
					}
				}
			}
			array_push($ret["edges"], array("source"=>$indeces[$row["source"]], "target"=>$indeces[$row["name"]], "value"=> $row['adjacency']));
			if($increment){
				$index++;
			}
			$eindex++;
		}
		$ret['max'] = $max;
		echo json_encode($ret);
	}
}


//Close connection
$db=null;
?>
