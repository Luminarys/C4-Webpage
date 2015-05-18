$(document).ready( function() {
	//Handle the module member query
	$('#modMemberQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var module = $("#modMemberInput").val();
		var species = $(".speciesSelect").val();
		$.get("php/module_query.php?module=" + module + "&spec=" + species, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#modMemberForm").hide();
				$("#goBack").show();
				if($('#basicQueryTable tr').length > 9){
					$('#lower-rect').removeAttr('style');
				}else{
					$("#goBack").css("height","136px");	
				}

    				table = $('#basicQueryTable').DataTable();
			});
		});
		table.draw();
	});
});
