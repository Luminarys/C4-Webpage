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
			$("#goBack").css("height","50px");	
    			var mtable = $('#metricQueryTable').DataTable();
		});
	$.get("php/expression_query.php?noex=true&g0=" + gene + "&spec=" + species, function(data) {
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

	$.get("php/gene_query.php?noex=true&g0=" + gene + "&spec=" + species, function(data) {
		$('#qTable').append("<p>Network Query Table:</p>");
		$('#qTable')
		.append(data)
		.ready(function(){
    			table = $('#basicQueryTable').DataTable();
				$('#lower-rect').removeAttr('style');
		});
	});
	table.draw();
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
var alt_specs = [];
var specs = 0;
$(document).ready(function() {

	$("#species > option").each(function() {
		alt_specs.push(this.value);
		specs++;
	});
    	table = $('#basicQueryTable').DataTable();
	//What to do when we do an AJAX query to load a popup
	if (getQueryVar("anlink")){
		var gene = getQueryVar("gene");
		$("#annotationInput").val(gene);
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
