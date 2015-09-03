<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Title of the document</title>
<script type="text/javascript" src="d3.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.2.min.js"></script> 
<script type="text/javascript" src="jquery.tipsy.js"></script>
<link href="tipsy.css" rel="stylesheet" type="text/css" />
</head>
<style>
path.link {
	fill:none;
	stroke:#666;
	stroke-width:1.5px;
}

circle {
	fill:#ccc;
	stroke:#fff;
	stroke-width:1.5px;
}
text {
	fill:000;
	font: 10px sans-serif;
	pointer-events:none;
}
</style>
<body>
 
<script>
var w = 8000, h = 5000; 
// get the data
d3.csv("datasave.csv", function(error, links) {

var nodes = {};

// Compute the distinct nodes from the links.
links.forEach(function(link) {
    link.source = nodes[link.source] || 
        (nodes[link.source] = {name: link.source});
    link.target = nodes[link.target] || 
        (nodes[link.target] = {name: link.target});
    link.value = +link.value;
});

var width = 1500,
    height = 800;

var color = d3.scale.category10();

var force = d3.layout.force()
    .nodes(d3.values(nodes))
    .links(links)
    .size([width, height])
   // .gravity(10)
    .linkDistance(60)
    .charge(-300)
    .on("tick", tick)
    .start();

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

var vis = d3.select("#chart").append("svg:svg")
          .attr("width", w)
          .attr("height", h);



// build the arrow.
svg.append("svg:defs").selectAll("marker")
    .data(["end"])
  .enter().append("svg:marker")
    .attr("id", String)
    .attr("viewBox", "0 -5 10 10")
    .attr("refX", 15)
    .attr("refY", -1.5)
    .attr("markerWidth", 6)
    .attr("markerHeight", 6)
    .attr("orient", "auto")
  .append("svg:path")
    .attr("d", "M0,-5L10,0L0,5");

// add the links and the arrows
var path = svg.append("svg:g").selectAll("path")
    .data(force.links())
  .enter().append("svg:path")
    .attr("class", "link")
    .attr("marker-end", "url(#end)");

// define the nodes
var node = svg.selectAll(".node")
    .data(force.nodes())
  .enter().append("g")
    .attr("class", "node")
    .style("fill", function(d) { return color(d.name); })
    .on("mouseover", mouseover)
    .on("mouseout", mouseout)
    .call(force.drag);



// add the nodes
node.append("circle")
    .attr("r", 10);

// add the text 
// node.append("text")
//     .attr("x", 12)
//     .attr("dy", ".35em")
//     .text(function(d) { return d.name; });

$('svg circle').tipsy({ 
        gravity: 'w', 
        html: true, 
        title: function() {
          var d = this.__data__;
          var e = d.name;
          return 'This person is: ' + e; 
        }
      });


function mouseover() {
  d3.select(this).select("circle").transition()
      .duration(750)
      .attr("r", 20);
}

function mouseout() {
  d3.select(this).select("circle").transition()
      .duration(750)
      .attr("r", 10);
}

// add the curvy lines
function tick() {
    path.attr("d", function(d) {
        var dx = d.target.x - d.source.x,
            dy = d.target.y - d.source.y,
            dr = Math.sqrt(dx * dx + dy * dy);
        return "M" + 
            d.source.x + "," + 
            d.source.y + "A" + 
            dr + "," + dr + " 0 0,1 " + 
            d.target.x + "," + 
            d.target.y;
    });

    node
        .attr("transform", function(d) { 
            return "translate(" + d.x + "," + d.y + ")"; });
}

});

</script>

</body>

</html> 