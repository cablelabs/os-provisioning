@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Device.index', 'SNMP MIB') }}

@stop

@section('content_left')

	<h2>Create SNMP MIB</h2>	

	{{ Form::open(array('route' => array('Device.store', 0), 'method' => 'POST')) }}

		@include('Device.form', array ('device' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop