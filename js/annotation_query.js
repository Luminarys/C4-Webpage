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

function getQueryVar(variable){
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

$(document).ready(function() {

    	var table = $('#basicQueryTable').DataTable();
	//What to do when t
	if (getQueryVar("link")){
		//Figure this out later
		req = "php/gene_query.php?";
       		var query = window.location.search.substring(1);
       		var vars = query.split("&");
		var first = true;
		var cont = 0
       		for (var i = 0;i < vars.length;i++) {
               		var pair = vars[i].split("=");
			if(i != 0){
				req+="&";
			}
			if(pair[0].charAt(0) == "g"){
				if(i != 0){
					req+="&";
				}
				req+=("g" + cont + "=" + pair[1]);	
				cont++;
				if (first){
					$('#multiGeneInputArea').val($('#multiGeneInputArea').val() + pair[1])
					first = false;
				}else{
					$('#multiGeneInputArea').val($('#multiGeneInputArea').val() + '\n' + pair[1])
				}
			}else if(pair[0] == "spec"){
				if(i != 0){
					req+="&";
				}
				req+=("spec="+pair[1]);
				$(".speciesSelect").val(pair[1]);
			}
       		}
		req+=("&type=" + "OR");
		console.log(req);
		$.get(req, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#multiGeneForm").hide();
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
		$.get("php/annotation_query.php?gene=" + gene + "&spec=" + species, function(data) {
			$('#qTable').empty()
			.append(data)
			.ready(function(){
				$("#annotationForm").hide();
				$("#goBack").removeAttr('style').show();
				$("#goBack").css("height","136px");	
    				var mtable = $('#metricQueryTable').DataTable();
			});
		$.get("php/gene_query.php?g0=" + gene + "&spec=" + species, function(data) {
			$('#qTable').append("<p>Network Query Table:</p>");
			$('#qTable')
			.append(data)
			.ready(function(){
    				table = $('#basicQueryTable').DataTable();
				if($('#basicQueryTable tr').length > 9){
					$('#lower-rect').removeAttr('style');
				}else{
					$("#goBack").css("height","136px");	
				}
			});
		});
		table.draw();
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
	});

});
