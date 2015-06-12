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
    		echo "<td class=popup value=?link=true&spec=". $species ."&gene=". $row['name'] . ">" . $row['name'] . "</td>";
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
