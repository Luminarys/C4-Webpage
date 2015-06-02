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

function insertData(gene, species) {
	$('#qTable').empty()
	$('#info').empty()
	$.get("php/annotation_query.php?gene=" + gene + "&spec=" + species, function(data) {
		$('#info')
		.append(data)
		.ready(function(){
			$("#annotationForm").hide();
			$("#goBack").removeAttr('style').show();
			$("#goBack").css("height","136px");	
    			var mtable = $('#metricQueryTable').DataTable();
		});
	$.get("php/expression_query.php?g0=" + gene + "&spec=" + species, function(data) {
		$('#qTable').append("<p>Gene Plot:</p>");
		texts = [gene];
		console.log(data);
		if (!isJson(data)){
			alert(data);
			return 1;
		}
		var info = JSON.parse(data);
		linePlot(info, texts);
	});
	});

	$.get("php/gene_query.php?g0=" + gene + "&spec=" + species, function(data) {
		$('#qTable').append("<p>Network Query Table:</p>");
		$('#qTable')
		.append(data)
		.ready(function(){
    			table = $('#basicQueryTable').DataTable();
				$('#lower-rect').removeAttr('style');
		});
	});
	table.draw();
	/*
	$.get("php/ortho_query", function(data) {
		$('#qTable').empty()
		.append(data)
		.ready(function(){
			if($('#orthoQueryTable tr').length > 9){
				$('#lower-rect').removeAttr('style');
			}else{
				$("#goBack").css("height","136px");	
			}
    			table2 = $('#orthoQueryTable').DataTable();
		});
	});
	table2.draw();
	/**/

}

function getQueryVar(variable){
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

var table = $('#basicQueryTable').DataTable();

$(document).ready(function() {

    	table = $('#basicQueryTable').DataTable();
	//What to do when we do an AJAX query to load a popup
	if (getQueryVar("link")){
		var gene = getQueryVar("gene");
		var species = getQueryVar("spec");
		$('.Site').empty();
		$('.Site').append("<div id='info'></div>");
		$('.Site').append("<div id='qTable'></div>");
		insertData(gene, species);
	}

	$('*').keyup( function() {
		console.log("key released");
        	table.draw();
    	} );

	$('#annotationQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var gene = $("#annotationInput").val();
		var species = $(".speciesSelect").val();
		insertData(gene, species);
	});

});
