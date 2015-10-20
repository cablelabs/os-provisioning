@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Modem.index', 'Modems') }}

@stop

@section('content_left')
	
	{{ Form::open(array('route' => array('Modem.store', 0), 'method' => 'POST')) }}

		@include('Modem.form', array ('modem' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop