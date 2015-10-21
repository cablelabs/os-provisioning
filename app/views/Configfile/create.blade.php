@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Configfile.index', 'Configfiles') }}

@stop

@section('content_left')

	<h2>Create COnfigfile</h2>	

	{{ Form::open(array('route' => array('Configfile.store', 0), 'method' => 'POST')) }}

		@include('Configfile.form', array ('configfile' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop