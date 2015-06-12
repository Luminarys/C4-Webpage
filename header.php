<div id="header">
	<?php
	$settings = parse_ini_file("settings.ini");	
	?>
	<div>
		<div class="logo">
			<?php
			echo "<a href='index.php'>" . $settings["name"] . "</a>";
			?>
		</div>
		<div id="cssmenu1">
		<ul id="navigation">
			<li class="lol">
				<a href="index.php">Home</a>
			</li>
			<li class='has-sub'>
				<a href="#"><span>Queries</span></a>
				<div class="submenu">
					<a href="gene_set_query.php" id="multiGeneQuery">Gene Set Query</a>
					<a href="module_query.php" id="modMemberQuery">Module Query</a>
					<a href="expression_query.php" id="expressionQuery">View Expression</a>
					<a href="expression_profile_query.php" id="expressionQuery">Query by Expression</a>
					<a href="ortho_query.php" id="orthQuery">Ortholog Query</a>
					<a href="annotation_query.php" id="annoQuery">Annotation Query</a>
				</div>
			</li>
			<li>
				<a href="https://github.com/Luminarys/C4-Webpage/">Github</a>
			</li>
			<li>
				<a href="about.php">Readme</a>
			</li>
			<li class='last'>
				<a href="contact.php">Contact</a>
			</li>
			</div>
		</ul>
	</div>
</div>
