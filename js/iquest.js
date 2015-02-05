 var map, a, vector_style_2, Hazard;
 var loc;
 var noCharts = true;
 var googleChart;


function drawNeighborhoodLineChart(chartTitle, set, neighborhood, start, end) {
    $("#" + set + "_line").html("<img src='img/ajax-loader.gif'/>");

  $.getJSON('api.php?set='+set+'&neighborhood='+neighborhood+'&start='+start+'&end='+end+"&percent=FALSE", function(data) {
    var continuousData = new google.visualization.DataTable();
    var dateColumn = 0;
    for (var i = 0; i < data.headers.length; i++) {
      continuousData.addColumn(data.headers[i]["type"], data.headers[i]["label"]);
      if (data.headers[i]["type"] == "date") {
        dateColumn = i;
      }
    }
    for (var i = 0; i < data.values.length; i++) {
      //convert mysql date to javascript date
        var tmpArray = data.values[i];
        tmpArray[dateColumn] = new Date([data.values[i][dateColumn]]);
        continuousData.addRow(tmpArray);
    }

    
    var continuousChart = new google.visualization.LineChart(document.getElementById(set + '_line'));
    continuousChart.draw(continuousData, {
                    title:chartTitle,
                    chartArea: {left: 40},
                    curveType: "function",
                    legend: {alignment: "left", position: "right"},
                    width: ($('body').width() - 20),
                    height: 400
      });    
  });

}

function drawNeighborhoodBarChart(chartTitle, set, neighborhood, start, end) {
    $("#" + set + "_bar").html("<img src='img/ajax-loader.gif'/>");

  var colorSet;
    $("#" + set + "_bar").html("<img src='img/ajax-loader.gif'/>");

  $.getJSON('api.php?set='+set+'&neighborhood='+neighborhood+'&start='+start+'&end='+end+"&percent=TRUE", function(data) {
    setMostCommonData(set, data.highest);

    var continuousData = new google.visualization.DataTable();
    var dateColumn = 0;
    for (var i = 0; i < data.headers.length; i++) {
      continuousData.addColumn(data.headers[i]["type"], data.headers[i]["label"]);
      if (data.headers[i]["type"] == "date") {
        dateColumn = i;
      }
    }
    for (var i = data.values.length - 1; i > 0; i--) {
      //convert mysql date to javascript date
        var tmpArray = data.values[i];
        tmpArray[dateColumn] = new Date([data.values[i][dateColumn]]);
        continuousData.addRow(tmpArray);
    }
    
//    var showEvery = parseInt(chartData.getNumberOfRows() / 200);      
      if (data.values[0].length == 3) {
        colorSet = ["#2A58A8", "#95B7C7"];
      }
      else {
        colorSet = ["#2A58A8", "#0089C7", "#95B7C7", "#E0E0E0"]
      }

//            vAxis: {format:'#.#%'},

      var continuousChart = new google.visualization.AreaChart(document.getElementById(set + '_bar')).
          draw(continuousData,
           {title:chartTitle,
            legend: {alignment: "left", position: "right"},
            colors: colorSet,
            width: ($('body').width() - 20),
            height: 400,
            chartArea: {left: 60},
            isStacked: true}
      )
    
  });
}


function drawFrequencyLineChart(end, start) {
    $("#overview_line").html("<img src='img/ajax-loader.gif'/>");
  $.getJSON('api.php?set=frequency&start='+start+'&end='+end, function(data) {
    var continuousData = new google.visualization.DataTable();
    var dateColumn = 0;
    for (var i = 0; i < data.headers.length; i++) {
      continuousData.addColumn(data.headers[i]["type"], data.headers[i]["label"]);
    }
    for (var i = 0; i < data.values.length; i++) {
      //convert mysql date to javascript date

        var tmpArray = data.values[i];
        tmpArray[dateColumn] = new Date([data.values[i][dateColumn]]);
        continuousData.addRow(tmpArray);
    }

    
    var continuousChart = new google.visualization.LineChart(document.getElementById('overview_line'));
    continuousChart.draw(continuousData, {
                    title:"SMS Frequency by Neighborhood",
                    chartArea: {left: 40},
                    curveType: "function",
                    legend: {alignment: "left", position: "right"},
                    width: ($('body').width() - 20),
                    height: 400
      });    
    });

}
 

