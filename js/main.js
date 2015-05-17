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
		prev="expressionQuery";
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

	//Handle the single gene query
	$('#singleGeneQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var gene = $("#singleGeneInput").val();
		var species = $(".speciesSelect").val();
		$.get("gene_query.php?g0=" + gene + "&spec=" + species, function(data) {
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
		req = "gene_query.php?";
		//Build the GET request by looping through the inputs
		for(i = 0;i < texts.length;i++){
			if(i !=  0){
				req+="&";
			}
			req+=("g" + i + "="+texts[i]);	
		}
		if (document.getElementById('ANDButton').checked){
			req+=("&type=" + "AND");
		}else{
			req+=("&type=" + "OR");
		}
		//Append on the species DB to access
		req+=("&spec=" + vals[2]);
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
	});

	//Handle the module member query
	$('#modMemberQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var module = $("#modMemberInput").val();
		var species = $(".speciesSelect").val();
		$.get("module_query.php?module=" + module + "&spec=" + species, function(data) {
			$('#qTable').empty()
			.html(data)
			.ready(function(){
				$("#modMemberForm").hide();
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
	});

	//Handle the expression query
	$('#expressionQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var $inputs = $('#expressionQueryForm :input');
		var vals = {};

		ind = 0;
		$inputs.each(function() {
			vals[ind] = $(this).val();	
			ind++;
		});

		var lines = $('#expressionInputArea').val().split(/\n/);
		var texts = [];

		for (var i=0; i < lines.length; i++) {
  			// only push this line if it contains a non whitespace character.
 			 if (/\S/.test(lines[i])) {
  				texts.push($.trim(lines[i]));
  			 }
           	}
		console.log(texts);
		console.log(vals);
		req = "expression_query.php?";
		//Build the GET request by looping through the inputs
		for(i = 0;i < texts.length;i++){
			if(i !=  0){
				req+="&";
			}
			req+=("g" + i + "="+texts[i]);	
		}
		//Append on the species DB to access
		req+=("&spec=" + vals[0]);
		console.log(req);
		$.get(req, function(data) {
			var info = JSON.parse(data);
			console.log(info);
			console.log(info[0]);
			$("#expressionForm").hide();
			$("#goBack").show();
			$("#goBack").css("height","136px");	
		});
		table.draw();
	});

});
