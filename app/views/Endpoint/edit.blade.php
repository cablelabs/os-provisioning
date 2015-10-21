@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Endpoint.index', 'Endpoints') }} /
		{{ HTML::linkRoute('Endpoint.edit', $endpoint->hostname, array($endpoint->id)) }}

@stop

@section('content_left')
	
	<h2>Edit Endpoints</h2>

	{{ Form::model($endpoint, array('route' => array('Endpoint.update', $endpoint->id), 'method' => 'put')) }}

		@include('Endpoint.form', $endpoint)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')


@stop
