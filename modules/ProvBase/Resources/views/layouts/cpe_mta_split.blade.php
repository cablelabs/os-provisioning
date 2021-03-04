@extends ('Layout.split-nopanel')

@section('content_top')

	@include ('provbase::layouts.top', ['type' => $type])

@stop

@section ('content_left')

<div class="row">

	{{-- We need to include sections dynamically: always content left and if needed content right - more than 1 time possible --}}

	<div class="col-md-7 ui-sortable">
		@include ('bootstrap.panel', array ('content' => 'content_dash', 'view_header' => 'Dashboard / Forecast', 'i' => 1, 'height' => 'auto'))
		@include ('bootstrap.panel', array ('content' => 'content_log', 'view_header' => 'DHCP Log', 'i' => 2, 'height' => 'auto'))
	</div>

	<div class="col-md-5 ui-sortable">
		@include ('bootstrap.panel', array ('content' => 'content_ping', 'view_header' => 'Ping Test', 'i' => 3, 'height' => 'auto'))
		@include ('bootstrap.panel', array ('content' => 'content_lease', 'view_header' => 'DHCP Lease', 'i' => 4, 'height' => 'auto'))
		@if (isset($configfile))
			@include ('bootstrap.panel', array ('content' => 'content_configfile', 'view_header' => 'Configfile', 'i' => 5, 'height' => 'auto'))
		@endif
</div>


</div>

@stop
