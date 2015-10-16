@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('cmtsdownstream.index', 'CMTS') }}

@stop

@section('content_left')

	<h2>Create CMTS</h2>
	
	{{ Form::open(array('route' => array('cmtsdownstream.store', 0), 'method' => 'POST')) }}

		@include('cmtsdownstream.form', array ('cmtsdownstream' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop