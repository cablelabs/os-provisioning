@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('CmtsDownstream.index', 'CMTS') }}

@stop

@section('content_left')

	<h2>Create CMTS</h2>
	
	{{ Form::open(array('route' => array('CmtsDownstream.store', 0), 'method' => 'POST')) }}

		@include('CmtsDownstream.form', array ('cmtsdownstream' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop