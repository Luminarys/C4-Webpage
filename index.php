<!DOCTYPE HTML>
<!-- Website template by freewebsitetemplates.com -->
<html>
<head>
	<meta charset="UTF-8">
	<title>C4</title>
	<link rel="stylesheet" href="css/query.css" type="text/css">

	<!-- jQuery -->
	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	
	<script>
	$(document).ready(function (){
		var height = $(document).height();
		$("#contents").css("min-height", height - 400);
	});
	</script>
</head>
<body>
	<?php
	include_once "header.php";
	?>
	<div id="contents">
			<div id="querySelection">
				<h3>Welcome, please select a query to interrogate the networks:</h3>
				<?php
				if($settings['gene_set']){
				 echo '<a href="gene_set_query.php" id="multiGeneQuery">Query a Gene Set</a><br>';
				}
				if($settings['module']){
					echo '<a href="module_query.php" id="modMemberQuery">Identify Genes by Modular Membership</a><br>';
				}
				if($settings['expression']){
					echo '<a href="expression_query.php" id="expressionQuery">View Gene Expression</a><br>';
				}
				if($settings['expression_prof']){
					echo '<a href="expression_profile_query.php" id="expressionQuery">Identify Genes by Expression Profile</a><br>';
				}
				if($settings['ortholog']){
					echo '<a href="ortho_query.php" id="orthQuery">Query Orthologs</a><br>';
				}
				if($settings['annotation']){
				 	echo '<a href="annotation_query.php" id="annoQuery">Query Annotation</a><br>';
				}
				if($settings['functional']){
					echo '<a href="functional_query.php" id="funcQuery">View Functional Enrichment</a><br>';
				}
				if($settings['plasticity']){
					echo '<a href="gene_plasticity_query.php" id="plasticityQuery">Query Genes by Plasticity</a>';
				}
				?>
			</div>
			<div id="qTable">
			</div>
	</div>
	<?php
	include_once "footer.php";
	?>
</body>
</html>
