@extends ('Layout.single')

<head>

	<link href="{{asset('/modules/hfcbase/alert.css')}}" rel="stylesheet" type="text/css" media="screen"/>
	<script type="text/javascript" src="{{asset('/modules/hfcbase/alert.js')}}"></script>

	<script src="{{asset('/modules/hfcbase/OpenLayers-2.13.1/OpenLayers.js')}}"></script>
	<script src="https://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>

	@include ('hfcbase::Tree.topo-api')

</head>



@section('content_top')
	{{ HTML::linkRoute('TreeTopo.show', $view_header, [$field, $search]) }}
@stop

@section('content_left')

	@include ('hfcbase::Tree.search')

	<div class="col-md-12" id="map" style="height: 80%;"></div>
@stop

