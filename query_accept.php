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

<!-- Local JS file -->
<script type="text/javascript" charset="utf8" src="js/main.js"></script>

</head>
<body>

<?php 

//Set debugging on
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Initialize Server
$db = new PDO('mysql:host=racetrack.ddpsc.org;dbname=C4;charset=utf8', 'MocklerWeb', 'MocklerWebPassw0rd');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Process user input
$gene = $_GET["gene"];

//Prepare and execute query
$query = $db->prepare("SELECT * FROM Zmays_Adj WHERE gene_id_A = ? OR gene_id_B = ?");
if($query->execute(array($gene, $gene))){
	$results = $query->fetchAll();
	$rows = $query->rowCount();

	//Prepare the second query for extracting data from Zmays_Metrics
	$queryT2 = $db->prepare("SELECT * FROM Zmays_Metrics WHERE gene_id = ?");

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
	echo "<table id='basicQuery' style='width:100%'>";
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
	foreach ($results as $row) {

		//Search of gene_id_B if gene_id_A is equal to the queried gene and vice versa
		if ($row['gene_id_A'] == $gene){
			$queryT2->execute(array($row['gene_id_B']));
			$outGene = $row['gene_id_B'];
		}else{
			$queryT2->execute(array($row['gene_id_A']));
			$outGene = $row['gene_id_A'];
		}
		$metrics = $queryT2->fetchAll();

		//Echo the table 
    		echo "<tr>";
    		echo "<td>" . $outGene . "</td>";
    		echo "<td>" . $row['value'] . "</td>";
    		echo "<td>" . $metrics[0]['mean_exp'] . "</td>";
    		echo "<td>" . $metrics[0]['mean_exp_rank'] . "</td>";
    		echo "<td>" . $metrics[0]['k'] . "</td>";
    		echo "<td>" . $metrics[0]['k_rank'] . "</td>";
    		echo "<td>" . $metrics[0]['module'] . "</td>";
    		echo "<td>" . $metrics[0]['modular_k'] . "</td>";
    		echo "<td>" . $metrics[0]['modular_k_rank'] . "</td>";
    		echo "<td>" . $metrics[0]['modular_mean_exp_rank'] . "</td>";
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

