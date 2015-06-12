<div id="footer">	
	<?php
	$settings = parse_ini_file("settings.ini");	
	date_default_timezone_set( "America/Chicago" );
	?>
	<div class="clearfix">
		<p>
			<?php
			echo "© " . date("Y") . " " . $settings["copyright"] . ". All Rights Reserved."; 
			?>
		</p>
	</div>
</div>
