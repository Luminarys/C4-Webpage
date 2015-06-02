<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	
	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>

	<!-- Gene Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/ortho_query.js"></script>

	<!-- Shared Query Functions JS file -->
	<script type="text/javascript" charset="utf8" src="js/query_shared.js"></script>

	<!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
	
	<!-- Local CSS -->
	<link rel="stylesheet" type="text/css" href="css/main.css">
	

</head>

<body class="Site">
	<div id="wrapper" class="Site-content">
		<div id="header">
			<p id="upper-rect"> C4 @ the DDPSC</p>
		</div>
		<form id="issue" action="https://github.com/Luminarys/C4-Webpage/issues">
    			<input type="submit" value="Report issue/Make Suggestion">
		</form>
		<div id="leftside">
			<p id="left-rect">Home</p>	
		</div>
		<div id="content">
			<div id="entryForm">
				<div id="goBack">
					<button class="backToQuery">Back to query selection</button>
					<button id="backToInput">Back to data entry</button>
				</div>
				<div id="orthoForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Gene Ortholog Query:</p>
					<form id='orthoQueryForm'>
						Given Species: <select>
							<?php

							//Set debugging on
							ini_set('display_errors', 'On');
							error_reporting(E_ALL | E_STRICT);
							
							//Load the authentication into an array
							$auth = file("php/DB.auth");
							
							//Initialize Server, loading the file auth
							$dbInit = 'mysql:host=' . substr($auth[0],0,-1) . ';dbname=' . substr($auth[3],0,-1) .';charset=utf8';
							$db = new PDO($dbInit, substr($auth[1],0,-1), substr($auth[2],0,-1));
							$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
							$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
							//Build valid species and genes from MapReference table
							$query = $db->prepare("SELECT * FROM MapReference");
							if($query->execute()){
								$result = $query->fetchAll();
								foreach ($result as $spec){
									echo "<option value=" . $spec["prefix"] . ">" . $spec["display_name"] . "</option>";
								}
								echo '</select>';
								echo 'Ortholog Species: <select id="orthoSpec">';
								foreach ($result as $spec){
									echo "<option value=" . $spec["prefix"] . ">" . $spec["display_name"] . "</option>";
								}
							}else{
								echo "Warning, no MapReference table defined, exiting";
								exit();
							}
						?>
						</select>
						<br>
						<div>
							<textarea id="orthoInputArea" rows="4" cols="51"></textarea>
						</div>
						<input type="submit">
					</form>
				</div>
				<button id="MultiGeneQueryNetwork">Network Query using selected Genes</button>
				<button id="MultiGeneQueryExpression">Expression Query using selected Genes</button>
			</div>
			<div id="bottomCombo">	
				<div id="bottomFiller"></div>
				<div id="qTable"></div>
			
				<div id="footer" class="footer">
					<p id="lower-rect" style="margin-top: 450px;">
						Copyright ...<br>
					</p>
				</div>
			</div>
		</div>
	</div>

</body>
</html>
