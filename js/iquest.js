 var map, a, vector_style_2, Hazard;
 var loc;
 var noCharts = true;


function drawNeighborhoodLineChart(chartTitle, set, neighborhood, start, end) {
  $.getJSON('api.php?set='+set+'&neighborhood='+neighborhood+'&start='+start+'&end='+end+"&percent=FALSE", function(data) {
    var chartData = google.visualization.arrayToDataTable(data);
    var showEvery = parseInt(chartData.getNumberOfRows() / 4);      

      if(typeof data[1] != 'undefined') {

        new google.visualization.LineChart(document.getElementById(set + '_line')).
            draw(chartData, {
                    title:chartTitle,
                    chartArea: {left: 40},
                    curveType: "function",
                    hAxis: {showTextEvery: 200},
                    legend: {alignment: "left", position: "right"},
                    width: ($('body').width() - 20),
                    height: 400

                  });
          }
          else {
            noCharts = true;
          }
      });

}

function drawNeighborhoodBarChart(chartTitle, set, neighborhood, start, end) {

  var colorSet;

  $.getJSON('api.php?set='+set+'&neighborhood='+neighborhood+'&start='+start+'&end='+end, function(data) {

    var chartData = google.visualization.arrayToDataTable(data);
    var showEvery = parseInt(chartData.getNumberOfRows() / 200);      
      if (data[1].length == 3) {
        colorSet = ["#2A58A8", "#95B7C7"];
      }
      else {
        colorSet = ["#2A58A8", "#0089C7", "#95B7C7", "#E0E0E0"]
      }

      if(typeof data[1] != 'undefined') {

  new google.visualization.BarChart(document.getElementById(set + '_bar')).
      draw(chartData,
           {title:chartTitle,
            legend: {alignment: "left", position: "right"},
            colors: colorSet,
            width: ($('body').width() - 20),
            height: 400,
            hAxis: {showTextEvery: 200},
            chartArea: {left: 40},
            isStacked: true}
      )
    }
    else {
      noCharts = true;
    }
  });
}


function drawFrequencyLineChart(end, start) {
  $.getJSON('api.php?set=frequency&start='+start+'&end='+end, function(data) {
    var chartData = google.visualization.arrayToDataTable(data);
    var showEvery = parseInt(chartData.getNumberOfRows() / 10);      

      if(typeof data[1] != 'undefined') {

        new google.visualization.LineChart(document.getElementById('overview_line')).
            draw(chartData, {
                    title:"SMS Frequency by Neighborhood",
                    chartArea: {left: 40},
                    curveType: "function",
                    legend: {alignment: "left", position: "right"},
                    width: ($('body').width() - 20),
                    height: 400,
                    hAxis: {showTextEvery: 4}

                  });
          }
          else {
            noCharts = true;
          }
      });

}
 

function loadEverything() {
    populateAllCharts();
    $('#chartTabsAll a[href="#overview"]').tab('show');

    $.getJSON("api.php?set=neighborhoods", function (data) {
             $.each(data, function (i, data) {
                 var jsondata = "<li><a href='#' onclick='switchToMapWithLocation("+data.id+")''>" + data.location + "</a></li>";
                 $(jsondata).appendTo("ul.neighborhoods");
             });
      });
  set_date("neighborhood", "1 Year");
  set_date("all", "1 Year");

  $('#dpStart').datepicker();
  $('#dpEnd').datepicker();
  $('#dpStart2').datepicker();
  $('#dpEnd2').datepicker();
  $('.dateRangeSelector').click(function() {
    $('.dateRangeSelector').removeClass('active');
    $(this).addClass("active");
    set_date("neighborhood", $(this).children("a").html())
    set_date("all", $(this).children("a").html())
    populateAllCharts();
    populateCharts();
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
  $('li#all_link').addClass("active");
  $('li#neighborhood_link').removeClass("active");
  $('#view_neighborhood').hide();
  $('#view_all').show();
}

function switchToMapWithLocation(location) {
  $('li#all_link').removeClass("active");
  $('#view_all').hide();
  $('#view_neighborhood').show();
  $('#contentMap').html("");
  initMap();
  showNeighborhood(location);
}

function showNeighborhood(location) {
  
  $.getJSON('api.php?set=location&neighborhood='+location, function(data) {
      $("#neighborhood").html(data.name + "<span class='label pull-right'>As of "+moment(data.collection_start, "YYYY-MM-DD").fromNow()+"</span>");
      $("#reports").html(data.reports);
      $("#customers").html(data.customers);
      $("#infoBox").show();
      $('a#active_neighborhood').html(data.name + " <b class='caret'></b>");
      $("#index_rating").css('width', Math.round(Math.random() * 100));
      $("#index_value").html(Math.round(Math.random() * 100));

  });


  loc = location;
  populateCharts();


}

function populateAllCharts() {
  var endDate = $('#dpEnd2').val();
  var startDate = $('#dpStart2').val();
  drawFrequencyLineChart(endDate, startDate);
}

function populateCharts() {
  // Create and populate the data table.
  var endDate = $('#dpEnd').val();
  var startDate = $('#dpStart').val();

  drawNeighborhoodLineChart("Amount of Responses Over Time", "water", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Line Chart", "taste", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Line Chart", "particles", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Line Chart", "flow", loc, startDate, endDate); 
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
/*      $.getJSON('api.php?set=location&neighborhood='+evt.feature.attributes.location.value, function(data) {
          $("#neighborhood").html(evt.feature.attributes.Name.value + "<span class='label pull-right'>As of "+moment(data.collection_start, "YYYY-MM-DD").fromNow()+"</span>");
          $("#reports").html(data.reports);
          $("#customers").html(data.customers);
          $("#infoBox").show();
      });

      $("#index_rating").css('width', Math.round(Math.random() * 100));
      loc = evt.feature.attributes.location.value;
      populateCharts();
  */    
  }
  
  function draw_event(evt)
    {
      var a = evt.feature.attributes;
      a.District = a.Name.value;
      return true;
    }
