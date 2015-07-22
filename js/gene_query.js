$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseFloat( $('#min').val(), 10 );
        var max = parseFloat( $('#max').val(), 10 );
        var col = parseFloat( data[parseInt($("#filterChoice").val())] ) || 0; // Get column number based on values in the pre-table
        var min2 = parseFloat( $('#min2').val(), 10 );
        var max2 = parseFloat( $('#max2').val(), 10 );
        var col2 = parseFloat( data[parseInt($("#filterChoice2").val())] ) || 0; // Get column number based on values in the pre-table
        var min3 = parseFloat( $('#min3').val(), 10 );
        var max3 = parseFloat( $('#max3').val(), 10 );
        var col3 = parseFloat( data[parseInt($("#filterChoice3").val())] ) || 0; // Get column number based on values in the pre-table
	if($("#invertChoice").val() == "true"){
       		if (
			( ( isNaN( min ) && isNaN( max ) ) ||
       		     	( isNaN( min ) && col <= max ) ||
       		     	( min <= col   && isNaN( max ) ) ||
       		     	( min <= col   && col <= max ) )
			&&
			( ( isNaN( min2 ) && isNaN( max2 ) ) ||
       		     	( isNaN( min2 ) && col <= max2 ) ||
       		     	( min2 <= col2   && isNaN( max2 ) ) ||
       		     	( min2 <= col2   && col2 <= max2 ) )
			&&
			( ( isNaN( min3 ) && isNaN( max3 ) ) ||
       		     	( isNaN( min3 ) && col3 <= max3 ) ||
       		     	( min3 <= col3   && isNaN( max3 ) ) ||
       		     	( min3 <= col3   && col3 <= max3 ) )
		)
       		{
       		    return false;
       		}
       		return true;

	}else{
       		if ( 
			(( isNaN( min ) && isNaN( max ) ) ||
       		     	( isNaN( min ) && col <= max ) ||
       		     	( min <= col   && isNaN( max ) ) ||
       		     	( min <= col   && col <= max ) )
			&&
			( ( isNaN( min2 ) && isNaN( max2 ) ) ||
       		     	( isNaN( min2 ) && col <= max2 ) ||
       		     	( min2 <= col2   && isNaN( max2 ) ) ||
       		     	( min2 <= col2   && col2 <= max2 ) )
			&&
			( ( isNaN( min3 ) && isNaN( max3 ) ) ||
       		     	( isNaN( min3 ) && col3 <= max3 ) ||
       		     	( min3 <= col3   && isNaN( max3 ) ) ||
       		     	( min3 <= col3   && col3 <= max3 ) )
		)
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

$(document).ready(function() {
    	var table;
	$("#MultiGeneQueryExpression").hide();
	$("#listSel").val('');
	if (getQueryVar("netlink")){
		req = "php/gene_query.php?";
       		var query = window.location.search.substring(1);
       		var vars = query.split("&");
		var first = true;
		var cont = 0;
		var spec;
		var genes = [];
       		for (var i = 0;i < vars.length;i++) {
               		var pair = vars[i].split("=");
			if(i != 0){
				req+="&";
			}
			req+=("g" + cont + "=" + pair[1]);	
			cont++;
			if(pair[0].charAt(0) == "g"){
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
	$('#multiGeneQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		checkSpec();
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

			$("#getCSV").click(function() {
				var url = $("#getCSV").attr("url");
				var field = $("#filterChoice option:selected").attr("field");
				var min	= $("#min").val();
				var max = $("#max").val();
				var field2 = $("#filterChoice2 option:selected").attr("field");
				var min2 = $("#min2").val();
				var max2 = $("#max2").val();
				var field3 = $("#filterChoice3 option:selected").attr("field");
				var min3 = $("#min3").val();
				var max3 = $("#max3").val();
				if(min != "" && max != ""){
					url += "&field=" + field + "&min=" + min + "&max=" + max;
				}
				if(min2 != "" && max2 != ""){
					url += "&field2=" + field2 + "&min2=" + min2 + "&max2=" + max2;
				}
				if(min3 != "" && max3 != ""){
					url += "&field3=" + field3 + "&min3=" + min3 + "&max3=" + max3;
				}
				window.open(url,'_blank');
			
			});

			$("#networkGraph").click(function() {
				var field = $("#filterChoice option:selected").text();
				var min	= $("#min").val();
				var max = $("#max").val();
				var field2 = $("#filterChoice2 option:selected").text();
				var min2 = $("#min2").val();
				var max2 = $("#max2").val();
				var field3 = $("#filterChoice3 option:selected").text();
				var min3 = $("#min3").val();
				var max3 = $("#max3").val();
				var args = [spec, genes];

				if(min != "" && max != ""){
					args.push(field, min, max);
				}else{
					args.push("Adjacency Value", 0, 2);
				}

				if(min2 != "" && max2 != ""){
					args.push(field2, min2, max2);
				}else{
					args.push("None", 0, 0);
				}

				if(min3 != "" && max3 != ""){
					args.push(field3, min3, max3);
				}else{
					args.push("None", 0, 0);
				}

				$("#qTable").empty();
				$("#MultiGeneQueryExpression").hide();
				$("#multiGeneForm").hide();
				$("#goBack").show();
				$('#lower-rect').removeAttr('style');
				generateGraph.apply(this, args);
			});
			table.draw();
		});
	});
	
});
