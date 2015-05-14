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
	var prev = "";
	$('#entryForm').children().hide();

	//For some reason the specific fields don't work, but this is fine
	$('*').keyup( function() {
		console.log("key released");
        	table.draw();
    	} );
	
	$("#singleGeneQuery").click(function() {
		prev="singleGene";
		$("#querySelection").hide();
		$("#singleGeneForm").show();
	});

	$("#multiGeneQuery").click(function() {
		prev="multiGene";
		$("#querySelection").hide();
		$("#multiGeneForm").show();
	});

	$("#backToInput").click(function() {
		$("#goBack").hide();
		$("#qTable").empty();
		$("#" + prev + "Form").show();
		$('#lower-rect').removeAttr('style').css("margin-top", "450px");
	});


	$(".backToQuery").click(function() {
		$('#qTable').empty()
		$('#entryForm').children().hide();
		$("#querySelection").show();
		//Ensure that the bottom bar stays at the bottom
		$('#lower-rect').removeAttr('style').css("margin-top", "450px");

	});

	$(".addGeneButton").click(function(e){
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		if(c_genes < max_genes){
			c_genes++;
			$(wrapper).append('<span id=g'+c_genes+ '><input type="text" name="gene[]" placeholder="Gene ID"\></span>');
		}
	});

	$(".removeGeneButton").on("click", function(e){
		e.preventDefault();
		console.log("Trying to remove gene field " + c_genes);
		if(c_genes > 0){
			$("#g"+c_genes).remove();
			c_genes--;
		}
	});
	
	//Handle the single gene query
	$('#singleGeneQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var gene = $("#singleGeneInput").val();
		var species = $(".speciesSelect").val();
		$.get("basic_query.php?g0=" + gene + "&spec=" + species, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#singleGeneForm").hide();
				$("#goBack").show();
				if($('#basicQueryTable tr').length > 9){
					$('#lower-rect').removeAttr('style');
				}
    				table = $('#basicQueryTable').DataTable();
			});
		});
		table.draw();
	});

	//Handle the multi-gene query
	$('#multiGeneQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var $inputs = $('#multiGeneQueryForm :input');
		ind = 0;	
		var vals = {};
		$inputs.each(function() {
			vals[ind] = $(this).val();	
			ind++;
		});
		console.log(vals);
		req = "basic_query.php?";
		//Build the GET request by looping through the inputs
		for(i = 0;i < ind-2;i++){
			if(i !=  0){
				req+="&";
			}
			req+=("g" + i + "="+vals[i]);	
		}
		//Append on the species DB to access
		req+=("&spec=" + vals[ind-2]);
		//console.log(req);
		$.get(req, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#multiGeneForm").hide();
				$("#goBack").show();
				if($('#basicQueryTable tr').length > 9){
					$('#lower-rect').removeAttr('style');
				}
			});
		});
    		table = $('#basicQueryTable').DataTable();
		table.draw();
	});

} );

