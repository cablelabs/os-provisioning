<script type="text/javascript">

/*
 * Global Vars
 */
var map;
var osm;
var Layers = [];
var select;

var global_url = '{{BaseRoute::get_base_url()}}/';


/*
 * Google Cards
 */
function map_google_init ()
{
    var gphy = new OpenLayers.Layer.Google( "Google Physical",	{type: google.maps.MapTypeId.TERRAIN});
    var gmap = new OpenLayers.Layer.Google( "Google Streets",	{numZoomLevels: 20});
    var ghyb = new OpenLayers.Layer.Google( "Google Hybrid",	{type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20});
    var gsat = new OpenLayers.Layer.Google( "Google Satellite",	{type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22});

	vectors = new OpenLayers.Layer.Vector("Modem Positioning Rules", {projection: map.displayProjection});

	map.addLayers([osm,gsat,gmap,gphy,ghyb,vectors]);

	vectors.events.on({ "afterfeaturemodified" : onAfterFeatureModified });

	drawControls = 	{ polygon 	: new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.Polygon,{ callbacks : { "done": savePolygonMPR} }),
					  box		: new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.RegularPolygon, {
									handlerOptions: { sides: 4, irregular: true },
									callbacks : { "done": savePolygonMPR}
									}),
					  modify	: new OpenLayers.Control.ModifyFeature(vectors)
					};

	for(var key in drawControls)
		map.addControl(drawControls[key]);

	document.getElementById('noneToggle').checked = true;
}

/*
 * Modem Positioning System
 *  Rectangle Views
 */
function map_mps_init()
{
@if(isset($mpr))
	@foreach ($mpr as $id => $corners)
		@if (count($mpr) == 2)
			bounds = new OpenLayers.Bounds();

			@foreach($corners as $coord)
				bounds.extend(new OpenLayers.LonLat({{ $coord[0] }}, {{ $coord[1] }} ));
			@endforeach

			bounds.transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());
			vectors.addFeatures([new OpenLayers.Feature.Vector(bounds.toGeometry(), {id: {{ $id }} })]);
		@elseif (count($mpr) > 2)
			vectors.addFeatures([
			new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([
			new OpenLayers.Geometry.LinearRing([

			@foreach ($corners as $coord)
				new OpenLayers.Geometry.Point({{ $coord[0] }}, {{ $coord[1] }} ),
			@endforeach

			]).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject())]), {id: {{ $id }} })]);
		@endif
	@endforeach
@endif
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

		var coord = /^-?\d+\.\d+,-?\d+\.\d+$/;
		// descr matches a geo-coordinate (-)xx.xxx,(-)xx.xxx
		if (coord.test(descr))
			// window.open("mapdia.header.php?kml="+descr, "_blank",
			// "directories=no, status= no, fullscreen=no, location=no, menubar=no, resizeable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no, left=50, top=50, width=300, height=300");
			window.open(global_url + "Tree/erd/pos/"+descr, "_bank");
		else
		{
			var lines = descr.split("<br>").length;
			var height = 0;

			if (lines > 15)
				height = 400;

			alert ("Modem Positioning System", descr, {left:400, top:300, width:document.width-50, height:height>0?height:document.height-50});
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
			var tree = '<?php switch(\NamespaceController::get_route_name()) { case 'CustomerTopo': $r='Modem'; break; case 'TreeTopography': $r='NetElement'; break; } echo(route($r.'.create')) ?>';
			var kml = '<?php $kml = isset ($_GET['kml']) ? $_GET['kml'] : ''; echo $kml ?>';
			var pos = lon + ',' + lat;
			// populate yor box/field with lat, lng
			alert('Add Network Element',
			  'Geoposition: ' + pos +
			  '<br><br><a href="' + tree + '?pos=' + pos + '&kml=' + kml + '">Add Device</a>'
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
				var x1 = minLng;
				var x2 = maxLng;
				var y1 = minLat;
				var y2 = maxLat;

				alert('Modem Positioning System',
				      'Lat: ' + minLat + ' to ' + maxLat+ '<br>Lng: ' + minLng + ' to ' + maxLng + '<br><br>' +
				      '<li><a href="'+ global_url + 'CustomerRect/' + minLng + '/' + maxLng + '/' + minLat + '/' + maxLat + '">Show Customer in Rectangle</a><br>' +
				      '</li><li><a href="' + global_url + 'Mpr/create?value=' + x1 + ';' + x2 + ';' + y1 + ';' +y2 + '">Add Modem Positioning Rule</a>' +
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

	@if (isset($kmls))
		@foreach ($kmls as $id => $kml)
			load({{$id+10}}, "{{asset($kml['file'])}}", "{{$kml['descr']}}");
		@endforeach
	@endif
}

function toggleControl(element) {
	for(key in drawControls) {
		var control = drawControls[key];
		if(element.value == key && element.checked)
			control.activate();
		else
			control.deactivate();
	}
	if(element.value == 'none')
		select.activate();
	else
		select.deactivate();
}

function getPolyStr(geo) {
	var str = '';
	var vertices = geo.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326")).getVertices();
	for (var i = 0; i < vertices.length; i++) {
		if(i) str += ';';
		str += vertices[i].x + ';' + vertices[i].y;
	}
	return str;
}

function savePolygonMPR(geo) {
	polystr = getPolyStr(geo);
	str = '<li><a href="' + global_url + 'CustomerPoly/' + polystr + '">Show Customer in Polygon</a></li>';
	str += '<li><a href="' + global_url + 'Mpr/create?value=' + polystr + '">Add Modem Positioning Rule</a></li>';
	alert('Modem Positioning System', str, {width:500});
}

function onAfterFeatureModified(event) {
	if (confirm ("Modify Polygon %id?".replace('%id', event.feature.attributes.id))) {
		str = getPolyStr(event.feature.geometry);
		<?php echo 'window.location = "' . route('Mpr.update_geopos', ['%id', '%str']) . "\".replace('%id', event.feature.attributes.id).replace('%str', str)"; ?>;
	} else {
		location.reload();
	}
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
