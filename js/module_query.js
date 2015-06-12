$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseFloat( $('#min').val(), 10 );
        var max = parseFloat( $('#max').val(), 10 );
        var age = parseFloat( data[parseInt($("#filterChoice").val())] ) || 0; // Get column number based on values in the pre-table
 
        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && age <= max ) ||
             ( min <= age   && isNaN( max ) ) ||
             ( min <= age   && age <= max ) )
        {
            return true;
        }
        return false;
    }
);

$(document).ready( function() {

    	var table = $('#basicQueryTable').DataTable();

	$('*').keyup( function() {
		console.log("key released");
        	table.draw();
    	} );

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
				addPopups();
				addMetricPopups();
				$('#basicQueryTable').on( 'draw.dt', debounce(addPopups, 100));
				$('#basicQueryTable').on( 'draw.dt', debounce(addMetricPopups, 100));

    				table = $('#basicQueryTable').DataTable();
			});
		});
		table.draw();
	});
});
