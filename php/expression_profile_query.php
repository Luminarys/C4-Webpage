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
$species;
$AND = False;

// Function to calculate square of value - mean
function sd_square($x, $mean) { return pow($x - $mean,2); }

//Function to compute the standard dev.
function sd($array) {
// square root of sum of squares devided by N-1
return sqrt(array_sum(array_map("sd_square", $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) );
}

//Function to get the pearson correlation coefficient of two samples
function computePearson($comp, $samp, $sd1, $mean1){
	$sd2 = sd($comp);
	$mean2 = array_sum($comp)/count($comp);
	$covar = 0;
	for($i = 0; $i < count($comp); $i++){
		$covar += (($comp[$i] - $mean2) * ($samp[$i] - $mean1));	
	}
	$sdc = $sd1 * $sd2;
	return $covar/$sdc;
}

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

$samples = array();
foreach ($_GET as $key => $value) {
	if($key[0] == "s" && is_numeric($key[1])){
		array_push($samples, intval($value));
	}
}
$sd = sd($samples);
$mean = array_sum($samples)/count($samples);
$cutoff = intval($_GET['maxres']);
$correlation = intval($_GET['r']);
if (!ctype_digit($_GET['emin']) || !ctype_digit($_GET['emin'])){
	echo "Invalid parameters used, please try again";
	exit();
}
$emin = intval($_GET['emin']);
$emax = intval($_GET['emax']);
$species = $_GET['spec'];
if(!in_array($species,$validSpecies)){
	echo "Invalid SQL Query used, please try again";
	exit();
}else{
	$pre_query = sprintf("SELECT * FROM %s_Genes as genes INNER JOIN %s_Expression as expr ON expr.id = genes.id LEFT JOIN %s_Metrics as metrics on metrics.id = genes.id WHERE metrics.mean_exp >= %d AND metrics.mean_exp <= %d", $species, $species, $species, $emin, $emax);
}
//Prepare and execute query, concatenating the pre-query strings
//The substr is used to remove the final ','
//echo $pre_query . $pre_query_a . $pre_query_b . $pre_query_c . substr($pre_query_d,0,-1) . ")";
$query = $db->prepare($pre_query);
$sample_query = $db->prepare("Select * FROM " . $species . "_Expression_Map ORDER BY biosample_id");
if($query->execute() && $sample_query->execute()){

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
	$layout = $sample_query->fetchAll();
	$results = $query->fetchAll();
	$rows = $query->rowCount();
	$res = array();
	$data = array();
	foreach ($results as $row) {
		$data = array();
		$carray = array();
		//Loop through the layout for each result, getting the mean of each sample and pushing it into an array
		//Use biosample_id to determine where each sample starts/stops
		$name = $layout[0]['field_name'];
		$bio_id = $layout[0]['biosample_id'];
		foreach ($layout as $sample) {
			//If there's a different bioID then prev, set the array within the JSON for the sample equal to cArray
			//then clear $carray and update bio_id and name(the prev. sample name)
			if ($sample['biosample_id'] != $bio_id){
				array_push($data, array_sum($carray)/count($carray));
				$bio_id = $sample['biosample_id'];
				$name = $sample['field_name'];
				$carray = array();
			}
			array_push($carray, $row[$name]);
		}
		$r = computePearson($data, $samples, $sd, $mean);
		if($r > $correlation){
			array_push($res, $row);
		}
	}
	for($i = 0; $i < $cutoff && $i < count($res); $i++){
    		echo "<td class=popup value=?link=true&spec=". $species ."&gene=". $res[$i]['name'] . ">" . $res[$i]['name'] . "</td>";
    		echo "<td>" . $res[$i]['mean_exp'] . "</td>";
    		echo "<td>" . $res[$i]['mean_exp_rank'] . "</td>";
    		echo "<td>" . $res[$i]['k'] . "</td>";
    		echo "<td>" . $res[$i]['k_rank'] . "</td>";
    		echo "<td>" . $res[$i]['modular_k'] . "</td>";
    		echo "<td>" . $res[$i]['modular_k_rank'] . "</td>";
    		echo "<td>" . $res[$i]['modular_mean_exp_rank'] . "</td>";
    		echo "</tr>";
	}
    	echo "</tbody>";
	echo "</table>";
}


//Close connection
$db=null;
?>
