<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	
	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>

	<!-- Gene Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/plasticity_query.js"></script>

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
		<?php
		include_once "header.php";
		?>
		<div id="contents">
			<form id="issue" action=<?php echo $settings['github'] ?>>
    				<input type="submit" value="Report issue/Make Suggestion">
			</form><br>
			<div id="entryForm">
				<div id="goBack">
					<button class="backToQuery">Back to query selection</button>
					<button id="backToInput">Back to data entry</button>
				</div>
				<div id="multiGeneForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Gene Plasticity Query:</p>
					<form id='plasticityQueryForm'>
							Control Species: 
							<select class="speciesSelect">
							<?php
								//Uses the MapReference table to generate options within a select block. Include this within <select> </select> to have it generate all the available species
							
								//Set debugging on
								ini_set('display_errors', 'On');
								error_reporting(E_ALL | E_STRICT);
								
								//Load the settings
								$settings = parse_ini_file("settings.ini");	
								
								//Initialize Server, using settings
								$dbInit = 'mysql:host=' . $settings["server"] . ';dbname=' . $settings["maindb"] .';charset=utf8';
								$db = new PDO($dbInit, $settings["user"], $settings["password"]);
								$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
								//Build valid species and genes from MapReference table
								$query = $db->prepare("SELECT DISTINCT pfx_control, MapReference.display_name FROM MapCompare LEFT JOIN MapReference ON MapCompare.pfx_control = MapReference.prefix");
								if($query->execute()){
									$result = $query->fetchAll();
									foreach ($result as $spec){
										echo "<option value=" . $spec["pfx_control"] . ">" . $spec["display_name"] . "</option>";
									}
								}else{
									echo "Warning, no MapReference table defined, exiting";
									exit();
								}
							?>
							</select>
							<br>
							Comparison Species:
							<span id='compareSpecSel'>
							</span>
							<br>
						<div>
							<textarea id="multiGeneInputArea" rows="4" cols="51"></textarea>
						</div>
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
		<?php
		include_once "footer.php";
		?>
	</div>

</body>
</html>
