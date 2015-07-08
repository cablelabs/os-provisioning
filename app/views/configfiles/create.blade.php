@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('configfile.index', 'Configfiles') }}

@stop

@section('content_left')
	
	{{ Form::open(array('route' => array('configfile.store', 0), 'method' => 'POST')) }}

		@include('configfiles.form', array ('configfile' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop