@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('modem.index', 'Modems') }} / 
		{{ HTML::linkRoute('modem.edit', 'Modem-'.$_GET['modem_hostname'], $_GET['modem_id']) }}

@stop

@section('content_left')
	
	{{ Form::open(array('route' => array('endpoint.store', 0), 'method' => 'POST')) }}

		@include('endpoints.form', array ('endpoint' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop