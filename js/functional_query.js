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

    	var table = $('#functionalQueryTable').DataTable();

	$('*').keyup( function() {
		console.log("key released");
        	table.draw();
    	} );

	//Handle the module member query
	$('#functionalQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var module = $("#moduleInput").val();
		var species = $(".speciesSelect").val();
		$.get("php/functional_query.php?module=" + module + "&spec=" + species, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#functionalForm").hide();
				$("#goBack").show();
				if($('#functionalQueryTable tr').length > 9){
					$('#lower-rect').removeAttr('style');
				}else{
					$("#goBack").css("height","136px");	
				}
				addPopups();
				addMetricPopups();
				$('#functionalQueryTable').on( 'draw.dt', debounce(addPopups, 100));
				$('#functionalQueryTable').on( 'draw.dt', debounce(addMetricPopups, 100));

    				table = $('#functionalQueryTable').DataTable();
			});
		});
		table.draw();
	});
});
