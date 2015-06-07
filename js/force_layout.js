function generateGraph(spec, genes){
	var gnum = genes.length;
	//Create the URL to use
	var req = "php/gene_query.php?network=true&spec=" + spec;
	for (var i = 0; i < genes.length; i++){
		req += "&g" + i + "=" + genes[i];
	}
	$('#lower-rect').removeAttr('style');
	var width = 900;
    	var height = 1400;

	var color = d3.scale.category20();
	
	var force = d3.layout.force()
	    .charge(-150)
	    .linkDistance(10)
	    .size([width, height]);
	
	var svg = d3.select("#qTable").append("svg")
	    .attr("width", width)
	    .attr("height", height);
	console.log("Added in svg");

	//Call the generated request
	d3.json(req, function(error, graph) {
		  force
		      .nodes(graph.nodes)
		      .links(graph.edges)
		      .start();

		var maxConn = graph.max;	
		var link = svg.selectAll(".link")
			.data(graph.edges)
			.enter().append("line")
			.attr("class", "link")
			.style("stroke-width", function(d) { return Math.sqrt(d.value); });
		
		console.log("Added in edges");
		
		var node = svg.selectAll(".node")
			.data(graph.nodes)
			.enter().append("circle")
			.attr("class", "node")
			.attr("value", function(d) {return "?link=true&spec=" + spec + "&gene=" + d.name;})
			.attr("r", 5)
			.style("fill", function(d) { return color(d.group); });
		      //.call(force.drag);
		      
		console.log("Added in nodes");
		     
		//Add in hovering titles 
		node.append("title")
			.text(function(d) { return d.name; });
			
		force.on("tick", function() {
			link.attr("x1", function(d) { return d.source.x; })
		       		.attr("y1", function(d) { return d.source.y; })
		        	.attr("x2", function(d) { return d.target.x; })
		        	.attr("y2", function(d) { return d.target.y; });
		
		    	node.attr("cx", function(d) { return d.x; })
		        	.attr("cy", function(d) { return d.y; });
			
			//Position vertically based on the number of sources. For 3 sources, put source 1 at 1/4 the way down,
			//source 2 at 2/4, and source 3 at 3/4. This will ensure nice placement overall
			var seg = height/(gnum+1);
			for (var i = 0; i < gnum;i++){
				graph.nodes[i].y = (i+1) * seg + 75;
		   		graph.nodes[i].x = width / 2;
			}
		});
		//Add in legend
		var colors = [];
		colors.push(["Source Node", color(0)]);
		colors.push(["1 Connection", color(1)]);
		for(var i = 2; i <= maxConn; i++){
			colors.push([ i + " Connections", color(i)]);
		}
		console.log(colors);

		var legend = svg.append("g")
			.attr("class", "legend")
			.attr("height", 100)
			.attr("width", 100)
			.attr("transform", "translate(-20, 50)");

		var legendRect = legend.selectAll('rect').data(colors);

		legendRect.enter()
    			.append("rect")
    			.attr("x", width - 110)
    			.attr("width", 10)
    			.attr("height", 10);

		legendRect
    			.attr("y", function(d, i) {
        			return i * 20;
    			})
    			.style("fill", function(d) {
        			return d[1];
    			});

		var legendText = legend.selectAll('text').data(colors);	
		legendText.enter()
    			.append("text")
    			.attr("x", width - 97);
		legendText
    			.attr("y", function(d, i) {
        			return i * 20 + 9;
    			})
    			.text(function(d) {
        			return d[0];
    			});

		//Add in qTip popups
		addGraphPopups();
	});

}

$(document).ready(function() {
	//These will be function inputs
	var spec = "Zmays";
	var genes = ["GRMZM2G152470","GRMZM2G004528","GRMZM2G403620","GRMZM2G149272"];
	generateGraph(spec, genes);
});
