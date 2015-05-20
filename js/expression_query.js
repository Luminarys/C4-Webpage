function average(d) {
	var sum = 0;
	var counter = 0;
	for (var key in d){
		sum+=parseFloat(d[key]);	
		counter+=1;
	}
	return sum/counter;
}

function meanNormalize(d) {
	av = average(d);
	for (var key in d){
		d[key] = d[key]/av;
	}
	return d;
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function iqr(k) {
  return function(d, i) {
    var q1 = d.quartiles[0],
        q3 = d.quartiles[2],
        iqr = (q3 - q1) * k,
        i = -1,
        j = d.length;
    while (d[++i] < q1 - iqr);
    while (d[--j] > q3 + iqr);
    return [i, j];
  };
}

function processBPData(info, genes){
	var max = 0;
	var samples = []
	for(var i = 0;i < genes.length;i++) {
		var cArr = info[genes[i]];
		//Generate an associative array based on averages
		var co = 0;
		for (var key in cArr){
			var subArr = cArr[key];
			var av = meanNormalize(subArr);
			//console.log(av);
			console.log(i);
			if (i < 1) {
				samples[co] = [key,[]];
			}
				for(var j = 0;j < av.length;j++){
					if(av[j] > max) max = av[j];
					//console.log("pushing new val");
					//console.log(samples);
					samples[co][1].push(av[j]);
				}
			co++;
		}

	}	
	return [samples,max];

}

function boxPlot(info, genes){
	var labels = true; // show the text labels beside individual boxplots?

	var margin = {top: 30, right: 50, bottom: 90, left: 50};
	var  width = 1350 - margin.left - margin.right;
	var height = 500 - margin.top - margin.bottom;
	var min = 0;
	//process the data
	var r = processBPData(info, genes);	
	var data = r[0];
	var max = r[1];
	console.log(data);
	var chart = d3.box()
		.whiskers(iqr(1.5))
		.height(height)	
		.domain([min, max])
		.showLabels(labels);

	var svg = d3.select("#qTable").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.attr("class", "box")    
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
	var x = d3.scale.ordinal()	   
		.domain( data.map(function(d) { console.log(d); return d[0] } ) )	    
		.rangeRoundBands([0 , width], 0.7, 0.3); 		

	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom");

	var y = d3.scale.linear()
		.domain([min, max])
		.range([height + margin.top, 0 + margin.top]);
	
	var yAxis = d3.svg.axis()
    		.scale(y)
    		.orient("left");

	svg.selectAll(".box")	   
      		.data(data)
	  	.enter().append("g")
		.attr("transform", function(d) { return "translate(" +  x(d[0])  + "," + margin.top + ")"; } )
      		.call(chart.width(x.rangeBand())); 

	svg.append("text")
        .attr("x", (width / 2))             
        .attr("y", 0 + (margin.top / 2))
        .attr("text-anchor", "middle")  
        .style("font-size", "18px") 
        //.style("text-decoration", "underline")  
                .text("Mean Normalized Expression Across Samples");

	svg.append("g")
        .attr("class", "y axis")
        .call(yAxis)
		.append("text") // and text1
		  .attr("transform", "rotate(-90)")
		  .attr("y", 6)
		  .attr("dy", ".71em")
		  .style("text-anchor", "end")
		  .style("font-size", "16px") 
		  .text("Mean Normalized Expression");		

	svg.append("g")
      		.attr("class", "x axis")
      		.attr("transform", "translate(0," + (height  + margin.top + 10) + ")")
      		.call(xAxis)
	  	.append("text")             // text label for the x axis
        	.attr("x", (width / 2) )
        	.attr("y",  25 )
		.attr("dy", ".71em")
        	.style("text-anchor", "middle")
		.style("font-size", "16px") 
        	.text("Sample"); 

	
}

function linePlot(info, texts){
	t2 = info;
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
		}
		var y = d3.scale.linear()
		.domain([d3.min(gData, function(datum) { return datum.val; }), d3.max(gData, function(datum) { return datum.val; })])
		.range([height - MARGINS.top, MARGINS.bottom])
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

}
var test = "";

function handleData(data, texts){
	$("#qTable").empty();
	console.log(data);
	var info = JSON.parse(data);
	test = JSON.stringify(info);
	
	if (document.getElementById('boxPlot').checked){
		boxPlot(info, texts);
	}else{
		linePlot(info, texts);
	}

}

var t2 = "";
$(document).ready(function() {
	
	console.log("document is ready");

	//Handle the expression query
	$('#expressionQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();

		$("#expressionForm").hide();
		$("#goBack").show();
		$("#goBack").css("height","136px");	
		$('#lower-rect').removeAttr('style');

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
		req+=("&spec=" + vals[2]);
		console.log(req);
		//Handle the GET request
		//$.get(req, handleData(data));
		$.get(req, function (data) {
			handleData(data,texts);
		});
		console.log(test);
	});
});
