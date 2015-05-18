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
		req = "php/expression_query.php?";
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
