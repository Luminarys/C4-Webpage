function handleData(data){	
	console.log(data);
	var info = JSON.parse(data);
}

$(document).ready(function() {

	var spec = "Zmays";
		$('#lower-rect').removeAttr('style');
	var width = 900,
    		height = 1400;

var color = d3.scale.category20();

var force = d3.layout.force()
    .charge(-150)
    .linkDistance(10)
    .size([width, height]);

var svg = d3.select("#qTable").append("svg")
    .attr("width", width)
    .attr("height", height);
console.log("Added in svg");
var gnum = 4;
d3.json("php/gene_query.php?g0=GRMZM2G004528&spec=Zmays&network=true&g1=GRMZM2G152470&g2=GRMZM2G403620&g3=GRMZM2G149272", function(error, graph) {
  force
      .nodes(graph.nodes)
      .links(graph.edges)
      .start();
console.log("Added in forcelayout");

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

  node.append("title")
      .text(function(d) { return d.name; });
	
	

  force.on("tick", function() {
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node.attr("cx", function(d) { return d.x; })
        .attr("cy", function(d) { return d.y; });
	var seg = height/(gnum+1);
	for (var i = 0; i < gnum;i++){
	graph.nodes[i].y = (i+1) * seg + 75;
   	graph.nodes[i].x = width / 2;
	}
  });
    addGraphPopups();
});

});
