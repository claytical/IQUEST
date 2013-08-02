function drawVisualization() {
  // Create and populate the data table.
 var flowData = google.visualization.arrayToDataTable([
['x', 'Over 12 hours of flow', '4-12 hours', 'Up to 3 hours', 'No flow'],
['8/31', 100, 200, 141, 200],
['9/1', 102, 244, 130, 180],
['9/2', 150, 256, 190, 210],
['9/3', 170, 233, 180, 177],
['9/4', 244, 100, 300, 190],
['9/5', 205, 270, 189, 200],

  ]);

  var colorData = google.visualization.arrayToDataTable([
    ['Date', 'Clear', 'Discolored or Cloudy'],
  ['08/31/2013',35,65],
  ['09/01/2013',55,45],
  ['09/02/2013',30,70],
  ['09/03/2013',60,40]
  ]);
  

  var particleData = google.visualization.arrayToDataTable([
    ['Date', 'None', 'Some Particles'],
  ['08/31/2013',35,65],
  ['09/01/2013',55,45],
  ['09/02/2013',30,70],
  ['09/03/2013',60,40]
  ]);


    var tasteData = google.visualization.arrayToDataTable([
    ['Date', 'Good', 'Tastes or smells bad'],
  ['08/31/2013',35,65],
  ['09/01/2013',55,45],
  ['09/02/2013',30,70],
  ['09/03/2013',60,40]
  ]);

  // Create and draw the visualization.
  new google.visualization.LineChart(document.getElementById('flow')).
      draw(flowData, {
              title:"Hours of Flow",
              curveType: "function",
                  legend: {alignment: "left", position: "right"},

                  width: $('body').width() - 20, height: 400
                  }
          );


  new google.visualization.LineChart(document.getElementById('color')).
      draw(colorData, {
              title:"Water Color",
              curveType: "function",
                  legend: {alignment: "left", position: "right"},

                  width: $('body').width() - 20, height: 400
                  }
          );

 new google.visualization.LineChart(document.getElementById('taste')).
      draw(tasteData, {
              title:"Odor/Taste",
              curveType: "function",
                  legend: {alignment: "left", position: "right"},

                  width: $('body').width() - 20, height: 400
                  }
          );


  new google.visualization.LineChart(document.getElementById('particles')).
      draw(particleData, {
              title:"Particles",
              curveType: "function",
                  legend: {alignment: "left", position: "right"},

                  width: $('body').width() - 20, height: 400
                  }
          );
}

      
      

      google.setOnLoadCallback(drawVisualization);