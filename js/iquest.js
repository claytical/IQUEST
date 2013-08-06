 var map, a, vector_style_2, Hazard;
 var loc;

function drawNeighborhoodLineChart(chartTitle, set, neighborhood, start, end) {
  $.getJSON('api.php?set='+set+'&neighborhood='+neighborhood+'&start='+start+'&end='+end, function(data) {
    var chartData = google.visualization.arrayToDataTable(data);
    new google.visualization.LineChart(document.getElementById(set + '_line')).
        draw(chartData, {
                title:chartTitle,
                chartArea: {left: 0},
                curveType: "function",
                legend: {alignment: "left", position: "right"},
                width: $('body').width() - 20,
                height: 400,
                vAxis: {title: "Date"},
                hAxis: {title: "Percentage"},

              });
      });

}

function drawNeighborhoodBarChart(chartTitle, set, neighborhood, start, end) {
  $.getJSON('api.php?set='+set+'&neighborhood='+neighborhood+'&start='+start+'&end='+end, function(data) {
    var chartData = google.visualization.arrayToDataTable(data);

  new google.visualization.BarChart(document.getElementById(set + '_bar')).
      draw(chartData,
           {title:chartTitle,
            legend: {alignment: "left", position: "right"},
            colors: ["green", "#dddddd"],
            width: $('body').width() - 20,
            height: 400,
            chartArea: {left: 0},
            vAxis: {title: "Date"},
            hAxis: {title: "Percentage"},
            isStacked: true}
      )
    });
}

function loadEverything() {
  set_date("1 Year");
  $('#dpStart').datepicker();
  $('#dpEnd').datepicker();
  $('.dateRangeSelector').click(function() {
    $('.dateRangeSelector').removeClass('active');
    $(this).addClass("active");
    set_date($(this).children("a").html())
  });
  initMap();
}

function set_date(range) {
  var today = moment();
  var tmpDate;
  var endDate = $('#dpEnd');
  var startDate = $('#dpStart');
  endDate.val(today.format("YYYY-MM-DD"));
  
  $('#custom_date').hide();
  switch(range) {
    case 'Custom':
      $('#custom_date').show();
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

function populateCharts() {
  // Create and populate the data table.
  var endDate = $('#dpEnd').val();
  var startDate = $('#dpStart').val();

  drawNeighborhoodLineChart("Water Color", "water", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Taste / Odor", "taste", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Particles", "particles", loc, startDate, endDate); 
  drawNeighborhoodLineChart("Hours of Flow", "flow", loc, startDate, endDate); 
  drawNeighborhoodBarChart("Water Color", "water", loc, startDate, endDate); 
  drawNeighborhoodBarChart("Taste / Odor", "taste", loc, startDate, endDate); 
  drawNeighborhoodBarChart("Particles", "particles", loc, startDate, endDate); 
  drawNeighborhoodBarChart("Hours of Flow", "flow", loc, startDate, endDate); 

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
      
      var layControl =  new OpenLayers.Control.LayerSwitcher({});
      map.addControl(layControl);
  /*
      var hover_feature_control = new OpenLayers.Control.SelectFeature(Hazard, {
        hover: true,
        renderIntent: "temporary",
        eventListeners: {
            featurehighlighted: hover_event,
            featureunhighlighted: unhover_event            
        }
      });
*/

      var select_feature_control = new OpenLayers.Control.SelectFeature(Hazard);
      map.addControl(select_feature_control);
     // map.addControl(hover_feature_control);
      select_feature_control.activate();
//      hover_feature_control.activate();
           
  var point = new OpenLayers.LonLat(-23070.48765315,617902.95096767); 
    map.setCenter(point, 14); 
     
        if(!map.getCenter()){
           map.zoomToMaxExtent();
        }

    }
  function hover_event(evt) {
  }

  function unhover_event(evt) {
  }

  function click_event(evt)
  {
      $.getJSON('api.php?set=location&neighborhood='+evt.feature.attributes.location.value, function(data) {
          $("#neighborhood").html(evt.feature.attributes.Name.value);
          $("#reports").html(data.reports);
          $("#customers").html(data.customers);
      });

    $("#index_rating").css('width', Math.round(Math.random() * 100));
    loc = evt.feature.attributes.location.value;
    populateCharts();
    
  }
  
  function draw_event(evt)
    {
      var a = evt.feature.attributes;
      a.District = a.Name.value;
      return true;
    }
