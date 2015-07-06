
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseFloat( $('#min').val(), 10 );
        var max = parseFloat( $('#max').val(), 10 );
        var age = parseFloat( data[parseInt($("#filterChoice").val())] ) || 0; // Get column number based on values in the pre-table
	if($("#invertChoice").val() == "true"){
       		if ( ( isNaN( min ) && isNaN( max ) ) ||
       		     ( isNaN( min ) && age <= max ) ||
       		     ( min <= age   && isNaN( max ) ) ||
       		     ( min <= age   && age <= max ) )
       		{
       		    return false;
       		}
       		return true;

	}else{
       		if ( ( isNaN( min ) && isNaN( max ) ) ||
       		     ( isNaN( min ) && age <= max ) ||
       		     ( min <= age   && isNaN( max ) ) ||
       		     ( min <= age   && age <= max ) )
       		{
       		    return true;
       		}
       		return false;
	}
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

function updateSelections(){
	var spec= $(".speciesSelect").val();
	req = "load_compare_options.php?spec=" + spec;
	$.get(req, function(data) {
		$("#compareSpecSel").empty()
		.html(data);
	});
}

$(document).ready(function() {
	updateSelections();
    	var table;
	$("#MultiGeneQueryExpression").hide();
	$(".speciesSelect").change(function () {
		updateSelections();
	});
	if (getQueryVar("netlink")){
		req = "php/gene_plasticity_query.php?";
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
       			}
		}
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

	//Handle the multi-gene query
	$('#plasticityQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var $inputs = $('#plasticityQueryForm :input');
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
		req = "php/gene_plasticity_query.php?";
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
		req += "&orig=" + $(".speciesSelect").val();
		req += "&target=" + $("#compareSelect").val();
		req += "&comp=" + $("#compareSelect option:selected").attr("prefix");
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
    				table = $('#basicQueryTable').DataTable({
					"scrollX": true
				});
				addPopups();
				addMetricPopups();
				$("#MultiGeneQueryExpression").show();
				$('#basicQueryTable').on( 'draw.dt', debounce(addPopups, 100));
				$('#basicQueryTable').on( 'draw.dt', debounce(addMetricPopups, 100));
				table.draw();
				}
			});
			table.draw();
		});
	});
	
});
