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

	$("#modMemberQuery").click(function() {
		prev="modMember";
		$("#querySelection").hide();
		$("#modMemberForm").show();
	});

	$("#expressionQuery").click(function() {
		prev="expression";
		$("#querySelection").hide();
		$("#expressionForm").show();
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
});
