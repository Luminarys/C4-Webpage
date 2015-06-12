<?php
function write_ini_file($assoc_arr, $path, $has_sections=FALSE) { 
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key." = \n"; 
            else $content .= $key." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
        return false; 
    }

    $success = fwrite($handle, $content);
    fclose($handle); 

    return $success; 
}

//Sanitize input
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// define variables and set to empty values
$name = $copyright = $server = $user = $password = $maindb = $orthodb = "";
$gene_set = $module = $expression = $expression_prof = $ortholog = $annotation = false;

//Process data and write settings file for a POSt request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$name = test_input($_POST["name"]);
	$copyright = test_input($_POST["copyright"]);
	$gene_set = test_input($_POST["gene_set"]);
	$module = test_input($_POST["module"]);
	$expression = test_input($_POST["expression"]);
	$expression_prof = test_input($_POST["expression_prof"]);
	$ortholog = test_input($_POST["ortholog"]);
	$annotation = test_input($_POST["annotation"]);
	$server = test_input($_POST["server"]);
	$user = test_input($_POST["user"]);
	$pssword = test_input($_POST["password"]);
	$maindb = test_input($_POST["maindb"]);
	$orthodb = test_input($_POST["orthodb"]);

	$result = array(
	"Header" => array(
   		$name
	),
	"Footer" => array(
   		$copyright
	),
	"Queries" => array(
  		 $gene_set
  		 $module,
  		 $expression,
  		 $expression_prof,
  		 $ortholog,
  		 $annotation
	),
	"DB" => array(
		$server,
		$user,
		$password,
		$maindb,
		$orthodb
	)
	);
	write_ini_file($result, "./test.ini", true);	
	echo ""
}


?>
