<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	
	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="js/tableTools.js"></script>
	
	<!-- Gene Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/gene_query.js"></script>

	<!-- Shared Query Functions JS file -->
	<script type="text/javascript" charset="utf8" src="js/query_shared.js"></script>
	<script type="text/javascript" charset="utf8" src="js/force_layout.js"></script>

	<!-- D3JS -->
	<script src="http://d3js.org/d3.v3.min.js"></script>	

	<!--qTip-->
	<script type="text/javascript" charset="utf8" src="js/popup.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.css">

	<!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
	
	<!-- Local CSS -->
	<link rel="stylesheet" type="text/css" href="css/query.css">
	

</head>

<body class="Site">
	<div id="wrapper" class="Site-content">
		<div id="header">
		</div>
		<script> 
	    		$(function(){
	      			$("#header").load("header.html"); 
	    		});
	    	</script> 
		
		<div id="contents">
		<form id="issue" action="https://github.com/Luminarys/C4-Webpage/issues">
    			<input type="submit" value="Report issue/Make Suggestion">
		</form>
		<br>
			<div id="entryForm">
				<div id="goBack">
					<button class="backToQuery">Back to query selection</button>
					<button id="backToInput">Back to data entry</button>
				</div>
				<div id="singleGeneForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Basic Gene Query:</p>
					<form id='singleGeneQueryForm'>
							<input type="text" id="singleGeneInput" placeholder="Gene ID">
							<select class="speciesSelect">
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
			<div>
				<p></p>
				<button id="MultiGeneQueryExpression">Expression Query using selected Genes</button>
			</div>
			<div id="qTable"></div>
		</div>
			<div id="footer">
			</div>
			<script> 
				$(function(){
					$("#footer").load("footer.html"); 
				});
			</script> 
			</div>
	</div>

</body>
</html>