function loadEverything() {
  set_date("neighborhood", "7 Days");
  set_date("all", "7 Days");

    populateAllCharts();
    $('#chartTabsAll a[href="#overview"]').tab('show');

    $.getJSON("api.php?set=neighborhoods", function (data) {
             $.each(data, function (i, data) {
                 var jsondata = "<li><a href='#' onclick='switchToMapWithLocation("+data.id+")''>" + data.location + "</a></li>";
                 $(jsondata).appendTo("ul.neighborhoods");
             });
      });

  $('#dpStart').datepicker();
  $('#dpEnd').datepicker();
  $('#dpStart2').datepicker();
  $('#dpEnd2').datepicker();
  $('.dateRangeSelector').click(function() {
    event.preventDefault();
    $('.dateRangeSelector').removeClass('active');
    $(this).addClass("active");
    set_date("neighborhood", $(this).children("a").html())
    set_date("all", $(this).children("a").html())
    populateAllCharts();
    populateCharts();
  });
}

function get_indexes() {
    $.getJSON('api.php?set=index&neighborhood=all&start='+$('#dpStart').val()+'&end='+$('#dpEnd').val(), function (data) {
/*           $.each(data, function (i, data) {
               var jsondata = "<li><a href='#' onclick='switchToMapWithLocation("+data.id+")''>" + data.location + "</a></li>";
               $(jsondata).appendTo("ul.neighborhoods");
           });*/
    var continuousData = new google.visualization.DataTable();
    
    continuousData.addColumn("string", "Neighborhood");
    continuousData.addColumn("number", "Index Rating");
    continuousData.addRows(data);

    
    var continuousChart = new google.visualization.BarChart(document.getElementById('index_line'));
    continuousChart.draw(continuousData, {
                    title:"Neighborhoods",
                    chartArea: {left: 150},
                    curveType: "function",
                    legend: 'none',
                    hAxis: {maxValue: 100, minValue: 0},
                    width: ($('body').width() - 20),
                    height: 400
      });    




    });

}

function set_date(view, range) {

  var today = moment();
  var tmpDate, endDate, startDate;
  if (view == "neighborhood") {
    endDate = $('#dpEnd');
    startDate = $('#dpStart');
    $('#custom_date').hide();
  }
  if (view == "all") {
    endDate = $('#dpEnd2');
    startDate = $('#dpStart2');
    $('#custom_date_all').hide();
  }  

  endDate.val(today.format("YYYY-MM-DD"));
  
  switch(range) {
    case 'Custom':
      $('#custom_date').show();
      $('#custom_date_all').show();
      break;
    case 'Today':
        startDate.val(today.format("YYYY-MM-DD"));
      break;
    case '7 Days':
        tmpDate = moment().subtract('days', 7);
        startDate.val(tmpDate.format("YYYY-MM-DD"));
        break;
    case '14 Days':
        tmpDate = moment().subtract('days', 14);
        startDate.val(tmpDate.format("YYYY-MM-DD"));
        break;    
    case '30 Days':
        tmpDate = moment().subtract('days', 30);
        startDate.val(tmpDate.format("YYYY-MM-DD"));
        break;    
    case '3 Months':
        tmpDate = moment().subtract('months', 3);
        startDate.val(tmpDate.format("YYYY-MM-DD"));
        break;    
    case '1 Year':
        tmpDate = moment().subtract('years', 1);
        startDate.val(tmpDate.format("YYYY-MM-DD"));
        break;    

    default:
    }
}

function showAll() {
  $('a#active_neighborhood').html("Neighborhood <b class='caret'></b>");
  $('#view_neighborhood').hide();
  $('#view_all').show();
}

function switchToMapWithLocation(location) {
  $('li#all_link').removeClass("active");
  $('#view_neighborhood').show();
  $('#view_all').hide();
  $('#contentMap').html("");
  initMap();
  showNeighborhood(location);
}

function setDataForNeighborhood(location) {
  $.getJSON('api.php?set=location&neighborhood='+location+'&start='+$('#dpStart').val()+'&end='+$('#dpEnd').val(), function(data) {
      $("#neighborhood").html(data.name + "<span class='label pull-right'>As of "+moment(data.collection_start, "YYYY-MM-DD").fromNow()+"</span>");
      $("#reports").html(data.reports);
      $("#median").html(data.median);
      $("#average").html(data.average);
      $("#customers").html(data.customers);
      $("#infoBox").show();
      $('a#active_neighborhood').html(data.name + " <b class='caret'></b>");

  });
  $.getJSON('api.php?set=index&neighborhood='+location+'&start='+$('#dpStart').val()+'&end='+$('#dpEnd').val(), function(data) {
      $("#index_rating").css('width', data + '%');
      $("#index_value").html(data);

  });
}

