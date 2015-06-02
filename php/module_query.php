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
$dbInit = 'mysql:host=' . substr($auth[0],0,-1) . ';dbname=' . substr($auth[3],0,-1) .';charset=utf8';
$db = new PDO($dbInit, substr($auth[1],0,-1), substr($auth[2],0,-1));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

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

//Build and execute query
if(!in_array($_GET["spec"], $validSpecies)){
	echo "Invalid SQL query, please try again";	
	exit();
}
//Example query
//SELECT * FROM Zmays_Clusters_Genes LEFT JOIN Zmays_Metrics ON Zmays_Metrics.id = Zmays_Clusters_Genes.id LEFT JOIN Zmays_Genes ON Zmays_Genes.id = Zmays_Clusters_Genes.id WHERE Cluster="5";
$species = $_GET["spec"];
$cluster = $species . "_Clusters_Genes";
$query = $db->prepare("SELECT * FROM " . $cluster . " LEFT JOIN " . $species . "_Metrics ON " . $species . "_Metrics.id = " . $cluster . ".id LEFT JOIN " . $species . "_Genes ON " . $species . "_Genes.id = " . $cluster . ".id WHERE Cluster = ?");
if($query->execute(array($_GET["module"]))){

	//Pre table search forms
	echo '<table border="0" cellspacing="5" cellpadding="5">';
        echo '<tbody><tr>';
        echo '    <td><b>Filtering: </b></td>';
        echo '    <td>Column: </td>';
        echo '    <td><select id="filterChoice">';
        echo '    <option value="1">Mean Exp</option>';
        echo '    <option value="2>Mean Exp Rank</option>';
        echo '    <option value="3">K</option>';
        echo '    <option value="4">K Rank</option>';
        echo '    <option value="5">Modular K</option>';
        echo '    <option value="6">Modular K Rank</option>';
        echo '    <option value="7">Modular Mean Exp Rank</option>';
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
    		echo "<th>Mean Exp</th>";
    		echo "<th>Mean Exp Rank</th>";
    		echo "<th>K</th>";
    		echo "<th>K Rank</th>";
    		echo "<th>Modular K</th>";
    		echo "<th>Modular K Rank</th>";
    		echo "<th>Modular Mean Exp Rank</th>";
    		echo "</tr>";
    		echo "</thead>";
		echo "<tfoot></tfoot>";
    		echo "<tbody>";
	//Get the results
	$results = $query->fetchAll();
	$rows = $query->rowCount();


	$pos = 0;
	foreach ($results as $row) {
		//Echo the table 
    		echo "<tr>";
    		echo "<td>" . $row['name'] . "</td>";
    		echo "<td>" . $row['mean_exp'] . "</td>";
    		echo "<td>" . $row['mean_exp_rank'] . "</td>";
    		echo "<td>" . $row['k'] . "</td>";
    		echo "<td>" . $row['k_rank'] . "</td>";
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
