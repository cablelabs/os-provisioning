
@if (isset($file))

	<head>

		<link href="{{asset('/modules/Hfcbase/alert.css')}}" rel="stylesheet" type="text/css" media="screen"/>
		<script type="text/javascript" src="{{asset('/modules/Hfcbase/alert.js')}}"></script>

		<script src="{{asset('/modules/Hfcbase/OpenLayers-2.13.1/OpenLayers.js')}}"></script>
		<script src="https://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>

		@include ('hfcbase::Tree.topo-api')

	</head>

	@section ('content_right')
		<div class="col-md-12" id="map" style="height: 50%;"></div>
	@stop

@endif

@extends ('Generic.index')
