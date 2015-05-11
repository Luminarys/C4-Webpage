<html>
<body>

<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
</style>

<?php 

//Set debugging on
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Initialize Server
$db = new PDO('mysql:host=racetrack.ddpsc.org;dbname=C4;charset=utf8', 'MocklerWeb', 'MocklerWebPassw0rd');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Process user input
$gene = $_POST["gene"];

echo "<p>Basic Gene Query:</p>";

echo $gene . "<br>"; 

//Prepare and execute query
$query = $db->prepare("SELECT * FROM Zmays_Adj WHERE gene_id_A = ? OR gene_id_B = ?");
if($query->execute(array($gene, $gene))){
	$results = $query->fetchAll();
	$rows = $query->rowCount();
	echo "There are " . $rows . " results<br>";

	$queryT2 = $db->prepare("SELECT * FROM Zmays_Metrics WHERE gene_id = ?");

	//Initialize table
	echo "<table style='width:100%'>";
    		echo "<tr>";
    		echo "<td>Gene</td>";
    		echo "<td>Adjacency Value</td>";
    		echo "<td>Mean Exp</td>";
    		echo "<td>Mean Exp Rank</td>";
    		echo "<td>K</td>";
    		echo "<td>K Rank</td>";
    		echo "<td>Module</td>";
    		echo "<td>Modular K</td>";
    		echo "<td>Modular K Rank</td>";
    		echo "<td>Modular Mean Exp Rank</td>";
    		echo "</tr>";
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
	echo "</table>";
}


//Close connection
$db=null;
?>

</body>
</html>

