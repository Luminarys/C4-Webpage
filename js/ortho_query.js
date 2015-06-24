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

function loadiDataSets(){
	var ispec = $("#inputSpec").val();
	$.get("load_network_options.php?spec=" + ispec, function(data) {
		$('#inputDataSel').empty()
		.html(data);
	});
}
function loadoDataSets(){
	var ospec = $("#orthoSpec").val();
	$.get("load_network_options.php?spec=" + ospec, function(data) {
		$('#orthoDataSel').empty()
		.html(data);
	});
}

$(document).ready(function() {
	$('*').keyup( function() {
		console.log("key released");
        	table.draw();
    	} );
	loadiDataSets();
	loadoDataSets();
    	var table = $('#orthoQueryTable').DataTable();
	$("#MultiGeneQueryExpression").hide();
	$("#MultiGeneQueryNetwork").hide();
	$("#inputSpec").change(function() {
		loadiDataSets();
	});
	$("#orthoSpec").change(function() {
		loadoDataSets();
	});
	
	$("#MultiGeneQueryNetwork").click(function() {
		var genes = $("#geneSelections input:checkbox:checked").map(function(){
      			return $(this).val();
    		}).get(); // <----
    		console.log(genes);
		req = "gene_set_query.php?netlink=true";
		if (!genes.length > 0){
			return false;
		}
		//Build the GET request by looping through the inputs
		for(i = 0;i < genes.length;i++){
			req+="&";
			req+=("g" + i + "="+genes[i]);	
		}
		req+=("&spec=" + $('#orthoDataSel').val());
		window.location.href = req;
	});

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
		req+=("&spec=" + $("#orthoDataSel").val());
		window.location.href = req;
	});
	$("#backToInput" ).click(function() {
		$("#MultiGeneQueryExpression").hide();
		$("#MultiGeneQueryNetwork").hide();
	});
	$('#orthoQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var lines = $('#orthoInputArea').val().split(/\n/);
		var texts = [];

		for (var i=0; i < lines.length; i++) {
  			// only push this line if it contains a non whitespace character.
 			 if (/\S/.test(lines[i])) {
  				texts.push($.trim(lines[i]));
  			 }
           	}
		console.log(texts);
		req = "php/ortho_query.php?";
		//Build the GET request by looping through the inputs
		for(i = 0;i < texts.length;i++){
			if(i !=  0){
				req+="&";
			}
			req+=("g" + i + "="+texts[i]);	
		}
		//Append on the species DB to access
		req+=("&orig=" + $("#inputSpec").val());
		req+=("&idata=" + $("#inputDataSel").val());
		req+=("&ortho=" + $("#orthoSpec").val());
		req+=("&odata=" + $("#orthoDataSel").val());
		console.log(req);
		$.get(req, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#orthoQueryForm").hide();
				$("#goBack").show();
				$("#MultiGeneQueryExpression").show();
				$("#MultiGeneQueryNetwork").show();
				if($('#orthoQueryTable tr').length > 9){
					$('#lower-rect').removeAttr('style');
				}else{
					$("#goBack").css("height","50px");	
				}
    				table = $('#orthoQueryTable').DataTable();
				addOrthoPopups();
			});
		});
		table.draw();
	});
});