function setMostCommonData(set, data) {
  $("#common_" + set).html(data);
}

function showNeighborhood(location) {
  setDataForNeighborhood(location);  
  loc = location;
  populateCharts();


}

function populateAllCharts() {

  var endDate = $('#dpEnd2').val();
  var startDate = $('#dpStart2').val();
  drawFrequencyLineChart(endDate, startDate);
  get_indexes();
}

function populateCharts() {
  event.preventDefault();
    $("#common_flow").html("<img src='img/ajax-loader.gif'/>");
    $("#common_water").html("<img src='img/ajax-loader.gif'/>");
    $("#common_particles" ).html("<img src='img/ajax-loader.gif'/>");
    $("#common_taste").html("<img src='img/ajax-loader.gif'/>");
  // Create and populate the data table.
  var endDate = $('#dpEnd').val();
  var startDate = $('#dpStart').val();
  setDataForNeighborhood(loc);
/*
  drawNeighborhoodLineChart("Amount of Responses Over Time", "water", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Line Chart", "taste", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Line Chart", "particles", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Line Chart", "flow", loc, startDate, endDate); 
  */
  drawNeighborhoodBarChart("Percent Distribution of Responses", "water", loc, startDate, endDate); 
  drawNeighborhoodBarChart("Percent Distribution of Responses", "taste", loc, startDate, endDate); 
  drawNeighborhoodBarChart("Percent Distribution of Responses", "particles", loc, startDate, endDate);   
  drawNeighborhoodBarChart("Percent Distribution of Responses", "flow", loc, startDate, endDate); 
  $('#chartTabs a[href="#water"]').tab('show');
  $('#chartOptions').show();
}

function initMap() {
   
     var options = {
            projection: new OpenLayers.Projection("EPSG:900913"),
            displayProjection: new OpenLayers.Projection("EPSG:4326"),
            units: "m",
            numZoomLevels: 18,
            maxResolution: 'auto',
            maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508.34)//, 
      
    };
    map = new OpenLayers.Map('contentMap',options);
    
    var vector_style = new OpenLayers.Style({
      'cursor': 'pointer',
      'fillColor': '#787878',
      'fillOpacity': .6,
      'fontColor':'#FFFFFF',
      'label': '${District}', 
      'pointRadius': 2,
      'strokeColor': '#232323',
      'fontSize': '10px', 
      'labelAlign' :  'c'
      });
      
      vector_style_2 = new OpenLayers.Style({
      'cursor': 'pointer',
      'fillColor': '#000000',
      'fontColor': '#343434',
      'pointRadius': 6,
      'strokeColor': '#232323', 
      'labelAlign' :  'tr'
      });

    var gmap = new OpenLayers.Layer.Google(
        "Google Streets",
          {isBaseLayer: true, numZoomLevels: 40, sphericalMercator: true}
      );
    map.addLayer(gmap);
        
    var Hazard = new OpenLayers.Layer.Vector("iQuest", {
            projection: map.displayProjection,
            eventListeners: {
              'beforefeatureadded': draw_event, 
              'featureselected': click_event 
            },
      
        strategies: [new OpenLayers.Strategy.BBOX(),  new OpenLayers.Strategy.Fixed], 
            protocol: new OpenLayers.Protocol.HTTP({
                url:"kml/ama.kml",
    format: new OpenLayers.Format.KML({
                    extractStyles: false, 
                    extractAttributes: true,
                 
                })
            })
        });
  
  
        map.addLayer(Hazard);
      var vector_style_map_ = new OpenLayers.StyleMap({'default': vector_style, 'select' : vector_style_2});
    
    
      Hazard.styleMap = vector_style_map_;
      
      var select_feature_control = new OpenLayers.Control.SelectFeature(Hazard);
      map.addControl(select_feature_control);
      select_feature_control.activate();
           
      var point = new OpenLayers.LonLat(-23070.48765315,617902.95096767); 
      map.setCenter(point, 14); 
     
        if(!map.getCenter()){
           map.zoomToMaxExtent();
        }

    }

  function click_event(evt)
  {
      event.preventDefault();
      showNeighborhood(evt.feature.attributes.location.value);
  }
  
  function draw_event(evt)
    {
      var a = evt.feature.attributes;
      a.District = a.Name.value;
      return true;
    }
