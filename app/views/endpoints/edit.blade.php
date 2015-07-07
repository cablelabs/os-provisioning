@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('modem.index', 'Modems') }} / 
		{{ HTML::linkRoute('modem.edit', 'Modem-'.$endpoint->modem->hostname, $endpoint->modem->id) }} / 
		{{ HTML::linkRoute('endpoint.edit', 'endpoint-'.$endpoint->hostname, array($endpoint->id)) }} /

@stop

@section('content_left')
	
	{{ Form::model($endpoint, array('route' => array('endpoint.update', $endpoint->id), 'method' => 'put')) }}

		@include('endpoints.form', $endpoint)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')


@stop