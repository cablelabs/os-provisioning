@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Qos.index', 'qualitys') }}

@stop

@section('content_left')

	<h2>Create quality</h2>	

	{{ Form::open(array('route' => array('Qos.store', 0), 'method' => 'POST')) }}

		@include('Qos.form', array ('quality' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop