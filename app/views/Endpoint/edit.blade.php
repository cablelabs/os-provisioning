@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('endpoint.index', 'Endpoints') }} /
		{{ HTML::linkRoute('endpoint.edit', $endpoint->hostname, array($endpoint->id)) }}

@stop

@section('content_left')
	
	<h2>Edit Endpoints</h2>

	{{ Form::model($endpoint, array('route' => array('endpoint.update', $endpoint->id), 'method' => 'put')) }}

		@include('endpoints.form', $endpoint)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')


@stop