<script type="text/javascript">

/*
 * Global Vars
 */
var map;
var osm;
var Layers = [];
var select;

var global_url = "<?php echo Request::root(); ?>/";


/*
 * Google Cards ? 
 */
function map_google_init ()
{
<?php
        if (1)
        {
                echo "
                var gphy = new OpenLayers.Layer.Google(
                        \"Google Physical\",
                        {type: google.maps.MapTypeId.TERRAIN}
                        // used to be {type: G_PHYSICAL_MAP}
                );
                var gmap = new OpenLayers.Layer.Google(
                        \"Google Streets\", // the default
                        {numZoomLevels: 20}
                        // default type, no change needed here
                );
                var ghyb = new OpenLayers.Layer.Google(
                        \"Google Hybrid\",
                        {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
                        // used to be {type: G_HYBRID_MAP, numZoomLevels: 20}
                );
                var gsat = new OpenLayers.Layer.Google(
                        \"Google Satellite\",
                        {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
                        // used to be {type: G_SATELLITE_MAP, numZoomLevels: 22}
                );

                map.addLayers([osm,gsat,gmap,gphy,ghyb]);
                ";
        }
        else
                echo "          map.addLayers([osm]);";

?>
}


/*
 * Modem Positioning System
 *  Rectangle Views
 */
function map_mps_init()
{
<?php
if (0) 
{
	$cluster = def_kml;
	include_once ('include.php');
	__include('mysql.php');
	_mysql_connect();

	echo "\t\t\t\tvar boxes  = new OpenLayers.Layer.Boxes( \"Modem Positioning Rules\", {projection: map.displayProjection} );";

	$i = 0;
	$extract = mysql_query("SELECT val,id FROM mps WHERE prio >= 0 AND kind='position' AND parent IN (SELECT id FROM tree WHERE cluster = $cluster) ORDER BY prio");
	while ($row = mysql_fetch_assoc($extract))
	{
		$i++;

		$val = trim ($row['val']); # Entferne führende und abschliesende Leerzeichen
		$arr = explode (' ', $val);
		$x1  = $arr[2] / 10000000;
		$x2  = str_replace (")", "", $arr[6]) / 10000000;
		$y1  = $arr[10] / 10000000;
		$y2  = str_replace (")", "", $arr[14]) / 10000000;
		if ($x1 != '>')
			$pos = "$x1, $y1, $x2, $y2";

		echo "
			var bounds = new OpenLayers.Bounds($pos);
			box$i = new OpenLayers.Marker.Box(bounds.transform(new OpenLayers.Projection(\"EPSG:4326\"),map.getProjectionObject()));
			box$i.events.register(\"click\", box$i, function (e) {
				alert (\"Modem Positioning System\", 
				       '<li><a target=".global_others_target." href=../customer/mps.php?mp_sys_operation=mp_op_Change&mp_sys_rec=".$row['id']."/>Show Rule</a></li>' + 
				       '<li><a target=".global_others_target." href=../customer/mps.php?mp_sys_operation=mp_op_Delete&mp_sys_rec=".$row['id']."/>Delete Rule</a></li>',
				       {width:150});
			});
			box$i.setBorder(\"brown\");
			boxes.addMarker(box$i); ";
	}
	echo "\n\n\t\t\tmap.addLayers([boxes]);\n";
}
?>
}


/*
 * Handle all Mouse Clk Stuff here
 */
function clk_init_1()
{
        /*
         * Handle Left Click on KML Element
         */
        select = new OpenLayers.Control.SelectFeature(Layers[0]);
        Layers[0].events.on({
                "featureselected": onFeatureSelect
        });
        map.addControl(select);
        select.activate();   

	function onFeatureSelect(event) {
		var feature = event.feature;
		var theDomEvent = select.handlers.feature.evt;

		// Since KML is user-generated, do naive protection against
		// Javascript.
		var descr = feature.attributes.description;

		if (theDomEvent.shiftKey == OpenLayers.Handler.MOD_SHIFT)
		{
			alert ("shift key: "+descr);
			return;
		}

		if (!descr.contains('='))
			// window.open("mapdia.header.php?kml="+descr, "_blank",
			// "directories=no, status= no, fullscreen=no, location=no, menubar=no, resizeable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no, left=50, top=50, width=300, height=300");
			window.open(global_url + "Tree/erd/pos/"+descr, "_bank");
		else
		{
			var lines = descr.split("<br>").length;
			var height = 0;
		
			if (lines > 15)
				height = 400;

			alert ("Modem Positioning System", descr, {width:300});
		}
		select.unselectAll(); // Skip UnSelect option, allows multiple clicks to one element
	}
}

function clk_init_2()
{

	/*
	 * Disable Default right-click event:
	 */
	document.getElementById('map').oncontextmenu = function(e){
		e = e?e:window.event;
		if (e.preventDefault) e.preventDefault(); // For non-IE browsers.
		else return false; // For IE browsers.
	};


	/*
	 * Control class for capturing card click events...
	 */
	OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
		defaultHandlerOptions: {
			'single': true,
			'double': true,
			'pixelTolerance': 0,
			'stopSingle': false,
			'stopDouble': false
		},
		handleRightClicks:true,
		initialize: function(options) {
			this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
			OpenLayers.Control.prototype.initialize.apply(this, arguments); 
			this.handler = new OpenLayers.Handler.Click(this, this.eventMethods, this.handlerOptions );
		},
		CLASS_NAME: "OpenLayers.Control.Click"
	});

	// Add an instance of the Click control that listens to various click events:
	var oClick = new OpenLayers.Control.Click({eventMethods:{
		'rightclick': function(e) {
			var ll = map.getLonLatFromPixel(e.xy); 
			ll.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
			var lon = ll.lon.toFixed(6);
			var lat = ll.lat.toFixed(6);
			var tree = "tree.add.php";
			var kml = '<?php $kml = isset ($_GET['kml']) ? $_GET['kml'] : ''; echo $kml ?>';
			var pos = lon + ',' + lat;
			// populate yor box/field with lat, lng
			alert('Add Network Element',
			  'Geoposition: ' + pos +
			  '<br><br><a href="' + tree + '?pos=' + pos + '&kml=' + kml + '">Add Device</a>' + 
			  '<br><a href="tree.fastadd.php?pos=' + pos + '&kml=' + kml + '">Add Device (fast method)</a>' 
			);
		},
		'click': function(e) {},
		'dblclick': function(e) {},
		'dblrightclick': function(e) {},
	}});
	map.addControl(oClick);
	oClick.activate();


	/*
	 * Rectangle
	 */
		var control = new OpenLayers.Control();
                OpenLayers.Util.extend(control, {
                    draw: function () {
                        // this Handler.Box will intercept the shift-mousedown
                        // before Control.MouseDefault gets to see it
                        this.box = new OpenLayers.Handler.Box( control,
                            {"done": this.notice},
                            {keyMask: OpenLayers.Handler.MOD_SHIFT});
                        this.box.activate();
                    },

                    notice: function (bounds) {
                        var ll = map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.left, bounds.bottom)); 
                        var ur = map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.right, bounds.top)); 
			ll.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
			ur.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
                        // var maxLat = ur.lat.toFixed(4);

			var lat1 = ll.lat.toFixed(4);
			var lat2 = ur.lat.toFixed(4);
			var minLat = lat1<lat2?lat1:lat2;
			var maxLat = lat1<lat2?lat2:lat1;
			var lng1 = ll.lon.toFixed(4);
			var lng2 = ur.lon.toFixed(4);
			var minLng = lng1<lng2?lng1:lng2;
			var maxLng = lng1<lng2?lng2:lng1;
			var x1 = Math.round(minLng * 10000000); 
			var x2 = Math.round(maxLng * 10000000); 
			var y1 = Math.round(minLat * 10000000); 
			var y2 = Math.round(maxLat * 10000000); 

			alert('Modem Positioning System',
			      'Lat: ' + minLat + ' to ' + maxLat+ '<br>Lng: ' + minLng + ' to ' + maxLng + '<br><br>' +  
			      '<li><a target="_bank" href="'+ global_url + 'CustomerRect/' + minLng + '/' + maxLng + '/' + minLat + '/' + maxLat + '">Show Customer in Rectangle</a><br>' + 
			      '</li><li><a target="_bank" href="../customer/mps.php?mp_sys_operation=mp_op_Add&rect=(x > ' + x1 + ' AND x < ' + x2 + ') AND (y > ' + y1 + ' AND y < ' +y2 + ')">Add Modem Positioning Rule</a>' +
			      '</li><br>(x > ' + x1 + ' AND x <  ' + x2 + ') AND (y > ' + y1 + ' AND y < ' + y2 + ')', {width:500} );

                    }
                });
		map.addControl(control);

}


