function average(d) {
	var sum = 0;
	var counter = 0;
	for (var key in d){
		sum+=parseFloat(d[key]);	
		counter+=1;
	}
	return sum/counter;
}

function cMax(d) {
	var max = 0;
	for (var key in d){
		val = parseFloat(d[key]);
		if (val > max){
			max = val;
		}
	}
	return max;
}

function std(d,av){
	var error = 0;
	for (var key in d){
		error+= ((parseFloat(d[key]) - av) * (parseFloat(d[key]) - av));	
	}
	return error;
}

function meanNormalize(d, av) {
	for (var key in d){
		d[key] = d[key]/av;
	}
	return d;
}

function maxNormalize(d, max) {
	for (var key in d) {
		d[key] = d[key]/av;	
	}
	return d;
}

function logNormalize(d) {
	for (var key in d) {
		//This ain't gonna work properly for anything equal to 0
		if(d[key] != 0){
			d[key] = Math.log2(d[key]);	
		}
		
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

function getRandomColor() {
    var letters = '012345'.split('');
    var color = '#';        
    color += letters[Math.round(Math.random() * 5)];
    letters = '0123456789ABCDEF'.split('');
    for (var i = 0; i < 5; i++) {
        color += letters[Math.round(Math.random() * 15)];
    }
    return color;
}

function getMax(info, genes){
	var max = 0;
	for (var i = 0;i < genes.length;i++) {
		var cArr = info[genes[i]];
		for (var key in cArr){
			var subArr = cArr[key];
			var cmax = cMax(subArr);
			if (cmax > max){
				max = cmax;
			}
		}
	}
	return max;
}

//DOES NOT WORK - NEED TO FIGURE OUT HOW TO DEAL WITH EXPRESSION VALUES EQUAL TO 0
function logNormalizeBPData(info, genes){
	var samples = []
	var max = 0;
	var min = 99999;
	for(var i = 0;i < genes.length;i++) {
		var cArr = info[genes[i]];
		//Generate an associative array based on averages
		var co = 0;
		for (var key in cArr){
			var subArr = cArr[key];
			var av = logNormalize(subArr);
			//console.log(av);
			console.log(i);
			if (i < 1) {
				samples[co] = [key,[]];
			}
				for(var j = 0;j < av.length;j++){
					if(av[j] > max) max = av[j];
					if(av[j] < min) min = av[j];
					//console.log("pushing new val");
					//console.log(samples);
					samples[co][1].push(av[j]);
				}
			co++;
		}

	}	
	//console.log(samples);
	return [samples,max,min];

}

function maxNormalizeBPData(info, genes){
	var samples = []
	for(var i = 0;i < genes.length;i++) {
		var cArr = info[genes[i]];
		//Generate an associative array based on averages
		var max = 0;
		var co = 0;
		for (var key in cArr){
			var subArr = cArr[key];
			for (var k in subArr) {
				if (max < parseFloat(subArr[k])) {
					max = parseFloat(subArr[k]);
				}
			} 		
		}
		for (var key in cArr){
			var subArr = cArr[key];
			var norm = meanNormalize(subArr,max);
			console.log(norm);
			//console.log(av);
			if (i < 1) {
				samples[co] = [key,[]];
			
				for(var j = 0;j < norm.length;j++){
					samples[co][1].push(norm[j]);
				}
			co++;
			}
		}

	}	
	return samples;

}

function meanNormalizeBPData(info, genes){
	var samples = []
	var max = 0;
	for(var i = 0;i < genes.length;i++) {
		var cArr = info[genes[i]];
		//Generate an associative array based on averages
		var co = 0;
		var sum = 0;
		var count = 0;
		for (var key in cArr){
			var subArr = cArr[key];
			for (var k in subArr) {
				sum+=parseFloat(subArr[k]);
				count+=1;
			} 		
		}
		console.log(sum);
		console.log(count);
		var average = sum/count;
		console.log(average);
		for (var key in cArr){
			var subArr = cArr[key];
			var av = meanNormalize(subArr,average);
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
	var labels = false; // show the text labels beside individual boxplots?

	var margin = {top: 30, right: 50, bottom: 90, left: 50};
	var  width = 1350 - margin.left - margin.right;
	var height = 500 - margin.top - margin.bottom;
	var min = 0;
	//process the data
	var r, data, max;
	console.log(normMeth);
	//LOG NORM. DOES NOT WORK
	if (normMeth == "mean") {
		r = logNormalizeBPData(info, genes);	
		//r = meanNormalizeBPData(info, genes);	
		data = r[0];
		max = r[1];
		min = r[2];
	}else if (normMeth == "max") {
		//r = meanNormalizeBPData(info, genes);	
		data = maxNormalizeBPData(info, genes);	
		max = 1;
	}

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
        .attr("y",-20 + (margin.top / 2))
        .attr("text-anchor", "middle")  
        .style("font-size", "18px") 
        //.style("text-decoration", "underline")  
                .text("Normalized Expression Across Samples");

	svg.append("g")
        .attr("class", "y axis")
        .call(yAxis)
		.append("text") // and text1
		  .attr("transform", "rotate(-90)")
		  .attr("y", 6)
		  .attr("dy", ".71em")
		  .style("text-anchor", "end")
		  .style("font-size", "16px") 

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

		$("normalizationDiv").show();	
}

function dotPlot(info, texts){
	if (false){
		var info = meanNormalizeLPData(info, texts);
	}else{

	}
	var vis;
	var width = 1000;
	var height = 400;
	var MARGINS = {
		top: 50,
       		right: 20,
       		bottom: 50,
       		left: 50
	}
	var colors = ["green"];
	var inactiveLines = {};
	for (var i = 0;i < texts.length;i++) {
		var cArr = info[texts[i]];
		var max = 0;
		for (var key in cArr){
			var subArr = cArr[key];
			var cmax = cMax(subArr);
			if (cmax > max){
				max = cmax;
			}
		}
		inactiveLines[texts[i]] = false;
		var gData = [];
		//console.log(cArr);
		//Generate an associative array based on averages
		var co = 0;
		var samples = [];
		for (var key in cArr){
			var subArr = cArr[key];
			gData.push({Sample:co++, val:subArr});
			samples.push(key);
		}
		console.log(gData);

		vis = d3.select("#qTable")
			.append("svg:svg")
			.attr("width", width)
			.attr("height", height);

		var y = d3.scale.linear()
		.domain([0, max])
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
		.attr("class","axis")
    		.attr("transform", "translate(0," + (height - MARGINS.bottom) + ")")
    		.call(xAxis);	

		vis.append("svg:g")
		.attr("class","axis")
		.attr("transform", "translate(" + (MARGINS.left) + ",0)")
		.call(yAxis);
		
		//Make the dotplot	
		var cpos = 0;
		for (var key in cArr){
			var subArr = cArr[key];
			for(var k = 0;k < subArr.length;k++) {
				var cdot = subArr[k];
				vis.append("circle")
				.attr("class","dot")
				.attr("cx",x(cpos))
				.attr("cy",y(cdot))
				.attr("r", 3.5)
				.style("fill","green");
			}
			cpos++;
		}
	}


}

function linePlot(info, texts){
	if (false){
		var info = meanNormalizeLPData(info, texts);
	}else{

	}
	var max = getMax(info, texts);
	var vis;
	var width = 1000;
	var height = 400 + 60 * Math.floor(texts.length/5);
	var MARGINS = {
		top: 50 + 30 * Math.floor(texts.length/5),
       		right: 20,
       		bottom: 50 + 30 * Math.floor(texts.length/5),
       		left: 50
	}
	var colors = ["green"];
	var inactiveLines = {};
	for (var i = 0;i < texts.length;i++) {
		inactiveLines[texts[i]] = false;
		var cArr = info[texts[i]];
		var gData = [];
		//console.log(cArr);
		//Generate an associative array based on averages
		var co = 0;
		var samples = [];
		for (var key in cArr){
			var subArr = cArr[key];
			var av = average(subArr);
			var sd = std(subArr, av);
			//console.log(av);
			gData.push({Sample:co++, val:av, dev:sd});
			samples.push(key);
		}

		console.log(gData);
		if(i == 0 || !combine){
		vis = d3.select("#qTable")
			.append("svg:svg")
			.attr("width", width)
			.attr("height", height);
			if(combine){
				var y = d3.scale.linear()
				.domain([0, max])
				.range([height - MARGINS.top, MARGINS.bottom])
			}else{
				var y = d3.scale.linear()
				.domain([0, d3.max(gData, function(datum) { return datum.val; })])
				.range([height - MARGINS.top, MARGINS.bottom])
			}
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
			.attr("class","axis")
    			.attr("transform", "translate(0," + (height - MARGINS.bottom) + ")")
    			.call(xAxis);	
                	
			vis.append("svg:g")
			.attr("class","axis")
			.attr("transform", "translate(" + (MARGINS.left) + ",0)")
			.call(yAxis);
                	
			var lineGen = d3.svg.line()
  			.x(function(d) {
    				return x(d.Sample);
  			})
 			.y(function(d) {
    				return y(d.val);
  			})
			.interpolate("basis");
					
			vis.append('svg:path')
  			.attr('d', lineGen(gData))
  			.attr('stroke', 'green')
  			.attr('stroke-width', 2)
			.attr("id", "tag" + texts[i].replace(/\s+/g, ""))
  			.attr('fill', 'none');
		if(!combine){
			vis.append("text")
        		.attr("x", (width / 2))             
        		.attr("y",(MARGINS.top / 2))
        		.attr("text-anchor", "middle")  
        		.style("font-size", "18px") 
                	.text(texts[i]);
		}
		}else{
			if (multiColor == "multi"){
				var col = getRandomColor();
			}else{
				var col = "green";
			}
			colors.push(col);
			vis.append('svg:path')
			.attr('d', lineGen(gData))
  			.attr('stroke', col)
  			.attr('stroke-width', 2)
			.attr("id", "tag" + texts[i].replace(/\s+/g, ""))
  			.attr('fill', 'none');
		}
	}
	if(combine){
		if(texts.length < 5){
			var lspace = width/texts.length;
		}else{
			var lspace = width/5;
		}
		var crow = 0;
		for(var i = 0;i < texts.length;i++){
			if(i%5 == 0 && i != 0) crow++;
			console.log((lspace/2 + (i % 5)*lspace) - 40);
			vis.append("text")
			.attr("x", (lspace/2 + (i % 5)*lspace) - 40)
			.attr("y", height - (MARGINS.bottom/2) + 15 + (25 * crow))
			.attr("class", "legend")
			.style("fill", function() { return colors[i]; })
			.on("click", function() {
				var active = inactiveLines[$(this).text()] ? false : true,
				newOpacity = active ? 0 : 1;
				console.log($(this).text());
				d3.select("#tag" + $(this).text().replace(/\s+/g, ''))
					.transition().duration(100)
					.style("opacity", newOpacity);
				inactiveLines[$(this).text()] = active;
			})
			.text(texts[i]);
		}
	}

}
var test = "";
var combine = false;
var multiColor = "uni";
var normMeth = "max";

function handleInitData(data, texts){
	$("#qTable").empty();
	console.log(data);
	var info = JSON.parse(data);
	test = JSON.stringify(info);
	multiColor = $("#geneColor").val();
	normMeth = $("#normalization").val();
	console.log(normMeth);
	$("#normalization-in").val(normMeth);
	if(plot == "box"){
		$("#qTable").empty();
		boxPlot(info, texts);
		$("#inGraphOpts").show();
		$("#combinePlotsDiv-in").hide();
		$("#normalizationPlotsDiv-in").show();
		$("#plotType-in").val("box");
		$("#geneColorDiv-in").hide();
	}else if(plot == "line"){
		$("#qTable").empty();
		if ($("#combinePlots").val() == "combine"){
			combine = true;
			$("#combinePlots-in").val("combine");
		}else{
			combine = false;
			$("#combinePlots-in").val("noCombine");
		}
		linePlot(info, texts);
		$("#inGraphOpts").show();
		$("#normalizationPlotsDiv-in").hide();
		$("#combinePlotsDiv-in").show();
		$("#geneColorDiv-in").show();
		$("#plotType-in").val("line");
	}else if(plot == "dot"){
		$("#qTable").empty();
		dotPlot(info, texts);
		$("#inGraphOpts").show();
		$('#combinePlotsDiv-in').hide();
		$("#geneColorDiv-in").hide();
		$("#normalizationPlotsDiv-in").hide();
		$("#plotType-in").val("dot");
	}
	console.log(multiColor);

}
//Handles changes within the graph
function handleReData(data, texts){
	$("#qTable").empty();
	console.log(data);
	var info = JSON.parse(data);
	test = JSON.stringify(info);
	multiColor = $("#geneColor-in").val();
	normMeth = $("#normalization-in").val();
	if(plot == "box"){
		boxPlot(info, texts);
		$("#inGraphOpts").show();
		$("#combinePlotsDiv-in").hide();
		$("#normalizationPlotsDiv-in").show();
		$("#geneColorDiv-in").hide();
	}else if(plot == "line"){
		if ($("#combinePlots-in").val() == "combine"){
			combine = true;
		}else{
			combine = false;
		}
		linePlot(info, texts);
		$("#inGraphOpts").show();
		$("#normalizationPlotsDiv-in").hide();
		$("#combinePlotsDiv-in").show();
		$("#geneColorDiv-in").show();
	}else if(plot == "dot"){
		$("#inGraphOpts").show();
		dotPlot(info, texts);
		$('#combinePlotsDiv-in').hide();
		$("#geneColorDiv-in").hide();
		$('#normalizationDiv-in').hide();	
	}
	console.log(multiColor);

}

function genReq(){
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
	req+=("&spec=" + vals[4]);
	console.log(req);
	return [req, texts];
}

function rePlot(){
	qRes = genReq();
	req = qRes[0];
	texts = qRes[1];
	$.get(req, function (data) {
		plot = $("#plotType-in").val();
		handleReData(data,texts);
	});
}

var t2 = "";
var plot = "";
var norm = "";
$(document).ready(function() {
	plot = $("#plotType").val();
	if(plot == "box"){
		$('#combinePlotsDiv').hide();
		$("#geneColorDiv").hide();
		$('#normalizationDiv').show();	
	}else if(plot == "line"){
		$('#combinePlotsDiv').show();
		$("#geneColorDiv").show();
		$('#normalizationDiv').hide();	
	}else if(plot == "dot"){
		$('#combinePlotsDiv').hide();
		$("#geneColorDiv").hide();
		$('#normalizationDiv').hide();	
	}
	$('#plotType').change(function(){
		plot = $("#plotType").val();
		if(plot == "box"){
			$('#combinePlotsDiv').hide();
			$("#geneColorDiv").hide();
			$('#normalizationDiv').show();	
		}else if(plot == "line"){
			$('#combinePlotsDiv').show();
			$("#geneColorDiv").show();
			$('#normalizationDiv').hide();	
		}else if(plot == "dot"){
			$('#combinePlotsDiv').hide();
			$("#geneColorDiv").hide();
			$('#normalizationDiv').hide();	
	}
    	});
	console.log("document is ready");
	//$("#normalizationPlotsDiv-in").hide();
	//$("#combinePlotsDiv-in").show();
	$("#combinePlotsDiv-in").change(function() {
		rePlot();
	});
	$("#geneColorDiv-in").change(function() {
		rePlot();
	});
	$("#plotTypeDiv-in").change(function() {
		rePlot();
	});
	$("#normalizationPlotsDiv-in").change(function() {
		rePlot();
	});
	$("#inGraphOpts").hide();
	//Handle the expression query
	$('#expressionQueryForm').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();

		$("#expressionForm").hide();
		$("#goBack").show();
		$("#goBack").css("height","136px");	

		//Handle the GET request
		//$.get(req, handleData(data));
		qRes = genReq();
		req = qRes[0];
		texts = qRes[1];
		$.get(req, function (data) {
			handleInitData(data,texts);
			$('#lower-rect').removeAttr('style');
		});
		console.log(test);
	});
});
