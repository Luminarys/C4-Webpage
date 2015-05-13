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
	var c_genes = 0;
	var max_genes = 5;
	var wrapper = $("#gene_wrap");
    	var table = $('#basicQueryTable').DataTable();
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
	
	$(".add_gene_button").click(function(e){
		e.preventDefault();
		if(c_genes < max_genes){
			c_genes++;
			$(wrapper).append('<span id=g'+c_genes+ '><input type="text" name="gene[]" placeholder="Gene ID"\></span>');
		}
	});
	$(".remove_gene_button").on("click", function(e){
		e.preventDefault();
		console.log("Trying to remove gene field " + c_genes);
		if(c_genes > 0){
			$("#g"+c_genes).remove();
			c_genes--;
		}
	});

	$('#geneQuery').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var $inputs = $('#geneQuery :input');
		ind = 0;	
		var vals = {};
		$inputs.each(function() {
			vals[ind] = $(this).val();	
			ind++;
		});
		console.log(vals);
		req = "basic_query.php?";
		for(i = 0;i < ind-2;i++){
			if(i !=  0){
				req+="&";
			}
			req+=("g" + i + "="+vals[i]);	
		}
		console.log(req);
		var gene = $('#gene').val();
		$.get(req, function(data) {
			$('#qTable').empty()
			.html(data);
		});
    		table = $('#basicQueryTable').DataTable();
		table.draw();
		prev_gene = gene;
	});

} );

