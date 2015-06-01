<html>

<head>

<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>

<!-- jQuery UI -->  
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- Local JS file -->
<script type="text/javascript" charset="utf8" src="js/bquery.js"></script>

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

$species = $_GET["spec"];
$validSpecies = array("Zmays","Sbicolor","Sitalica");
//Build and execute query
if(!in_array($species, $validSpecies)){
	echo "Invalid SQL query, please try again";	
	exit();
}
$query = $db->prepare("SELECT * FROM " . $species . "_Annotation WHERE locus = ?");
if($query->execute(array($_GET["gene"]))){
	$results = $query->fetchAll();
	$rows = $query->rowCount();
	echo "<p> Gene ID: " . $results[0]['locus'] . "</p>";
	echo "<p> Gene Name: " . $results[0]['name'] . "</p>";
	echo "<p> Gene Description: " . $results[0]['description'] . "</p>"; 
}
//SELECT * FROM (SELECT * FROM Zmays_Genes WHERE Zmays_Genes.name='GRMZM2G001272') res LEFT JOIN Zmays_Metrics ON Zmays_Metrics.id = res.id;
$query = $db->prepare("SELECT * FROM (SELECT * FROM " . $species . "_Genes WHERE " . $species . "_Genes.name  = ? ) res LEFT JOIN " . $species . "_Metrics ON " . $species . "_Metrics.id = res.id");
if($query->execute(array($_GET["gene"]))){
	//Initialize table
	echo "<p> Metrics Table: </p>"; 
	echo "<table id='metricQueryTable' style='width:100%'>";
    		echo "<thead>";
    		echo "<th>Gene</th>";
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

	$results = $query->fetchAll();
	foreach ($results as $row) {
		//Echo the table 
    		echo "<tr>";
    		echo "<td>" . $row['name'] . "</td>";
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
