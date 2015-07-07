<!DOCTYPE HTML>
<html> 
<head>
	  
	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.js"></script>

	<!-- D3JS -->
	<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.js"></script>

	<!-- D3JS Min -->
	<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
	
	<!-- Expression Query JS file -->
	<script type="text/javascript" charset="utf8" src="js/expression_query.js"></script>
	<script src="js/box.js"></script>	

	<!-- Shared Query Functions JS file -->
	<script type="text/javascript" charset="utf8" src="js/query_shared.js"></script>
	<script type="text/javascript" charset="utf8" src="js/popup.js"></script>

	<!-- Local CSS -->
	<link rel="stylesheet" type="text/css" href="css/query.css">
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
					<button class="backToQuery">back to query selection</button>
					<button id="backToInput">back to data entry</button>
				</div>
				<div id="inGraphOpts">
					<span id="plotTypeDiv-in">
						Plot type:
						<select id="plotType-in">
						<option value="box">Box Plot</option>
						<option value="line">Line Graph</option>
						<option value="dot">Dot Plot</option>
						</select>
					</span>
					<span id="combinePlotsDiv-in">
						Plot combination:
						<select id="combinePlots-in">
						<option value="combine">Yes</option>
						<option value="noCombine">No</option>
						</select>
					</span>
					<span id="geneColorDiv-in">
						Gene coloration:
						<select id="geneColor-in">
						<option value="multi">Yes</option>
						<option value="uni">No</option>
						</select>
						<br><br>
					</span>
					<span id="normalizationPlotsDiv-in">
						Normalization method:
						<select id="normalization-in">
						<option value="mean">Mean Normalize</option>
						<option value="max">Max Normalize</option>
						<option value="log">Log 2 Normalize</option>
						</select>
					</span>
					<span id="normalizationLPDiv-in">
						Normalization method:
						<select id="normalizationLP-in">
						<option value="raw">Raw Data</option>
						<option value="mean">Mean Normalize</option>
						<option value="max">Max Normalize</option>
						<option value="log">Log 2 Normalize</option>
						</select>
						<br><br>
					</span>
				</div>
				<div id="expressionForm" class="entryForm">
					<button class="backToQuery">Back to query selection</button>
					<p>Gene Expression Query:</p>
					<form id='expressionQueryForm'>
						Select plot type: <select id="plotType">
							<option value="box">Box Plot</option>
							<option value="line">Line Graph</option>
							<option value="dot">Dot Plot</option>
						</select><br><br>
						<div id="combinePlotsDiv">
							Plot combination:
							<select id="combinePlots">
							<option value="combine">Yes</option>
							<option value="noCombine">No</option>
							</select>
							<br><br>
						</div>
						<div id="geneColorDiv">
							Gene coloration:
							<select id="geneColor">
							<option value="multi">Yes</option>
							<option value="uni">No</option>
							</select>
							<br><br>
						</div>
						<div id="normalizationDiv">
							Normalization method:
							<select id="normalization">
							<option value="mean">Mean Normalize</option>
							<option value="max">Max Normalize</option>
							<option value="log">Log 2 Normalize</option>
							</select>
							<br><br>
						</div>
						<div id="normalizationDivLP">
							Normalization method:
							<select id="normalizationLP">
							<option value="raw">Raw Data</option>
							<option value="mean">Mean Normalize</option>
							<option value="max">Max Normalize</option>
							<option value="log">Log 2 Normalize</option>
							</select>
							<br><br>
						</div>
						<div id="plotOpt4">
						</div>
						Select species: 
						<select class="speciesSelect">
							<?php
							include_once "load_species_options.php";
							?>
						</select>
						<br>
						<div>
							<textarea id="expressionInputArea" rows="4" cols="51"></textarea>
						</div>
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
