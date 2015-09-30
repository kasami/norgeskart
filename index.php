<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>D3 World Map</title>
    <style>
    body {
      margin: 20px auto;
      max-width: 900px;
    }
    path {
      stroke: white;
      stroke-width: 0.5px;
      fill: black;
      outline: none;
    }
    path:hover {
      fill: #222;
    }
    svg {
      border: 1px solid #CCC;
    }
    </style>
    <link rel="stylesheet" type="text/css" href="html5tooltips.css" />
    <link rel="stylesheet" type="text/css" href="html5tooltips.animation.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/topojson/1.6.19/topojson.min.js"></script>
    <script src="./html5tooltips.js"></script>
  </head>
  <body>
    <script type="text/javascript">


    d3.json("fylker.topojson", function(error, fylker) {
      if (error) return console.error(error);

      console.log(fylker);

      var collection = topojson.feature(fylker, fylker.objects.collection);

      var width = 500;
      var height = 590;

      var svg = d3.select("body").append("svg")
          .attr("width", width)
          .attr("height", height);

      var projection = d3.geo.mercator()
          .center([18.0, 65.5])
          .scale(1000)
          .translate([width / 2, height / 2]);

      var path = d3.geo.path()
        .projection(projection);

      svg.selectAll(".fylke")
        .data(topojson.feature(fylker, fylker.objects.collection).features)
        .enter().append("path")
        .attr("id", function(d) { return "fid" + d.properties.FylkeNr; })
        .attr("title", function(d) { return d.properties.NAVN; })
        .attr("d", path);
    });

    d3.json("http://data.ssb.no/api/v0/dataset/1102.json?lang=no", function(error, data)
    {
      if (error) return console.error(error);

      fylkesindeks = data.dataset.dimension.Region.category.index;
      tallindeks = data.dataset.dimension.ContentsCode.category.index;
      tallnavn = data.dataset.dimension.ContentsCode.category.label;

      var tooltipArray = [];

      Object.keys(fylkesindeks).forEach(function(key) {

        fylkespath = d3.select("#fid"+parseInt(key));
        if(fylkespath.node()) {
          var contentMoreText = "";
          Object.keys(tallindeks).forEach(function(tkey) {
            contentMoreText = contentMoreText +  tallnavn[tkey] + ": " + data.dataset.value[tallindeks[tkey]+(11*fylkesindeks[key])] + "<br/>";
            fylkespath.attr("data-"+tkey, data.dataset.value[tallindeks[tkey]+(11*fylkesindeks[key])]);
            if (data.dataset.value[9+(11*fylkesindeks[key])] > 0) {
              fylkespath.attr("style", "fill:green");
            }
            else {
             fylkespath.attr("style", "fill:red"); 
            }
          });
          var tooltip = {
            targetSelector : "#fid"+parseInt(key),
            contentText : "<h2>" +fylkespath.attr("title") + "</h2>" + contentMoreText,
            maxWidth: 400,
            animateFunction: "spin",
          };
          tooltipArray.push(tooltip)
        }
      })

      console.log(tooltipArray);
      html5tooltips(tooltipArray);

      window.data = data;
      
    });
    </script>
  </body>
</html>