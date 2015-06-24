<html>
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
                        $content .= $key2."[] = ".$elem2[$i]."\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = ".$elem2."\n"; 
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
            else $content .= $key." = ".$elem."\n"; 
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
$name = $github = $copyright = $server = $user = $password = $maindb = $orthodb = "";
$gene_set = $module = $expression = $expression_prof = $ortholog = $annotation = false;

//Process data and write settings file for a POSt request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$name = test_input($_POST["name"]);
	$github = test_input($_POST["github"]);
	$github_home = test_input($_POST["github-home"]);
	$copyright = test_input($_POST["copyright"]);
	$gene_set = test_input($_POST["gene_set"]);
	$module = test_input($_POST["module"]);
	$expression = test_input($_POST["expression"]);
	$expression_prof = test_input($_POST["expression_prof"]);
	$ortholog = test_input($_POST["ortholog"]);
	$annotation = test_input($_POST["annotation"]);
	$server = test_input($_POST["server"]);
	$user = test_input($_POST["user"]);
	$password = test_input($_POST["password"]);
	$maindb = test_input($_POST["maindb"]);
	$orthodb = test_input($_POST["orthodb"]);

	$result = array(
	"Header" => array(
   		"name" => $name,
		"github" => $github
		"github-home" => $github_home
	),
	"Footer" => array(
   		"copyright" => $copyright
	),
	"Queries" => array(
  		 "gene_set" => $gene_set,
  		 "module" => $module,
  		 "expression" => $expression,
  		 "expression_prof" => $expression_prof,
  		 "ortholog" => $ortholog,
  		 "annotation" => $annotation
	),
	"DB" => array(
		"server" => $server,
		"user" => $user,
		"password" => $password,
		"maindb" => $maindb,
		"orthodb" => $orthodb
	)
	);
	if(write_ini_file($result, "./settings.ini", true)){
		echo "Settings saved, please open the website in a new tab to test it, and restrict access to this file and settings.ini";
	}else{
		echo "Warning, could not wrinte to the configuration file";
	}
}
?>
	<h2>PHP Form Validation Example</h2>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		Site Name(used for the header title): <input type="text" name="name">
		<br><br>
		Github URL: <input type="text" name="github-home">
		<br><br>
		Github Issues URL: <input type="text" name="github">
		<br><br>
		Copyright Holder: <input type="text" name="copyright">
		<br><br>
		 Gene Set Query:
   		<input type="radio" name="gene_set" value="true">Enabled
   		<input type="radio" name="gene_set" value="false">Disabled
		<br><br>
		 Module Query:
   		<input type="radio" name="module" value="true">Enabled
   		<input type="radio" name="module" value="false">Disabled
		<br><br>
		 Expression Query:
   		<input type="radio" name="expression" value="true">Enabled
   		<input type="radio" name="expression" value="false">Disabled
		<br><br>
		 Expression Profile Query:
   		<input type="radio" name="expression_prof" value="true">Enabled
   		<input type="radio" name="expression_prof" value="false">Disabled
		<br><br>
		 Ortholog Query:
   		<input type="radio" name="ortholog" value="true">Enabled
   		<input type="radio" name="ortholog" value="false">Disabled
		<br><br>
		 Annotation Query:
   		<input type="radio" name="annotation" value="true">Enabled
   		<input type="radio" name="annotation" value="false">Disabled
		<br><br>
		MySQL Server Address: <input type="text" name="server">
		<br><br>
		MySQL Server User: <input type="text" name="user">
		<br><br>
		MySQL Server Password: <input type="password" name="password">
		<br><br>
		MySQL Server Main Database Name: <input type="text" name="maindb">
		<br><br>
		MySQL Server Orthology Database Name: <input type="text" name="orthodb">
		<br><br>
		<input type="submit" name="submit" value="Submit">
	</form>
</html>
