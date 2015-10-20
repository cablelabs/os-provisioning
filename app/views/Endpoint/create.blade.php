@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Endpoint.index', 'Endpoints') }}

@stop

@section('content_left')

	<h2>Create Endpoints</h2>
	
	{{ Form::open(array('route' => array('Endpoint.store', 0), 'method' => 'POST')) }}

		@include('endpoints.form', array ('endpoint' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop