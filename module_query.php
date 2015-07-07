<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	
	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>

	<!-- Module Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/module_query.js"></script>

	<!-- Shared Query Functions JS file -->
	<script type="text/javascript" charset="utf8" src="js/query_shared.js"></script>

	<!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
	
	<!-- Local CSS -->
	<link rel="stylesheet" type="text/css" href="css/query.css">

	<!--qTip-->
	<script type="text/javascript" charset="utf8" src="js/popup.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.css">
	
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
				<div id="modMemberForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Module Member Query:</p>
					<form id='modMemberQueryForm'>
							<input type="text" id="modMemberInput" placeholder="Module #">
							<select class="speciesSelect">
							<?php
							include_once "load_species_options.php";
							?>
						</select>
						<input type="submit">
					</form>
				</div>
			</div>
			<div id="qTable"></div>
		</div>
		<?php
		include_once "footer.php";
		?>
	</div>

</body>
</html>
