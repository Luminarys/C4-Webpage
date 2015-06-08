
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
    	var table;
	$("#MultiGeneQueryExpression").hide();
	if (getQueryVar("netlink")){
		req = "php/gene_query.php?";
       		var query = window.location.search.substring(1);
       		var vars = query.split("&");
		var first = true;
		var cont = 0
		var spec;
		var genes = [];
       		for (var i = 0;i < vars.length;i++) {
               		var pair = vars[i].split("=");
			if(i != 0){
				req+="&";
			}
			if(pair[0].charAt(0) == "g"){
				genes.push(pair[1]);
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
				spec = pair[1];
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
				if(data == "Invalid gene used, please try again"){
					return 0;
				}else{
    				table = $('#basicQueryTable').DataTable();
				addPopups();
				$("#MultiGeneQueryExpression").show();
				$('#basicQueryTable').on( 'draw.dt', debounce(addPopups, 100));
				}
			});
			$("#networkGraph").click(function() {
				var field = $("#filterChoice option:selected").text();
				var min	= $("#min").val();
				var max = $("#max").val();
				console.log(typeof min);
				if(min in window && max in window){
					$("#qTable").empty();
					$("#MultiGeneQueryExpression").hide();
					$("#multiGeneForm").hide();
					$("#goBack").show();
					$('#lower-rect').removeAttr('style');
					generateGraph(spec, genes, field, min, max);
				}else if(field == "Adjacency Value"){
					//Default to this
					console.log("defaulting to adjacency -2/2");
					$("#qTable").empty();
					$("#MultiGeneQueryExpression").hide();
					$("#multiGeneForm").hide();
					$("#goBack").show();
					$('#lower-rect').removeAttr('style');
					generateGraph(spec, genes, "Adjacency Value", -2, 2);
				}
			});
		});
	}

	$("#MultiGeneQueryExpression").click(function() {
		var genes = $("#geneSelections input:checkbox:checked").map(function(){
      			return $(this).val();
    		}).get(); // <----
    		console.log(genes);
		req = "expression_query.php?exlink=true";
		if (!genes.length > 0){
			return false;
		}
		//Build the GET request by looping through the inputs
		for(i = 0;i < genes.length;i++){
			req+="&";
			req+=("g" + i + "="+genes[i]);	
		}
		req+=("&spec=" + $(".speciesSelect").val());
		window.location.href = req;
	});

	//Handles the filtering
	$('*').keyup( function() {
        	table.draw();
    		//addPopups();
    	} );
	//Handles readding in popups whenever the table is adjusted
	$(document).click(function() {
    		//addPopups();
  	});

	$('#singleGeneQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var gene = $("#singleGeneInput").val();
		var species = $(".speciesSelect").val();
		var spec = species;
		var genes = [gene];
		$.get("php/gene_query.php?g0=" + gene + "&spec=" + species, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#singleGeneForm").hide();
				$("#goBack").removeAttr('style').show();
				if($('#basicQueryTable tr').length > 9){
					$('#lower-rect').removeAttr('style');
				}else{
					$("#goBack").css("height","136px");	
				}

				if(data == "Invalid gene used, please try again"){
					return 0;
				}else{
    				table = $('#basicQueryTable').DataTable();
				addPopups();
				$("#MultiGeneQueryExpression").show();
				$('#basicQueryTable').on( 'draw.dt', debounce(addPopups, 100));
				}
			});
			$("#networkGraph").click(function() {
				var field = $("#filterChoice option:selected").text();
				var min	= $("#min").val();
				var max = $("#max").val();
				console.log(typeof min);
				if(min in window && max in window){
					$("#qTable").empty();
					$("#MultiGeneQueryExpression").hide();
					$("#multiGeneForm").hide();
					$("#goBack").show();
					$('#lower-rect').removeAttr('style');
					generateGraph(spec, genes, field, min, max);
				}else if(field == "Adjacency Value"){
					//Default to this
					console.log("defaulting to adjacency -2/2");
					$("#qTable").empty();
					$("#MultiGeneQueryExpression").hide();
					$("#multiGeneForm").hide();
					$("#goBack").show();
					$('#lower-rect').removeAttr('style');
					generateGraph(spec, genes, "Adjacency Value", -2, 2);
				}
			});
		});
		table.draw();
	});

	//Handle the multi-gene query
	$('#multiGeneQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var $inputs = $('#multiGeneQueryForm :input');
		var vals = {};

		ind = 0;
		$inputs.each(function() {
			vals[ind] = $(this).val();	
			ind++;
		});

		var lines = $('#multiGeneInputArea').val().split(/\n/);
		var texts = [];

		for (var i=0; i < lines.length; i++) {
  			// only push this line if it contains a non whitespace character.
 			 if (/\S/.test(lines[i])) {
  				texts.push($.trim(lines[i]));
  			 }
           	}
		console.log(texts);
		console.log(vals);
		var spec;
		var genes = [];
		req = "php/gene_query.php?";
		//Build the GET request by looping through the inputs
		for(i = 0;i < texts.length;i++){
			if(i !=  0){
				req+="&";
			}
			req+=("g" + i + "="+texts[i]);	
			genes.push(texts[i]);
		}
		if (document.getElementById('ANDButton').checked){
			req+=("&type=" + "AND");
		}else{
			req+=("&type=" + "OR");
		}
		//Append on the species DB to access
		req+=("&spec=" + vals[2]);
		spec = vals[2];
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
				if(data == "Invalid gene used, please try again"){
					return 0;
				}else{
    				table = $('#basicQueryTable').DataTable();
				addPopups();
				$("#MultiGeneQueryExpression").show();
				$('#basicQueryTable').on( 'draw.dt', debounce(addPopups, 100));
				}
			});
			$("#networkGraph").click(function() {
				var field = $("#filterChoice option:selected").text();
				var min	= $("#min").val();
				var max = $("#max").val();
				console.log(typeof min);
				if(min in window && max in window){
					$("#qTable").empty();
					$("#MultiGeneQueryExpression").hide();
					$("#multiGeneForm").hide();
					$("#goBack").show();
					$('#lower-rect').removeAttr('style');
					generateGraph(spec, genes, field, min, max);
				}else if(field == "Adjacency Value"){
					//Default to this
					console.log("defaulting to adjacency -2/2");
					$("#qTable").empty();
					$("#MultiGeneQueryExpression").hide();
					$("#multiGeneForm").hide();
					$("#goBack").show();
					$('#lower-rect').removeAttr('style');
					generateGraph(spec, genes, "Adjacency Value", -2, 2);
				}
			});
			table.draw();
		});
	});
	
});
