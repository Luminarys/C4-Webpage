<!DOCTYPE HTML>
<!-- Website template by freewebsitetemplates.com -->
<html>
<head>
	<meta charset="UTF-8">
	<title>C4</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">

	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
</head>
<body>
	<?php
	include_once "header.php";
	?>
	<div id="contents">
			<div id="querySelection">
				<h3>Welcome to the C4 project, please select a query to interrogate the networks:</h3>
				<a href="gene_set_query.php" id="multiGeneQuery">Query a Gene Set</a><br>
				<a href="module_query.php" id="modMemberQuery">Identify Genes by Modular Membership</a><br>
				<a href="expression_query.php" id="expressionQuery">View Gene Expression</a><br>
				<a href="expression_profile_query.php" id="expressionQuery">Identify Genes by Expression</a><br>
				<a href="javascript:void(0)" id="expressionView">View Modular Expression Profiles and Functional Enrichment</a><br>
				<a href="ortho_query.php" id="orthQuery">Query Orthologs</a><br>
				<a href="annotation_query.php" id="annoQuery">Query Annotation</a><br>
			</div>
			<div id="qTable">
			</div>
	</div>
	<?php
	include_once "footer.php";
	?>
</body>
</html>