/*
 * Load KML Stuff
 */
function load (id, link, name)
{
       var styleMap = new OpenLayers.StyleMap({
            strokeColor: "#FFCC33",
            strokeWidth: 6,
            strokeOpacity: 1,
            fillColor: "#003399",
            fillOpacity: 0,
            label: "${name}",
	    labelYOffset: 27,
            fontSize: "25px",
	    fontColor: 'grey'
        });

        Layers[id] = new OpenLayers.Layer.Vector(name, {
		styleMap: styleMap,
                projection: map.displayProjection,
                strategies: [new OpenLayers.Strategy.Fixed()],
                protocol: new OpenLayers.Protocol.HTTP({
                        url: link,
                        format: new OpenLayers.Format.KML({
                                extractStyles: true,
                                extractAttributes: true
                        })
                })
        });

        map.addLayers([Layers[id]]);
        // auto zoom kml Layer
        Layers[0].events.register('loadend', Layers[0], function(evt){map.zoomToExtent(Layers[0].getDataExtent())});
}


function map_kml_load ()
{

	load(0, "{{asset($file)}}", "Infrastructure");

<?php

	if (0)
	{
		#
		# Load static KML files
		#
		include_once ('include.php');
		__include('mysql.php');
		_mysql_connect();

		$id = $_GET['kml'];
		$ext = mysql_query("SELECT id, kml_file, name FROM tree WHERE cluster = $id OR id = $id AND deleted = 0");
		$i = 1;

		while ($row = mysql_fetch_assoc($ext))
		{
			$id = $row['id'];
			$name = $row['name'];
			if ($row['kml_file'] != '')
				echo "\n\tload(1, \"static/$id.kml\", \"Details: $name\");\n\n";
		}
	}

?>
}


