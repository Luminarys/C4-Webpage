<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	
	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>
	
	<!-- Gene Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/annotation_query.js"></script>

	<!-- D3JS -->
	<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.js"></script>

	<!-- D3JS Min -->
	<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
	
	<!-- Expression Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/expression_query.js"></script>

	<!-- Shared Query Functions JS file -->
	<script type="text/javascript" charset="utf8" src="js/query_shared.js"></script>

	<!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
	
	<!-- Local CSS -->
	<link rel="stylesheet" type="text/css" href="css/query.css">
	

</head>

<body class="Site">
	<div id="wrapper" class="Site-content">
		<?php
		include_once "header.php";
		?>
		<div id="contents">
			<form id="issue" action="https://github.com/Luminarys/C4-Webpage/issues">
    				<input type="submit" value="Report issue/Make Suggestion">
			</form><br>
			<div id="entryForm">
				<div id="goBack">
					<button class="backToQuery">Back to query selection</button>
					<button id="backToInput">Back to data entry</button>
				</div>
				<div id="annotationForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Gene Annotation Query:</p>
					<form id='annotationQueryForm'>
							<input type="text" id="annotationInput" placeholder="Gene ID">
							<select class="speciesSelect" id="species">
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
							if($query->execute()){
								$result = $query->fetchAll();
								foreach ($result as $spec){
									echo "<option value=" . $spec["prefix"] . ">" . $spec["display_name"] . "</option>";
								}
							}else{
								echo "Warning, no MapReference table defined, exiting";
								exit();
							}
						?>
						</select>
						<input type="submit">
					</form>
				</div>
			</div>
			<div id="info"></div>
			<div id="qTable"></div>
		</div>
			
		<?php
		include_once "footer.php";
		?>
	</div>

</body>
</html>
