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
				<div id="orthoForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Gene Ortholog Query:</p>
					<form id='orthoQueryForm'>
						Given Species: <select>
						<?php
						include "load_species_options.php";
						?>
						</select><br>
						Ortholog Species: <select id="orthoSpec">
						<?php
						include "load_species_options.php";
						?>
						</select>
						<br><br>
						<div>
							<textarea id="orthoInputArea" rows="4" cols="51"></textarea>
						</div>
						<input type="submit">
					</form>
				</div>
				<button id="MultiGeneQueryNetwork">Network Query using selected Genes</button>
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