function map_kml_customer_load ()
{
<?php

	if(0)
	{
		$filename = def_filename;
		echo "\n\tload(0, \"$filename.kml\", \"Customer\");\n\n";

		include_once ('include.php');
		__include('mysql.php');
		_mysql_connect();

		$id = def_kml;
		$ext = mysql_query("SELECT kml_file, id FROM tree WHERE id IN (SELECT cluster FROM tree WHERE id = $id)");
		$row = mysql_fetch_assoc($ext);

		$id = $row['id'];

		if ($row['kml_file'] != '')
			echo "\n\tload(1, \"../../base/kml/static/$id.kml\", \"Detailed Infrastructure\");\n\n";
	}

?>
}



/*
 * MAP API Init
 */
function map_init() 
{
	// MAP
	map = new OpenLayers.Map('map',
	{
		    controls: [
			new OpenLayers.Control.Navigation(),
			new OpenLayers.Control.PanZoomBar(),
			new OpenLayers.Control.LayerSwitcher(),
		    ],
	});

	// Cards
	osm = new OpenLayers.Layer.OSM("Open Street Map");
	map_google_init();

	// Center / Zoom: 
	// TODO: use global define for default pos
	map.setCenter(
		new OpenLayers.LonLat(
			13.17,50.65
		).transform(
			new OpenLayers.Projection("EPSG:4326"),
			map.getProjectionObject()
		), 12
	);


	var bounds = new OpenLayers.Bounds(); 

<?php

	if(0)
	{
		$zoom = isset($_GET['zoom']) ? $_GET['zoom'] : '';
		$pos  = isset($_GET['pos']) ? $_GET['pos'] : '';
		$a  = split(",", $zoom);
		$x1 = $a[1];
		$x2 = $a[3];
		$y1 = $a[0];
		$y2 = $a[2];

		if ($pos)
		{
			echo "    
				var markers = new OpenLayers.Layer.Markers( \"Markers\" );
	    			map.addLayer(markers);
				markers.addMarker(new OpenLayers.Marker(map.getCenter()));";
		}
	 

		if ($zoom)
		{
			echo "\n\tbounds.extend(new OpenLayers.LonLat($x1, $y1).transform(new OpenLayers.Projection(\"EPSG:4326\"), map.getProjectionObject()));";
			echo "\n\tbounds.extend(new OpenLayers.LonLat($x2, $y2).transform(new OpenLayers.Projection(\"EPSG:4326\"), map.getProjectionObject()));";
			echo "\n\tmap.zoomToExtent(bounds);\n";
		}
	}

?>
}


/*
 * MAIN
 */
function init_for_map ()
{
	map_init();
	map_kml_load();
	map_mps_init();
	clk_init_1();
	clk_init_2();
}

function init_for_search()
{
	map_init();
	clk_init_2();
}

function init_for_customer ()
{
	map_init();
	map_kml_customer_load();
	clk_init_1();
	clk_init_2();
}


</script>
