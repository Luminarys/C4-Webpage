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
<script type="text/javascript" charset="utf8" src="js/script.js"></script>
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

	$queryT2 = $db->prepare("SELECT * FROM Zmays_Metrics WHERE gene_id = ?");

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
    		echo "<tbody>";
	//Print the table, doing a query each time
	foreach ($results as $row) {
		if ($row['gene_id_A'] == $gene){
			$queryT2->execute(array($row['gene_id_B']));
			$outGene = $row['gene_id_B'];
		}else{
			$queryT2->execute(array($row['gene_id_A']));
			$outGene = $row['gene_id_A'];
		}
    		echo "<tr>";
		$metrics = $queryT2->fetchAll();
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

