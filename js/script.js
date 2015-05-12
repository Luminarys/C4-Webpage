$(document).ready(function() {
    // $('#basicQuery').DataTable();
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

		if(!isNaN(values[2])){
			var filteredData = table
			.columns( 8 )
			.data()
			.filter ( function ( value, index ) {
				return value > 120 ? true : false;
			});	
		}else{
			alert("Please use a valid number");
		}
		
	});
} );

