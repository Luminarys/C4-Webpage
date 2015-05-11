<html>
<body>
<?php 

//Set debugging on
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//Initialize Server
$server = "racetrack.ddpsc.org";
$user = "MocklerWeb";
$password = "MocklerWebPassw0rd";
$DB = "Maize_Sorghum"; 

$conn = new mysqli($server, $user, $password, $DB);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo "Connected to: " . $conn->host_info . "\n";


echo "<p>Basic Gene Query:</p>";

echo $_POST["gene"]; 



?>

</body>
</html>

