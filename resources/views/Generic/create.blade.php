@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')

		{{ $link_header }}

		{{ \App\Http\Controllers\BaseController::translate('Create') }}

	@stop
@endif

@section('content_left')

	{{ Form::open(array('route' => array($route_name.'.store', 0), 'method' => 'POST', 'files' => true)) }}

		@include($form_path)

	{{ Form::close() }}

@stop
