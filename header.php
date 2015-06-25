<div id="header">
	<?php
	$settings = parse_ini_file("settings.ini");	
	?>
	<div>
		<div id="name">
			<?php
			echo $settings["name"];
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
					<?php
					if($settings['gene_set']){
						echo '<a href="gene_set_query.php" id="multiGeneQuery">Gene Set Query</a>';
					}
					if($settings['module']){
						echo '<a href="module_query.php" id="modMemberQuery">Module Query</a>';
					}
					if($settings['expression']){
						echo '<a href="expression_query.php" id="expressionQuery">View Expression</a>';
					}
					if($settings['expression_prof']){
						echo '<a href="expression_profile_query.php" id="expressionQuery">Query by Expression</a>';
					}
					if($settings['ortholog']){
						echo '<a href="ortho_query.php" id="orthQuery">Ortholog Query</a>';
					}
					if($settings['annotation']){
						echo '<a href="annotation_query.php" id="annoQuery">Annotation Query</a>';
					}
					if($settings['functional']){
						echo '<a href="functional_query.php" id="funcQuery">Functional Enrichment Query</a>';
					}
					?>
				</div>
			</li>
			<li>
				
				<a href="<?php echo $settings["github-home"]; ?>">Github</a>
			</li>
			<li>
				<a href="manual.php">Readme</a>
			</li>
			<li class='last'>
				<a href="contact.php">Contact</a>
			</li>
			</div>
		</ul>
	</div>
</div>
