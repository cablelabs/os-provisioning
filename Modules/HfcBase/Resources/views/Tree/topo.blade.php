@extends ('Layout.single')

<head>

	<meta http-equiv="Pragma" content="no-cache">
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<link href="{{asset('/modules/Hfcbase/alert.css')}}" rel="stylesheet" type="text/css" media="screen"/>
	<script type="text/javascript" src="{{asset('/modules/Hfcbase/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('/modules/Hfcbase/alert.js')}}"></script>

	<link rel="stylesheet" href="{{asset('/modules/Hfcbase/OpenLayers-2.13.1/style.css')}}" type="text/css">
	<link rel="stylesheet" href="{{asset('/modules/Hfcbase/OpenLayers-2.13.1/theme/default/style.css')}}" type="text/css">
	<script src="{{asset('/modules/Hfcbase/OpenLayers-2.13.1/OpenLayers.js')}}"></script>

	<?php
	#
	# Include Google ?
	# TODO: use generic global define or sql config page
	#
	if (1)
	        echo "<script src=\"https://maps.google.com/maps/api/js?v=3.2&sensor=false\"></script>";
	?>

	@include ('hfcbase::Tree.topo-api')

</head>

@section('content_left')
	<div class="col-md-12" id="map" style="height: 80%;"></div>
@stop

