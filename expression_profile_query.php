<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">	

	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="js/tableTools.js"></script>
	
	<!-- Expression Profile Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/expression_profile_query.js"></script>

	<!-- Shared Query Functions JS file -->
	<script type="text/javascript" charset="utf8" src="js/query_shared.js"></script>
	<script type="text/javascript" charset="utf8" src="js/force_layout.js"></script>

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
		<form id="issue" action="https://github.com/Luminarys/C4-Webpage/issues">
    			<input type="submit" value="Report issue/Make Suggestion">
		</form>
		<br>
			<div id="entryForm">
				<div id="goBack">
					<button class="backToQuery">Back to query selection</button>
					<button id="backToInput">Back to data entry</button>
				</div>
				<div id="expressionProfileForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Query by Expression Profile:</p>
					<form id='expressionProfileForm'>
							<input type="text" id="r-val" placeholder="Minimum R">
							<input type="text" id="resnum" placeholder="# of results">
							<input type="text" id="minexp" placeholder="Maximum Mean Expression">
							<input type="text" id="maxexp" placeholder="Minimum Mean Expression"><br><br>
							<select class="speciesSelect" id="spec">
							<?php
							include_once "load_species_options.php";
							?>
						</select>
						<input type="submit">
					</form>
				</div>
				<div id="eq" class="entryform">
					<br>
					<div>
						<br>
						<table style="width:100%;">
							<tr id="samples">
							</tr>
							<tr id="values">
							</tr>
						</table>
					</div>
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
