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


$(document).ready(function() {

    	var table = $('#basicQuery').DataTable();
	var prev_gene = "";
	$('#min, #max').keyup( function() {
        table.draw();
    	} );
	$('#filterForm').submit(function(e) {
		e.preventDefault();
		var $inputs = $('#filterForm :input');
		console.log($inputs);
		//Intialize parameters for filtering
		var column = "";	
		var lessThan = 0;	
		var val = 0;	
		var values = [];
		var i = 0;

		$inputs.each(function() {
			console.log($(this).val());
			values[i] = $(this).val();
			i++;
		});

		console.log(values);
		if ( values[1] === "Greater than"){
			lessThan = 1;	
		}
		val = parseInt(values[2]);
		console.log(val);

		if(!isNaN(values[2])){
			var filteredData = table
			.columns( 8 )
			.data()
			.filter ( function ( value, index ) {
				console.log(value)
				return false;	
				return value > 120 ? true : false;
			});	
			 filteredData.draw();
		}else{
			alert("Please use a valid number");
		}
		
	});

	$('#geneQuery').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var gene = $('#gene').val();
		//console.log(prev_gene + ' ' + gene);
		if (prev_gene !== gene){
			$.get('query_accept.php?gene=' + gene, function(data) {
				$('#qTable').empty();
				$('#qTable').html(data);
			});
		}
		prev_gene = gene;
	});

} );

