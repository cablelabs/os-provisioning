@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('modem.index', 'Modems') }}

@stop

@section('content_left')
	
	{{ Form::open(array('route' => array('modem.store', 0), 'method' => 'POST')) }}

		@include('modems.form', array ('modem' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop