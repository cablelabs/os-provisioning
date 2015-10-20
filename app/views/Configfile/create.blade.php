@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('configfile.index', 'Configfiles') }}

@stop

@section('content_left')

	<h2>Create COnfigfile</h2>	

	{{ Form::open(array('route' => array('configfile.store', 0), 'method' => 'POST')) }}

		@include('configfiles.form', array ('configfile' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop