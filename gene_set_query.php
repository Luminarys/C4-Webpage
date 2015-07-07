<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	
	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>

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
		<?php
		include_once "header.php";
		?>
		<div id="contents">
			<form id="issue" target="_blank" action=<?php echo $settings['github'] ?>>
    				<input type="submit" value="Report issue/Make Suggestion">
			</form><br>
			<div id="entryForm">
				<div id="goBack">
					<button class="backToQuery">Back to query selection</button>
					<button id="backToInput">Back to data entry</button>
				</div>
				<div id="multiGeneForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Gene Set Query:</p>
					<form id='multiGeneQueryForm'>
							<input type="radio" name="multiGeneOption" checked="checked">Include edges in which one node is in the Target Node list</option><br>
							<input type="radio" name="multiGeneOption" id="ANDButton">Include edges in which both nodes are in the Target Node list</option><br>
							<select class="speciesSelect">
							<?php
							include_once "load_species_options.php";
							?>
							</select>
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
