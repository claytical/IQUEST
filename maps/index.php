<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>:: iQuest</title>

<link rel="stylesheet"href="css/iquest.css" type="text/css" />
<script type='text/javascript' src='OpenLayers.js'></script>
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script src="http://maps.google.com/maps/api/js?sensor=false&v=3.2"></script>

<script type="text/javascript">
 var map, a, vector_style_2, Hazard;
 
 
 function init() {
	 
	
	 $("#contentMap").html('');
	 $("#contentMap").css('width', '100%');
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
		alert(evt.feature.attributes.Name.value );
	       
		
	}
	
	function draw_event(evt)
		{
			var a = evt.feature.attributes;
			a.District = a.Name.value;
			return true;
		}

</script>
</head>

<body onload='init();'>
<div id='contentMap'></div>
</body>
</html>