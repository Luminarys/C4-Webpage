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

function average(d) {
	var sum = 0;
	var counter = 0;
	for (var key in d){
		sum+=parseFloat(d[key]);	
		counter+=1;
	}
	return sum/counter;
}


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
			$("#qTable").empty();
			var info = JSON.parse(data);
			for (var i = 0;i < texts.length;i++) {
				var cArr = info[texts[i]];
				var gData = [];
				//console.log(cArr);
				//Generate an associative array based on averages
				var co = 0;
				var samples = [];
				for (var key in cArr){
					var subArr = cArr[key];
					var av = average(subArr);
					//console.log(av);
					gData.push({Sample:co++, val:av});
					samples.push(key);
				}

				console.log(gData);

				var width = (60) * gData.length;
				var height = 400;
				var vis = d3.select("#qTable")
					.append("svg:svg")
					.attr("width", width)
					.attr("height", height);
				var MARGINS = {
					top: 20,
        				right: 20,
        				bottom: 20,
        				left: 50
				};

				var y = d3.scale.linear()
				.domain([d3.min(gData, function(datum) { return datum.val; }), d3.max(gData, function(datum) { return datum.val; })])
				.range([height - MARGINS.top, MARGINS.bottom]);

				var ra = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19];
	
				var x = d3.scale.linear()
				.domain([0,19])
				.range([MARGINS.left, width - MARGINS.right]);

				var xAxis = d3.svg.axis()
				.scale(x)
				.tickValues(ra)
				.tickFormat(function(d) { return samples[d];});

				var yAxis = d3.svg.axis().scale(y).orient("left");

				vis.append("svg:g")
    				.attr("transform", "translate(0," + (height - MARGINS.bottom) + ")")
    				.call(xAxis);	

				vis.append("svg:g")
				.attr("transform", "translate(" + (MARGINS.left) + ",0)")
				.call(yAxis);

				var lineGen = d3.svg.line()
  				.x(function(d) {
    					return x(d.Sample);
  				})
 				.y(function(d) {
    					return y(d.val);
  				});
				
				vis.append('svg:path')
  				.attr('d', lineGen(gData))
  				.attr('stroke', 'green')
  				.attr('stroke-width', 2)
  				.attr('fill', 'none');
				
			}
			$("#expressionForm").hide();
			$("#goBack").show();
			$("#goBack").css("height","136px");	
			$('#lower-rect').removeAttr('style');
		});
	});

});
