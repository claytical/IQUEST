
// JavaScript Document
 var map, a, vector_style_2, Hazard;
 
 function first()
 {
	 
	 var a = document.getElementById('prd').value;
	 if (a==''|| a == null)
	 {
		 alert('Enter Product Type');
	 }
	 else
	 {
	 $.post('../searchProd.php', {prod:a}, function(data)
	 {
	
		 	if(data!=0)
			{
		 		var let = eval(data);
				var t ="<table width='100%' border='0' class='headerbox'>";
		   		t +="<tr class='bolden'>";
				t +="<td width='15%'>Category</td>";
				t +="<td width='85%'>Description</td>";
		  		t +="</tr>";
		  		var len = let.length;
		  			for (var m=0; m<len ; m++)
		  				{
								t +="<tr>";
								t +="<td><a href='javascript:void()';  onclick=second(/"+let[m][0]+"/)>"+let[m][0] +"</a></td>";
								t +="<td>"+let[m][1] +"</td>";
		  						t +="</tr>";
		  				}
					t +="</table>";
					$("#prodContent").html(t)
			}
			else
			{
				alert('Product Category Not Found');
			}
		 });
	 }

 }
 
 function second(par)
 {
	 var me = par;
	var n = me.toString();
	
	var len = n.length;
	
	var num = len -2;
	
	var h = n.substr(1, num);
	
	 $.post('searchComp.php', {prod:h}, function(data)
	 {
	
		 	if(data!=0)
			{
		 		var let = eval(data);
				var t ="<table width='100%' border='0' class='headerbox'>";
		   		t +="<tr class='bolden'>";
				t +="<td width='20%'>GEPA ID</td>";
				t +="<td width='80%'>Company</td>";
		  		t +="</tr>";
		  		var len = let.length;
		  			for (var m=0; m<len ; m++)
		  				{
								t +="<tr>";
								t +="<td><a href='javascript:void()';  onclick=setIt(/"+let[m][0]+"/)>"+let[m][0] +"</a></td>";
								t +="<td>"+let[m][3] +"</td>";
		  						t +="</tr>";
		  				}
					t +="</table>";
					$("#compContent").html(t)
			}
			else
			{
				alert('Product Category Not Found');
			}
		 });
	 }



 
 

 
	
	function eat()
	{
		alert('gahan');
	}
	
function setIt(param)
	{
		
		 var me = param;
			var n = me.toString();
			var len = n.length;
			var num = len -2;
			var h = n.substr(1, num);
		
		$.post('search.php', {id:h} , function(data)
		{
			
			if (data!=0)
			{
				var n = eval(data);
				var point = new OpenLayers.LonLat(parseFloat(n[0][1]), parseFloat(n[0][2])); 
       			point.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913")); 
		 		var rule_ = new OpenLayers.Rule({
			 	filter: new OpenLayers.Filter.Comparison({
				 type: OpenLayers.Filter.Comparison.EQUAL_TO, 
				 property : 'name',
				 value : n[0][0]
				 }), 
			 symbolizer: {
				 fillColor: '#787878', fillOpacity:.8,pointRadius:5, strokeColor: '#454545',strokeWidth:2, label: n[0][0], labelAlign : 'tr'
			 }
		 });
		 
		 var vector_style_ = new OpenLayers.Style();
		 vector_style_.addRules([rule_]);
		 var vector_style_map = new OpenLayers.StyleMap({ 'default': vector_style_, 'select': vector_style_ });
		 map.layers[1].styleMap  = vector_style_map;
		 map.setCenter(point, 15);
			}
			else
			{
			}
		});
	}